<?php
// Извлекаем корень домена из пути к текущему файлу
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);
require_once(BASE_PATH . '/security.permaviat.ruPR9/settings/connect_datebase.php');





if(!isset($_SERVER['PHP_AUTH_USER'])) { 
    header('HTTP/1.0 403 Forbidden'); 
    exit; 
}
if(!isset($_SERVER['PHP_AUTH_PW'])) { 
    header('HTTP/1.0 403 Forbidden'); 
    exit; 
}

# Получаем данные из хешированного заголовка
$login = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];
$SECRET_KEY = 'cAtwalkkEy';

$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login` = '$login'");
if($read_user = $query_user->fetch_assoc()) {
   if(password_verify($password,$read_user['password'])){
     function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');  // Правильный base64Url [web:21]
}

function base64UrlDecode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

// В вашем коде:
$header = json_encode(["typ" => "JWT", "alg" => "HS256"]);  // HS256, не sha256!
$payload = json_encode([
    "userId" => $read_user['id'],  // Без хэша!
    "userRole" => $read_user['roll'],
    "exp" => time() + 3600  // Добавьте срок!
]);

$headerEncoded = base64UrlEncode($header);
$payloadEncoded = base64UrlEncode($payload);
$signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, $SECRET_KEY, true);
$signatureEncoded = base64UrlEncode($signature);

$token = $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
header("token: " . $token);

   }else{
    header('HTTP/1.0 401 Unauthorized');
   }
} else {
    header('HTTP/1.0 401 Unauthorized');
}
?>