<?php

include 'vendor/autoload.php';
use Firebase\JWT\JWT;


class JwtAuth
{
    public function __construct()
    {
        
    }

    public function getjwt($userid,$usertype)
    {
        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24),
            'uid' => $userid,
            'utp' => $usertype
        ];

        $jwt = JWT::encode($payload, JWT_KEY, 'HS256');
        return $jwt;
    }
}