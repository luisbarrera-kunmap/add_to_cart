<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

file_put_contents('log.txt', file_get_contents('php://input') . "\n", FILE_APPEND);

// Preflight check
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST allowed']);
    exit;
}

// Read JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Basic validation
if (!$data || !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing data']);
    exit;
}

// Generar un GUID
$guid = 'id_' . substr(md5(uniqid("", true)), 0, 8); // Ajusta la longitud según tus necesidades

// Convert timestamp to HH:MM:SS format
if (!empty($data['timestamp'])) {
    try {
        $datetime = new DateTime($data['timestamp']);
        // Opcional: Ajustar a tu zona horaria local
        $datetime->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['timestamp'] = $datetime->format('H:i:s');
    } catch (Exception $e) {
        $data['timestamp'] = ''; // Fallback si algo sale mal
    }
}

// Extract slug from URL
if (!empty($data['url'])) {
    $parsedUrl = parse_url($data['url'], PHP_URL_PATH); // e.g. "/producto/resonancia-magnetica-de-muneca-simple"
    $data['url'] = basename($parsedUrl); // e.g. "resonancia-magnetica-de-muneca-simple"
}

// Extract image path after /uploads/
if (!empty($data['image_url'])) {
    $uploadsPos = strpos($data['image_url'], '/uploads/');
    if ($uploadsPos !== false) {
        $data['image_url'] = substr($data['image_url'], $uploadsPos + strlen('/uploads/'));
    }
}

// Build Eloqua payload using field IDs
$eloquaPayload = [
    "fieldValues" => [
        [ "id" => "6658", "value" => $data['email'] ],
        [ "id" => "6671", "value" => $data['service_name'] ?? '' ],
        [ "id" => "6672", "value" => $data['sku'] ?? '' ],
		[ "id" => "6673", "value" => $data['category'] ?? '' ],
		[ "id" => "6674", "value" => $data['quantity'] ?? '' ],
		[ "id" => "6675", "value" => $data['price'] ?? '' ],
		[ "id" => "6678", "value" => $data['image_url'] ?? '' ],
		[ "id" => "6873", "value" => $data['timestamp'] ?? '' ],
		[ "id" => "6872", "value" => $data['medical_unit'] ?? '' ],
		[ "id" => "6874", "value" => $data['url'] ?? '' ],
		[ "id" => "6875", "value" => $guid],
		[ "id" => "6876", "value" => $data['order'] ?? '' ],
		[ "id" => "6911", "value" => $data['pricem'] ?? '' ],
		[ "id" => "6912", "value" => $data['cita'] ?? '' ],
		// [ "id" => "", "value" => $data['type'] ?? '' ],
		[ "id" => "6936", "value" => $data['discount'] ?? '' ],
		[ "id" => "6984", "value" => $data['utmSource'] ?? '' ],
		[ "id" => "6986", "value" => $data['utmCampaign'] ?? '' ],
		[ "id" => "6985", "value" => $data['utmMedium'] ?? '' ],
		// [ "id" => "", "value" => $data['p_related'] ?? '' ],
    ]
];

// Send to Eloqua
$ch = curl_init("https://secure.p04.eloqua.com/api/REST/2.0/data/form/618");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic Q2hyaXN0dXNcS3VubWFwLkFnZW5jaWE6S3VuTTRwS3VuTTRwQDIwMjM="
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($eloquaPayload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Respond accordingly
if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode(["success" => true]);
} else {
    http_response_code($httpCode);
    echo json_encode(["error" => "Eloqua API error", "response" => $response]);
}
