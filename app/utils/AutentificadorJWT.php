<?php

use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000),
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "Test JWT"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
