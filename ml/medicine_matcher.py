"""
Medicine Matcher ML Service
Uses TF-IDF + Cosine Similarity to match OCR text against medicine database.
Trains on medicine names, salts, and synonyms from MySQL database.

Run: python ml/medicine_matcher.py
Endpoint: POST http://localhost:5050/analyze
"""

import json
import re
import mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np

import sys
import os
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from recommendation_engine import train_recommendation_model, get_ml_recommendations, rec_model

app = Flask(__name__)
CORS(app)

# ─── CONFIG ────────────────────────────────────────────────────────────────────
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'medicine_delivery'
}

# ─── GLOBAL MODEL STATE ────────────────────────────────────────────────────────
model = {
    'vectorizer': None,
    'medicine_vectors': None,
    'medicines': [],
    'trained': False
}


def get_db_connection():
    """Connect to MySQL database"""
    return mysql.connector.connect(**DB_CONFIG)


def load_medicines():
    """Load all medicines from database"""
    try:
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        cursor.execute("""
            SELECT id, name, salt, price, stock, requires_prescription, category_id
            FROM medicines 
            WHERE deleted_at IS NULL AND approval_status = 'approved'
        """)
        medicines = cursor.fetchall()
        cursor.close()
        conn.close()
        return medicines
    except Exception as e:
        print(f"DB Error: {e}")
        # Fallback: try without approval_status filter
        try:
            conn = get_db_connection()
            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT id, name, salt, price, stock, requires_prescription FROM medicines WHERE deleted_at IS NULL")
            medicines = cursor.fetchall()
            cursor.close()
            conn.close()
            return medicines
        except Exception as e2:
            print(f"DB Fallback Error: {e2}")
            return []


def build_training_corpus(medicines):
    """
    Build training documents for each medicine.
    Includes name variations for better matching.
    """
    corpus = []
    for med in medicines:
        name = (med['name'] or '').lower()
        salt = (med['salt'] or '').lower()
        
        # Core name without dosage and form words
        name_no_dosage = re.sub(r'\d+\s*(mg|ml|mcg|g|iu|%)', '', name).strip()
        core = re.sub(r'\b(tablets?|capsules?|syrup|injection|ointment|cream|drops|suspension|gel|powder|solution)\b', '', name_no_dosage)
        core = re.sub(r'\s+', ' ', core).strip()
        
        # Build document with multiple representations
        parts = [name, name, name, name_no_dosage, core, core]
        if salt:
            parts.extend([salt, salt])
            # Also add "name + salt" combo
            parts.append(f"{core} {salt}")
        
        doc = ' '.join(parts)
        corpus.append(doc)
    
    return corpus


def train_model():
    """Train the TF-IDF model on medicine database"""
    global model
    
    print("Loading medicines from database...")
    medicines = load_medicines()
    
    if not medicines:
        print("WARNING: No medicines found in database!")
        model['trained'] = False
        return False
    
    print(f"Found {len(medicines)} medicines. Building corpus...")
    corpus = build_training_corpus(medicines)
    
    # Train TF-IDF vectorizer (word + subword for handling variations)
    vectorizer = TfidfVectorizer(
        analyzer='word',
        ngram_range=(1, 2),  # Unigrams and bigrams
        max_features=8000,
        lowercase=True,
        sublinear_tf=True,
        token_pattern=r'[a-zA-Z0-9]+'  # Include single chars and numbers (for "C", "B12")
    )
    
    medicine_vectors = vectorizer.fit_transform(corpus)
    
    model['vectorizer'] = vectorizer
    model['medicine_vectors'] = medicine_vectors
    model['medicines'] = medicines
    model['trained'] = True
    
    print(f"Model trained successfully on {len(medicines)} medicines!")
    return True


def extract_medicine_candidates(text):
    """
    Extract potential medicine name candidates from OCR text.
    Focuses on lines that look like medicine names, not addresses/headers.
    """
    text = text.strip()
    lines = text.split('\n')
    
    candidates = []
    
    # Patterns that indicate NON-medicine lines
    skip_patterns = [
        r'payment\s*receipt',
        r'transaction\s*id',
        r'ship\s*to',
        r'clinical\s*source',
        r'subtotal|gst|delivery fee|settled|total\s*payable',
        r'^\s*₹?\s*[\d,]+\.?\d*\s*$',  # Pure price/numbers
        r'authorized\s*payment',
        r'\b(january|february|march|april|may|june|july|august|september|october|november|december)\b',
        r'^\s*(qty|cost|category|pharmaceutical product)\s*$',
        r'^\s*(shop|sector|gst|apt|plaza|mumbai|maharashtra|phaltan)\b',
        r'^\s*\d{1,2}[:/]\d{2}',  # Time patterns
        r'txn_',  # Transaction IDs
        r'^\s*(antibiotic|supplement|analgesic|antipyretic|antifungal|antiviral)\s*$',  # Category-only lines
    ]
    
    # Patterns that indicate a medicine line
    medicine_indicators = [
        r'\d+\s*(mg|ml|mcg|g|iu|%)',  # Has dosage
        r'\b(tablet|capsule|syrup|drops|cream|ointment|injection|gel|powder|solution|complex|forte)\b',
    ]
    
    for line in lines:
        line = line.strip()
        if not line or len(line) < 4:
            continue
        
        # Skip non-medicine lines
        skip = False
        for pattern in skip_patterns:
            if re.search(pattern, line, re.IGNORECASE):
                skip = True
                break
        if skip:
            continue
        
        # Prioritize lines with medicine indicators
        is_medicine_line = any(re.search(p, line, re.IGNORECASE) for p in medicine_indicators)
        
        if is_medicine_line:
            # Clean: remove trailing numbers (qty, price) and category words
            clean_line = re.sub(r'\s+\d+\s*$', '', line)  # Remove trailing qty
            clean_line = re.sub(r'\s*₹?\s*[\d,]+\.?\d*\s*$', '', clean_line)  # Remove trailing price
            # Remove category words that are NOT part of medicine names
            clean_line = re.sub(r'\b(antibiotic|supplement|analgesic|antipyretic|antifungal|antiviral|pharmaceutical|product)\b', '', clean_line, flags=re.IGNORECASE)
            clean_line = re.sub(r'\s{2,}', ' ', clean_line).strip()
            if len(clean_line) >= 4:
                candidates.insert(0, clean_line)  # Priority
        
        # Add all non-skipped lines as lower priority candidates
        candidates.append(line)
    
    return candidates


def match_medicines(text, threshold=0.38):
    """
    Match OCR text against trained medicine model.
    Returns list of matched medicines with confidence scores.
    Deduplicates by medicine name (keeps highest score).
    """
    global model
    
    if not model['trained']:
        return []
    
    candidates = extract_medicine_candidates(text)
    
    matched = {}  # medicine_name_lower -> {medicine, score}
    
    for candidate in candidates:
        candidate_lower = candidate.lower().strip()
        if len(candidate_lower) < 3:
            continue
        
        # Transform candidate using trained vectorizer
        candidate_vector = model['vectorizer'].transform([candidate_lower])
        
        # Calculate similarity with all medicines
        similarities = cosine_similarity(candidate_vector, model['medicine_vectors'])[0]
        
        # Find matches above threshold
        for idx, score in enumerate(similarities):
            if score >= threshold:
                med = model['medicines'][idx]
                med_name_key = med['name'].lower().strip()
                
                if med_name_key not in matched or matched[med_name_key]['score'] < score:
                    matched[med_name_key] = {
                        'medicine': med,
                        'score': float(score),
                        'matched_text': candidate
                    }
    
    # Sort by score descending, limit to top 5
    results = sorted(matched.values(), key=lambda x: x['score'], reverse=True)
    return results[:5]


@app.route('/analyze', methods=['POST'])
def analyze_prescription():
    """Analyze prescription text and return matched medicines"""
    data = request.get_json()
    text = data.get('text', '')
    
    if not text:
        return jsonify({'success': False, 'message': 'No text provided'}), 400
    
    if not model['trained']:
        train_model()
    
    if not model['trained']:
        return jsonify({'success': False, 'message': 'Model not trained - database connection issue'}), 500
    
    matches = match_medicines(text)
    
    medicines = []
    for m in matches:
        med = m['medicine']
        medicines.append({
            'id': med['id'],
            'medicine_id': med['id'],
            'name': med['name'],
            'salt': med.get('salt', ''),
            'price': float(med.get('price', 0) or 0),
            'confidence': round(m['score'] * 100, 1),
            'matched_text': m['matched_text'],
            'requires_prescription': med.get('requires_prescription', 0)
        })
    
    return jsonify({
        'success': True,
        'medicines': medicines,
        'total_matches': len(medicines),
        'model_type': 'TF-IDF Cosine Similarity',
        'analysis_id': f'ML-{np.random.randint(100000, 999999)}'
    })


@app.route('/recommend', methods=['POST'])
def recommend():
    """Get ML-powered recommendations for a user"""
    data = request.get_json()
    user_id = data.get('user_id')
    limit = data.get('limit', 12)
    
    if not user_id:
        return jsonify({'success': False, 'message': 'user_id required'}), 400
    
    if not rec_model['trained']:
        train_recommendation_model()
    
    if not rec_model['trained']:
        return jsonify({'success': False, 'message': 'Recommendation model not trained'}), 500
    
    recommendations = get_ml_recommendations(int(user_id), int(limit))
    
    return jsonify({
        'success': True,
        'recommendations': recommendations,
        'total': len(recommendations),
        'model_type': 'Collaborative + Content-Based Filtering',
        'user_id': user_id
    })


@app.route('/retrain', methods=['POST'])
def retrain():
    """Retrain all models"""
    success1 = train_model()
    success2 = train_recommendation_model()
    return jsonify({
        'success': success1 and success2, 
        'medicines_count': len(model['medicines']),
        'recommendation_trained': rec_model['trained']
    })


@app.route('/health', methods=['GET'])
def health():
    """Health check"""
    return jsonify({
        'status': 'running',
        'model_trained': model['trained'],
        'medicines_loaded': len(model['medicines']),
        'recommendation_trained': rec_model['trained']
    })


if __name__ == '__main__':
    print("=" * 50)
    print("MediMitra ML Medicine Matcher")
    print("=" * 50)
    
    # Train on startup
    train_model()
    train_recommendation_model()
    
    print(f"\nStarting server on http://localhost:5050")
    print("Endpoints:")
    print("  POST /analyze    - Analyze prescription text")
    print("  POST /recommend  - Get ML recommendations")
    print("  POST /retrain    - Retrain all models")
    print("  GET  /health     - Health check")
    print("=" * 50)
    
    app.run(host='0.0.0.0', port=5050, debug=False)
