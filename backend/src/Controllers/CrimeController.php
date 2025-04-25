<?php
namespace DetectiveCase\Controllers;

class CrimeController {
    
    /**
     * Health check endpoint
     */
    public function healthCheck() {
        return json_encode([
            'status' => 'healthy',
            'message' => 'DetectiveCase PHP API is running',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get crimes data - passes through to Python server
     */
    public function getCrimes() {
        // Get query parameters
        $params = [
            'type' => $_GET['type'] ?? 'ALL',
            'start_date' => $_GET['start_date'] ?? '2019-01-01',
            'end_date' => $_GET['end_date'] ?? '2023-01-01',
            'limit' => $_GET['limit'] ?? 1000
        ];
        
        // Log the request
        logMessage('Requesting crimes data with params: ' . json_encode($params));
        
        // Make request to Python API
        $response = makeApiRequest('crimes', $params);
        
        // Return response
        return $response;
    }
    
    /**
     * Get crime types - passes through to Python server
     */
    public function getCrimeTypes() {
        // Log the request
        logMessage('Requesting crime types data');
        
        // Make request to Python API
        $response = makeApiRequest('crime-types');
        
        // Return response
        return $response;
    }
    
    /**
     * Get crime heatmap data - passes through to Python server
     */
    public function getCrimeHeatmap() {
        // Get query parameters
        $params = [
            'type' => $_GET['type'] ?? 'ALL',
            'period' => $_GET['period'] ?? 'pre-covid'
        ];
        
        // Log the request
        logMessage('Requesting crime heatmap data with params: ' . json_encode($params));
        
        // Make request to Python API
        $response = makeApiRequest('crime-heatmap', $params);
        
        // Return response
        return $response;
    }
    
    /**
     * Get neighborhood statistics - passes through to Python server
     */
    public function getNeighborhoodStats() {
        // Log the request
        logMessage('Requesting neighborhood stats data');
        
        // Make request to Python API
        $response = makeApiRequest('neighborhood-stats');
        
        // Return response
        return $response;
    }
} 