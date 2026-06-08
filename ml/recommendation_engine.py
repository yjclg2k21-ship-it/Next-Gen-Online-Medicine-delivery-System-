"""
ML Recommendation Engine for MediMitra
Uses Collaborative Filtering + Content-Based Filtering

- Collaborative: Users who bought X also bought Y (matrix factorization)
- Content-Based: Medicine similarity by salt, category, price range

Endpoint: POST http://localhost:5050/recommend
"""

import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.preprocessing import LabelEncoder, MinMaxScaler
import mysql.connector

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'medicine_delivery'
}

# Global model state
rec_model = {
    'medicine_features': None,
    'medicine_ids': [],
    'medicines_data': [],
    'user_item_matrix': None,
    'user_ids': [],
    'trained': False
}


def get_db():
    return mysql.connector.connect(**DB_CONFIG)


def train_recommendation_model():
    """Train collaborative + content-based recommendation model"""
    global rec_model
    
    try:
        conn = get_db()
        cursor = conn.cursor(dictionary=True)
        
        # 1. Load all approved medicines with features
        cursor.execute("""
            SELECT m.id, m.name, m.salt, m.price, m.category_id, m.brand_id, m.vendor_id,
                   m.requires_prescription, c.name as category_name
            FROM medicines m
            LEFT JOIN categories c ON m.category_id = c.id
            WHERE m.deleted_at IS NULL AND m.approval_status = 'approved'
        """)
        medicines = cursor.fetchall()
        
        if not medicines:
            print("No medicines found for recommendation training")
            return False
        
        # 2. Build content-based feature matrix
        medicine_ids = [m['id'] for m in medicines]
        
        # Encode categorical features
        salt_encoder = LabelEncoder()
        category_encoder = LabelEncoder()
        
        salts = [m['salt'] or 'unknown' for m in medicines]
        categories = [str(m['category_id'] or 0) for m in medicines]
        
        salt_encoded = salt_encoder.fit_transform(salts)
        category_encoded = category_encoder.fit_transform(categories)
        
        # Normalize price
        prices = np.array([float(m['price'] or 0) for m in medicines]).reshape(-1, 1)
        scaler = MinMaxScaler()
        price_normalized = scaler.fit_transform(prices).flatten()
        
        # Build feature matrix: [salt_encoded, category_encoded, price_normalized, requires_rx]
        n_salts = len(salt_encoder.classes_)
        n_cats = len(category_encoder.classes_)
        
        # One-hot encode salt and category for better similarity
        feature_matrix = np.zeros((len(medicines), n_salts + n_cats + 2))
        for i, med in enumerate(medicines):
            feature_matrix[i, salt_encoded[i]] = 1.0  # Salt one-hot
            feature_matrix[i, n_salts + category_encoded[i]] = 1.0  # Category one-hot
            feature_matrix[i, n_salts + n_cats] = price_normalized[i]  # Price
            feature_matrix[i, n_salts + n_cats + 1] = float(med['requires_prescription'] or 0)
        
        # 3. Build user-item interaction matrix (collaborative filtering)
        cursor.execute("""
            SELECT o.user_id, oi.medicine_id, COUNT(*) as purchase_count
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status IN ('delivered', 'confirmed', 'dispatched', 'pending')
            GROUP BY o.user_id, oi.medicine_id
        """)
        interactions = cursor.fetchall()
        
        user_ids = list(set(i['user_id'] for i in interactions))
        
        # Create user-item matrix
        user_idx_map = {uid: idx for idx, uid in enumerate(user_ids)}
        med_idx_map = {mid: idx for idx, mid in enumerate(medicine_ids)}
        
        user_item_matrix = np.zeros((len(user_ids), len(medicine_ids)))
        for interaction in interactions:
            uid = interaction['user_id']
            mid = interaction['medicine_id']
            if uid in user_idx_map and mid in med_idx_map:
                user_item_matrix[user_idx_map[uid], med_idx_map[mid]] = interaction['purchase_count']
        
        cursor.close()
        conn.close()
        
        # Store model
        rec_model['medicine_features'] = feature_matrix
        rec_model['medicine_ids'] = medicine_ids
        rec_model['medicines_data'] = medicines
        rec_model['user_item_matrix'] = user_item_matrix
        rec_model['user_ids'] = user_ids
        rec_model['user_idx_map'] = user_idx_map
        rec_model['med_idx_map'] = med_idx_map
        rec_model['trained'] = True
        
        print(f"Recommendation model trained: {len(medicines)} medicines, {len(user_ids)} users, {len(interactions)} interactions")
        return True
        
    except Exception as e:
        print(f"Recommendation training error: {e}")
        return False


def get_ml_recommendations(user_id, limit=12):
    """Get ML-powered recommendations for a user"""
    global rec_model
    
    if not rec_model['trained']:
        return []
    
    medicine_ids = rec_model['medicine_ids']
    medicines_data = rec_model['medicines_data']
    feature_matrix = rec_model['medicine_features']
    user_item_matrix = rec_model['user_item_matrix']
    user_idx_map = rec_model['user_idx_map']
    med_idx_map = rec_model['med_idx_map']
    
    scores = np.zeros(len(medicine_ids))
    purchased_indices = []
    
    # --- Collaborative Filtering ---
    if user_id in user_idx_map:
        user_idx = user_idx_map[user_id]
        user_vector = user_item_matrix[user_idx]
        purchased_indices = np.where(user_vector > 0)[0].tolist()
        
        # Find similar users (cosine similarity on purchase patterns)
        if user_item_matrix.shape[0] > 1:
            user_similarities = cosine_similarity(user_vector.reshape(1, -1), user_item_matrix)[0]
            # Weighted sum of similar users' purchases
            for other_idx, sim in enumerate(user_similarities):
                if other_idx != user_idx and sim > 0.1:
                    scores += sim * user_item_matrix[other_idx]
    
    # --- Content-Based Filtering ---
    if purchased_indices:
        # Average feature vector of purchased items
        purchased_features = feature_matrix[purchased_indices]
        user_profile = np.mean(purchased_features, axis=0).reshape(1, -1)
        
        # Similarity of all medicines to user profile
        content_scores = cosine_similarity(user_profile, feature_matrix)[0]
        scores += content_scores * 2  # Weight content-based higher
    
    # Zero out already purchased items
    for idx in purchased_indices:
        scores[idx] = 0
    
    # Get top recommendations (deduplicate by name)
    top_indices = np.argsort(scores)[::-1]
    
    recommendations = []
    seen_names = set()
    for idx in top_indices:
        if scores[idx] <= 0:
            continue
        if len(recommendations) >= limit:
            break
        med = medicines_data[idx]
        name_key = med['name'].lower().strip()
        if name_key in seen_names:
            continue
        seen_names.add(name_key)
        recommendations.append({
            'id': med['id'],
            'name': med['name'],
            'salt': med['salt'] or '',
            'price': float(med['price'] or 0),
            'category_id': med['category_id'],
            'category_name': med.get('category_name', ''),
            'requires_prescription': med['requires_prescription'],
            'ml_score': round(float(scores[idx]), 3),
            'reason': 'ml_collaborative' if user_id in user_idx_map else 'ml_content_based'
        })
    
    return recommendations
