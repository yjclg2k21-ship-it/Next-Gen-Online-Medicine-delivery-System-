<?php
namespace App\Controllers;

use App\Models\HealthMetric;
use App\Services\MedicineIntelligence;

/**
 * Clinical Controller
 * Manages user health metrics and provides clinical insights.
 */
class ClinicalController extends BaseController {

    private $metricModel;
    private $medIntelligence;

    public function __construct() {
        $this->metricModel = new HealthMetric();
        $this->medIntelligence = new MedicineIntelligence();
    }

    /**
     * Get user health metrics
     */
    public function getMetrics() {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $metrics = $this->metricModel->getLatestByUser($userId);
        return $this->json(['success' => true, 'metrics' => $metrics]);
    }

    /**
     * log new health reading
     */
    public function logMetric() {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $data = $this->getPostData();
        $metricId = $this->metricModel->create([
            'user_id' => $userId,
            'metric_type' => $data['type'] ?? 'glucose',
            'value_1' => $data['value_1'] ?? 0,
            'value_2' => $data['value_2'] ?? null,
            'unit' => $data['unit'] ?? ''
        ]);

        return $this->json(['success' => true, 'id' => $metricId, 'message' => 'Metric logged successfully']);
    }

    /**
     * Get Clinical Insights for Dashboard
     */
    public function getInsights() {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $analysis = $this->medIntelligence->analyzeHealthData($userId);
        return $this->json(['success' => true, 'insights' => $analysis['insights'], 'count' => $analysis['metric_count']]);
    }
}
