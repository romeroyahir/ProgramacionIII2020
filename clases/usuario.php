<?php
require_once 'FileManager.php';
use \Firebase\JWT\JWT;

class Usuario extends FileManager
{
    public $_email;
    public $_clave;

    public function __construct($email, $clave)
    {
        if (!is_null($email) && is_string($email)) {
            $this->_email = $email;
        }
        if (!is_null($clave) && is_string($clave)) {
            $this->_clave = $clave;
        }
    }

    public static function isUniqueAndSet($user)
    {
        //RTA ES UN ARRAY QUE TIENE UN CAMPO QUE ES TRUE POR DEFAULT
        $rta = [true];

        if (
            isset($user->_email) && isset($user->_clave)
            && !empty($user->_clave) && !empty($user->_email)
        ) {
            $usuarios = Usuario::leerJson();
            foreach ($usuarios as $value) {

                if ($value->_email == $user->_email) {
                    //SI EL OBJETO SE REPITE ASIGNO MENSAJE AL SEGUNDO INDICE
                    $rta = [false, "Email repetido... No se guardó"];
                }
            }
        } else {
            //SI ALGUN CAMPO ESTÁ VACÍO ASIGNO MENSAJE AL SEGUNDO INDICE
            $rta = [false, "No se permiten campos vacíos"];
        }

        return $rta;
    }

    public static function login($email, $clave)
    {
        $encodeOk = false;
        $payload = array();
        $usuarios = Usuario::leerJson();
         
        foreach ($usuarios as $user) {
            if ($user->_email == $email && password_verify($clave, $user->_clave)) {
               echo "ENTRO EN EL IF ";
                $payload = array(
                    "email" => $email,
                    "clave" => $clave
                );
                $encodeOk = JWT::encode($payload, "pro3-parcial");
            break;
            }
        }
        return $encodeOk;
    }

    public static function isAdmin($token){
        try{
            JWT::decode($token, "pro3-parcial", array('HS256'));
            return true;
        }
        catch(Exception $ex){
            return false;
        }    
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __toString()
    {
        return $this->_email . '*' . $this->_clave . PHP_EOL;
    }

    public static function guardarJson($usuario)
    {
        $listaDeUsuarios = Usuario::leerJson();

        array_push($listaDeUsuarios, $usuario);
        parent::jsonWrite("./archivos/users.json", $listaDeUsuarios);
    }

    public static function leerJson()
    {
        $lista = parent::jsonRead("./archivos/users.json");
        $listaDeUsuarios = array();

        foreach ($lista as $datos) {

            if (count((array)$datos) == 2) {
                $usuario = new Usuario($datos->_email, $datos->_clave);
                array_push($listaDeUsuarios, $usuario);
            }
        }
        return $listaDeUsuarios;
    }
}