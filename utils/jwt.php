<?php
require_once '../vendor/autoload.php';
require 'load_env.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function jwt_token($user_id, $firstname, $lastname, $role)
{
    $jwt_secret = $_ENV['JWT_SECRET'];
    $payload = JWT::encode(
        array(
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'data' => array(
                'user_id' => $user_id,
                'name' => $firstname . ' ' . $lastname,
                'role' => $role
            )
        ),
        $jwt_secret,
        'HS256'
    );

    return $payload;
}

function decodeJWT($token)
{
    $jwt_secret = $_ENV['JWT_SECRET'];

    return JWT::decode($token, new Key($jwt_secret, 'HS256'));
}