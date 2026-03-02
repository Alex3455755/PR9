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
     # Создаём информацию как должна вычисляться JWT подпись
    $header = array("typ" => "JWT", "alg" => "sha256");
    # создаём полезные данные которые будут храниться в JWT
    $payload = array(
        "userId" => password_hash($read_user['id'], PASSWORD_DEFAULT),
        "userRole" => password_hash($read_user['roll'], PASSWORD_DEFAULT),
    );

    # Генерируем секретный ключ
    
    # Токен пользователя из header + payload
    $signedToken = base64_encode(json_encode($header)) . '.' . base64_encode(json_encode($payload));
    # создаём сигнатуру при помощи алгоритма указанного в header signature
    $signature = hash_hmac($header['alg'], $signedToken, $SECRET_KEY);

    $token = base64_encode(json_encode($header)) . '.' . base64_encode(json_encode($payload)) . '.' . base64_encode($signature);
    header("token: " . $token);
   }else{
    header('HTTP/1.0 401 Unauthorized');
   }
} else {
    header('HTTP/1.0 401 Unauthorized');
}
?>