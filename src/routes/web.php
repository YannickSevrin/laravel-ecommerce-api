<?php

use Illuminate\Support\Facades\Route;

// Basic route for API documentation or status
Route::get('/', function () {
    return response()->json([
        'message' => 'Laravel E-commerce API',
        'version' => '1.0.0',
        'documentation' => '/api/documentation',
        'status' => 'running'
    ]);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now()->toISOString()
    ]);
});