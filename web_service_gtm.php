<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, *');

// Guardar todos los POST recibidos en un archivo
$logPath = 'post_logs.txt'; // Puedes cambiar la ruta si lo deseas
$logContent = date('Y-m-d H:i:s') . " - POST DATA:\n" . print_r($_POST, true) . "\n--------------------------\n";
file_put_contents($logPath, $logContent, FILE_APPEND);

$type = $_POST["type"];
$email = $_POST["email"];

// Validar si $email está vacío o no contiene un formato de correo electrónico válido
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Detener la ejecución del script y devolver un mensaje de error
    exit(); 
    
}


if ($type == "cart") {
    $service_name = $_POST["service_name"];
    $sku = $_POST["sku"];
    $category = $_POST["category"];
    $quantity = $_POST["quantity"];
    $price = $_POST["price"];
    $image_url = $_POST["image_url"];
    $timestamp = $_POST["timestamp"];
    $medical_unit = $_POST["medical_unit"];
    $url = $_POST["url"];
    $order = $_POST["order"];
    $pricem = $_POST["pricem"];
    $cita = $_POST["cita"];
    $discount = $_POST["discount"];
    $p_related = $_POST["p_related"];
    $utmSource = $_POST["utmSource"];
    $utmMedium = $_POST["utmMedium"];
    $utmCampaign = $_POST["utmCampaign"];

    //Validadión de campos
    $discount_v = $pricem !== "" ? 'true' : '';
    $cita_v = ($cita !== "" && $cita !== "undefined") ? 'true' : '';

    if ($discount == "Membresía") {
        $membresia_v = 'true';
        $oferta_v = '';
    } else if ($discount == "¡Oferta!") {
        $oferta_v = 'true';
        $membresia_v = '';
    } else {
        $membresia_v = '';
        $oferta_v = '';
        $discount = '';
    }

    // Generar un GUID
    $guid = 'id_' . substr(md5(uniqid("", true)), 0, 8); // Ajusta la longitud según tus necesidades


    //Autenticación OIC
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://idcs-8332050b9ca94ab48f84d174e8db9675.identity.oraclecloud.com/oauth2/v1/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id=8c4e30a91aed4b68a4ed0994d4a18f8c&client_secret=d16a98e1-5ccf-4c13-9fb8-2495157cbf81&scope=christusmuguerza.com.mx%2FEcommerce',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $access_token = json_decode($response)->access_token;




    // Enviar datos a OIC
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://servicios-oic.christus.mx/CloudUtilities/Ecommerce/AddCart',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
          "Header": {
            "TrackingID": "ECOMCART",
            "Source": "ECOMMERCE"
          },
          "File": {
            "Auth" : "AUTH",
            "Date" : "' . $timestamp . '",
            "type": "FormData",
            "fieldValues":  [
                {
                    "type": "FieldValue",
                    "id": "6658",
                    "value": "' . $email . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6671",
                    "value": "' . $service_name . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6672",
                    "value": "' . $sku . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6673",
                    "value": "' . $category . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6674",
                    "value": "' . $quantity . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6675",
                    "value": "' . $price . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6911",
                    "value": "' . $pricem . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6678",
                    "value": "' . $image_url . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6873",
                    "value": "' . $timestamp . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6872",
                    "value": "' . $medical_unit . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6874",
                    "value": "' . $url . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6875",
                    "value": "' . $guid . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6876",
                    "value": "' . $order . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6912",
                    "value": "' . $cita . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6936",
                    "value": "' . $discount . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6939",
                    "value": "' . $discount_v . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6933",
                    "value": "' . $cita_v . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6938",
                    "value": "' . $membresia_v . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6937",
                    "value": "' . $oferta_v . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6984",
                    "value": "' . $utmSource . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6985",
                    "value": "' . $utmMedium . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6986",
                    "value": "' . $utmCampaign . '"
                }
            ]
          }
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // $logFilePath = 'respuesta_log.txt';
    // $logFile = fopen($logFilePath, 'a+');
    // fwrite($logFile, date('Y-m-d H:i:s') . " - Respuesta: CART\n " . $response . "\n\n");
    // fclose($logFile);
    echo $response;


    if ($p_related != null) {
        $parsed_related = json_decode($p_related, true);

        foreach ($parsed_related as $product) {
            $name_pr = $product['name'];
            $price_pr = $product['price'];
            $image_url_pr = $product['image_url'];
            $url_pr = $product['product_url'];
            $order_pr = $product['order'];
            $sale_pr = $product['sale'];

            // Enviar datos a Eloqua
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://secure.p04.eloqua.com/API/REST/2.0/data/form/646',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
            "type": "FormData",
            "fieldValues": [
                {
                    "type": "FieldValue",
                    "id": "6955",
                    "value": "' . $email . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6956",
                    "value": "' . $name_pr . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6957",
                    "value": "' . $category . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6958",
                    "value": "' . $price_pr . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6960",
                    "value": "' . $image_url_pr . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6959",
                    "value": "' . $timestamp . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6962",
                    "value": "' . $medical_unit . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6964",
                    "value": "' . $url_pr . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6954",
                    "value": "' . $guid . $order_pr . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6961",
                    "value": "' . $order_pr . '"
                },
                {
                    "type": "FieldValue",
                    "id": "6967",
                    "value": "' . $sale_pr . '"
                }
            ]
        }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Basic Q2hyaXN0dXNcS3VubWFwLkFnZW5jaWE6S3VuTTRwS3VuTTRwQDIwMjM='
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            // $logFilePath = 'respuesta_log.txt';
            // $logFile = fopen($logFilePath, 'a+');
            // fwrite($logFile, date('Y-m-d H:i:s') . " - Respuesta PR:\n " . $response . "\n\n");
            // fclose($logFile);
            // echo $response;
        }
    }
} else if ($type == "remove") {

    $email = $_POST["email"];
    $sku = $_POST["sku"];

    // URL encode
    $encodedEmail = urlencode($email);
    $encodedSku = urlencode($sku);

    $url = 'https://secure.p04.eloqua.com/api/REST/2.0/data/customObject/271/instances?search=SKU1%3D%27' . $encodedSku . '%27Email_Address1%3D%27' . $encodedEmail . '%27';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic Q2hyaXN0dXNcS3VubWFwLkFnZW5jaWE6S3VuTTRwS3VuTTRwQDIwMjM='
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;

    // Log the GET request and response
    $logFile = __DIR__ . '/log.txt';

    $elements = json_decode($response)->elements ?? [];
    foreach ($elements as $element) {
        $id = $element->id;
        $deleteUrl = 'https://secure.p04.eloqua.com/api/REST/2.0/data/customObject/271/instance/' . $id;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $deleteUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic Q2hyaXN0dXNcS3VubWFwLkFnZW5jaWE6S3VuTTRwS3VuTTRwQDIwMjM='
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;

        $logEntry = "\n$encodedEmail\n DELETE Request: $deleteUrl\nResponse: $response\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}