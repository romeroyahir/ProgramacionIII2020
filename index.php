<?php
require __DIR__ . '\vendor\autoload.php';
include_once __DIR__ . '.\vendor\Firebase\php-jwt\src\JWK.php';

// use \Firebase\JWT\JWT;
// $key = "pro3-parcial";

require_once './clases/profesor.php';
require_once './clases/asignacion.php';
require_once './clases/materia.php';
require_once './clases/usuario.php';




// /**
//  * IMPORTANT:
//  * You must specify supported algorithms for your application. See
//  * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
//  * for a list of spec-compliant algorithms.
//  */
// $decoded = JWT::decode($jwt, $key, array('HS256'));

// print_r($decoded);


$method = $_SERVER['REQUEST_METHOD'];
$path_info = $_SERVER['PATH_INFO'];

//echo "\n$method\n";
//echo "\n$path_info\n";

function getToken()
{
    $headers = getallheaders();
    $jsonobj = json_encode($headers, JSON_PRETTY_PRINT);
    //echo "$jsonobj\n";
    return $headers['token'];
}

switch ($method) {
    case 'GET':
        if (Usuario::isAdmin(getToken())) {
            switch ($path_info) {
                case '/materia':
                    // 6. (GET) materia: Muestra un listado con todas las materias.

                    $materias = Materia::leerJson();
                    echo "MATERIAS: <br>";
                    foreach ($materias as $item) {
                        echo $item . '<br>';
                    }

                    break;
                case '/profesor':
                    // 7. (GET) profesor: Muestra un listado con todas las profesores.
                    $profesores = Profesor::leerJson();
                    echo "PROFESORES: <br>";
                    foreach ($profesores as $item) {
                        echo $item . '<br>';
                    }
                    break;
                case '/asignacion':
                    echo "ASIGNACIONES: " . PHP_EOL;
                    // 8. (GET) asignacion: Muestra un listado con todas las materias asignadas a cada profesor.
                    $profesores = Profesor::leerJson();
                    $materias = Materia::leerJson();
                    $asignaciones = Asignacion::leerJson();

                    foreach ($asignaciones as $asignacion) {
                        $texto = "Turno: $asignacion->_turno ";
                        foreach ($profesores as $profesor) {
                            if ($profesor->_legajo == $asignacion->_legajoProfesor) {
                                $texto = $texto . $profesor;
                            }
                        }

                        foreach ($materias as $materia) {
                            if ($materia->_id == $asignacion->_idMateria) {
                                $texto = $texto . $materia;
                            }
                        }
                        echo $texto . PHP_EOL;
                    }
                    break;
            }
        } else {
            echo "No autorizado...";
        }
        break;
    case 'POST':
        switch ($path_info) {
            case '/usuario':
                //1. (POST) usuario. Registrar a un cliente con email y clave y guardarlo en el archivo users.xxx.
                //save
                $clave = $_POST['clave'] != null ? password_hash($_POST['clave'], PASSWORD_DEFAULT) : "";

                $usuario = new Usuario($_POST['email'],  $clave);

            $jsonobj = json_encode($usuario,JSON_PRETTY_PRINT);
            echo $jsonobj;

                $rta = Usuario::isUniqueAndSet($usuario);
                if ($rta[0]) {
                    Usuario::guardarJson($usuario);
                } else {
                    echo $rta[1];
                }
                break;
            case '/login':
                //2. (POST) login: Recibe email y clave y si son correctos devuelve un JWT, de lo contrario informar lo
                // sucedido. La clave no se debe guardar en texto plano.
                $rta = Usuario::login($_POST['email'],  $_POST['clave']);
                if ($rta != false) {
                    echo $rta;
                } else {
                    echo "Usuario o clave Incorrectos";
                }
                break;
            case '/materia':
                // 3. (POST) materia: Recibe nombre, cuatrimestre y lo guarda en el archivo materias.xxx. Agregar un id único
                // para cada materia.
                if (Usuario::isAdmin(getToken())) {

                    $materia = new Materia($_POST['nombre'], $_POST['cuatrimestre']);
                    $rta = Materia::isUniqueAndSet($materia);
                    if ($rta[0]) {
                        Materia::guardarJson($materia);
                        echo "Materia guardada pancracio!";
                    } else {
                        echo $rta[1];
                    }
                } else {
                    echo "No autorizado...";
                }
                break;
            case '/profesor':
                // 4. (POST) profesor: Recibe nombre, legajo (validar que sea único) y lo guarda en el archivo profesores.xxx.
                if (Usuario::isAdmin(getToken())) {

                    $profesor = new Profesor($_POST['nombre'], $_POST['legajo']);
                    $rta = Profesor::isUniqueAndSet($profesor);
                    if ($rta[0]) {
                        Profesor::guardarJson($profesor);
                        echo "\nProfesor guardadouuu\n";
                    } else {
                        echo $rta[1];
                    }
                } else {
                    echo "No autorizado...";
                }
                break;
            case '/asignacion':
                // 5. (POST) asignacion: Recibe legajo del profesor, id de la materia y turno (manana o noche) y lo guarda en el
                // archivo materias-profesores. No se debe poder asignar el mismo legajo en el mismo turno y materia.
                if (Usuario::isAdmin(getToken())) {
                    $asignacion = new Asignacion($_POST['legajoProfesor'], $_POST['idMateria'], $_POST['turno']);
                    $rta = Asignacion::isUniqueAndSet($asignacion);
                    if ($rta[0]) {
                        Asignacion::guardarJson($asignacion);
                        echo "\nAsignaste re piolitaaaa!!\n";
                    } else {
                        echo $rta[1];
                    }
                } else {
                    echo "No autorizado...";
                }
                break;
            default:
                break;
        }
        break;
    default:
        break;
}