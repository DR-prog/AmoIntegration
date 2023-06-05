<?php
require_once 'accountconfig.php';

function refreshToken() {
global $client_id, $client_secret, $redirect_uri, $link;

// Получаем сохраненный access token из файла
$data = json_decode(file_get_contents('TOKEN_FILE.txt'), true);
$refresh_token = $data["refresh_token"];

// Формируем данные для отправки в POST запросе
$data = array(
'refresh_token' => $refresh_token,
'client_id' => $client_id,
'client_secret' => $client_secret,
'redirect_uri' => $redirect_uri,
'grant_type' => 'refresh_token'
);

// Отправляем POST запрос на сервер авторизации
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_USERAGENT, 'Kommo-oAuth-client/1.0');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Обрабатываем ответ сервера
$result = json_decode($response, true);
file_put_contents('TOKEN_FILE.txt', json_encode($result));

// Возвращаем обновленный access token
return $result["access_token"];
}

$access_token = refreshToken();

// Выводим обновленный access token
echo "Updated Access Token: " . $access_token;