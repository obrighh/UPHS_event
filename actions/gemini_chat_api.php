<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API Configuration
$GEMINI_API_KEY = "AIzaSyBgzqKivfFEseWs2C-WNJ_p3PbmHFnK1fA";

// Try these in order (most likely to work)
$GEMINI_MODEL = "gemini-2.5-flash"; // Change this after running test_gemini_models.php
// $GEMINI_MODEL = "gemini-3-pro"; // Change this after running test_gemini_models.php

$url = "https://generativelanguage.googleapis.com/v1/models/{$GEMINI_MODEL}:generateContent?key={$GEMINI_API_KEY}";

try {
    // Get request data
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception('No input data');
    }
    
    $data = json_decode($input, true);
    if (!isset($data['contents'])) {
        throw new Exception('Missing contents');
    }

    // Prepare Gemini request
    $requestBody = [
        'contents' => $data['contents'],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 1024,
            'topP' => 0.95,
            'topK' => 40
        ],
        'safetySettings' => [
            ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE']
        ]
    ];

    // Call Gemini API
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Handle errors
    if ($curlError) {
        throw new Exception('Connection error: ' . $curlError);
    }

    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = isset($errorData['error']['message']) 
            ? $errorData['error']['message'] 
            : "HTTP Error {$httpCode}";
        throw new Exception($errorMsg);
    }

    // Return success response
    echo $response;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'model_used' => $GEMINI_MODEL ?? 'unknown'
    ]);
}
?>