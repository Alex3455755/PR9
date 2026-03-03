<?php
    $SECRET_KEY = 'cAtwalkkEy';

    function base64UrlDecode($data) {
        return base64_decode(str_replace(['-','_'], ['+','/'], $data));
    }

    function verifyJWT($jwt) {

        global $SECRET_KEY;

        $parts = explode('.', $jwt);

        if(count($parts) != 3) return false;

        list($header, $payload, $signature) = $parts;

        $validSignature = hash_hmac('sha256', $header.".".$payload, $SECRET_KEY, true);

        if(base64UrlDecode($signature) !== $validSignature)
            return false;

        $payloadData = json_decode(base64UrlDecode($payload), true);

        if($payloadData['exp'] < time())
            return false;

        return $payloadData;
    }
?>