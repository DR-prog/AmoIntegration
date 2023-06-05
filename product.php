<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

function getDealItems($accessToken, $subdomain, $dealId)
{
    $url = "https://{$subdomain}.kommo.com/api/v4/leads/{$dealId}?with=catalog_elements";
    $headers = [
        "Accept: application/json",
        'Authorization: Bearer ' . $accessToken
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Kommo-oAuth-client/1.0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode == 200) {
        $responseData = json_decode($response, true);
        $items = $responseData['_embedded']['catalog_elements'];
        $itemCount = count($items);
        if ($itemCount > 0) {
            echo "<table class='my-table'>";
            echo "<tr><th>Product</th><th>Quantity</th></tr>";
            foreach ($items as $item) {
                $productId = $item['metadata']['catalog_id'];
                $catalogId = $item['id'];
                $productQuantity = $item['metadata']['quantity'];
                $productInfo = getProductInfo($accessToken, $subdomain, $productId, $catalogId);
                if ($productInfo !== null) {
                    $productName = $productInfo['name'];
                    echo "<tr><td>{$productName}</td><td>{$productQuantity}</td></tr>";
                }
            }
            echo "</table>";
        } else {
            echo 'No products found in the lead.';
        }
    } else {
        echo 'Failed to get lead products.';
    }
}

function getProductInfo($accessToken, $subdomain, $productId, $catalogId)
{
    $url = "https://{$subdomain}.kommo.com/api/v4/catalogs/{$productId}/elements/{$catalogId}";
    $headers = [
        "Accept: application/json",
        'Authorization: Bearer ' . $accessToken
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($httpCode == 200) {
        $productInfo = json_decode($response, true);
        $elementName = $productInfo['name'];
        $productInfo['element_name'] = $elementName; // Добавляем имя элемента каталога в массив информации о товаре
        return $productInfo;
    } else {
        return null;
    }
}

$data = json_decode(file_get_contents('TOKEN_FILE.txt'), true);
$accessToken = $data["access_token"];
$subdomain = 'dmitrirastokinyandexru';

if (empty((int)$_GET['lead_id'])) {
    die ("Error: No lead ID provided");
} else {
    $dealId = (int)$_GET['lead_id'];
}
getDealItems($accessToken, $subdomain, $dealId);
?>
<style>
    .my-table {
        width: 100%;
        border-collapse: collapse;
    }

    .my-table th, .my-table td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .my-table th {
        background-color: #f2f2f2;
    }

    .my-table tr:hover {
        background-color: #f5f5f5;
    }
</style>
