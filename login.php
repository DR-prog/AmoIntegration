<?php
require_once 'accountconfig.php';

function getToken() {
    global $client_id, $client_secret, $redirect_uri, $link;

    if (isset($_GET['code'])) {
        $code = $_GET['code'];

        // Формируем данные для отправки в POST запросе
        $data = array(
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code'
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

        return  0;
    } else {
        die("Возникли проблемы с получением кода авторизации");
    }
}

$access_token = getToken();

// Выводим access token
echo "Access Token: " . $access_token;

