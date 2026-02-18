<?php

class JWT {
    private static $secretKey = "placeholder";
    private static $encrypt = 'HS256';//HMAC-SHA256
    private static $tokenExpiration = 86400;//24 hrs

    //Create token
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$encrypt]);
        
        $payload['exp'] = time() + self::$tokenExpiration;
        $payload = json_encode($payload);

        $base64urlHeader = self::base64urlEncode($header);
        $base64urlPayload = self::base64urlEncode($payload);

        //Create signature
        $signature = hash_hmac(
            'sha256',
            $base64urlHeader . "." . $base64urlPayload,
            self::$secretKey,
            true
        );
        $base64urlSignature = self::base64urlEncode($signature);

        //Return token
        return $base64urlHeader . "." . $base64urlPayload . "." . $base64urlSignature;
    }

    //Decode and verify JWT token
    public static function decode($jwt) {

        $tokenParts = explode('.', $jwt);

        if(count($tokenParts) != 3) {
            return null;
        }

        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];

        //Verify signature
        $base64urlHeader = self::base64urlEncode($header);
        $base64urlPayload = self::base64urlEncode($payload);

        $signature = hash_hmac(
            'sha256',
            $base64urlHeader . "." . $base64urlPayload,
            self::$secretKey,
            true
        );
        $base64urlSignature = self::base64urlEncode($signature);

        //Check if signature matches
        if ($base64urlSignature !== $signatureProvided) {
            return null;
        }

        $payload = json_decode($payload, true);

        //Check if token is expired
        if  (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    private static function base64urlEncode($text) {

        return str_replace(
            ['+', '/', '='],
            ['-','_',''],
            base64_encode($text)
        );
    }
}
?>