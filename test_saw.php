<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Services\SAWService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing SAW Analysis ===\n";

try {
    $sawService = new SAWService();
    
    echo "1. Calculating training needs...\n";
    $results = $sawService->calculateTrainingNeeds();
    
    echo "2. Results found: " . $results->count() . "\n";
    
    if ($results->count() > 0) {
        echo "3. Saving results to database...\n";
        $sawService->saveTrainingNeeds($results);
        
        echo "4. Top 5 Training Priorities:\n";
        foreach ($results->take(5) as $index => $result) {
            $employee = $result['employee'];
            $score = $result['saw_score'];
            $recommendation = $result['training_recommendation'];
            
            echo sprintf(
                "   %d. %s (NIP: %s) - Score: %.4f - Priority: %s\n",
                $index + 1,
                $employee->name,
                $employee->nip,
                $score,
                $recommendation['priority']
            );
        }
        
        echo "\n=== Analysis completed successfully! ===\n";
        echo "Visit http://127.0.0.1:8000 to see the results in web interface.\n";
    } else {
        echo "No results found. Please check if employees have assessments.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}