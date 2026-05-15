<?php
/**
 * TAPIFY - Backend Entry Point
 */
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Tapify Backend API is running',
    'version' => '1.0.0'
]);
