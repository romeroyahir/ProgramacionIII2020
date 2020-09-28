<?php
require_once 'FileManager.php';

class Profesor extends FileManager
{
    public $_nombre;
    public $_legajo;

    public function __construct($nombre, $legajo)
    {
        if (!is_null($nombre) && is_string($nombre)) {
            $this->_nombre = $nombre;
        }
        if (!is_null($legajo) && is_numeric($legajo)) {
            $this->_legajo = $legajo;
        }
    }

    public static function isUniqueAndSet($profesor)
    {
        //RTA ES UN ARRAY QUE TIENE UN CAMPO QUE ES TRUE POR DEFAULT
        $rta = [true];
        if (
            isset($profesor->_legajo) && isset($profesor->_nombre)
            && !empty($profesor->_nombre) && !empty($profesor->_legajo)
        ) {
            $profesores = Profesor::leerJson();
            foreach ($profesores as $value) {
                if ($value->_legajo == $profesor->_legajo) {
                    //SI EL OBJETO SE REPITE ASIGNO MENSAJE AL SEGUNDO INDICE
                    $rta = [false, "Legajo repetido... No se guardó"];
                }
            }
        } else {
            //SI ALGUN CAMPO ESTÁ VACÍO ASIGNO MENSAJE AL SEGUNDO INDICE
            $rta = [false, "No se permiten campos vacíos"];
        }

        return $rta;
    }


    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    // public function __toString()
    // {
    //     return $this->_nombre . '*' . $this->_legajo.PHP_EOL;
    // }

    public function __toString()
    {
        return "Profesor [ Nombre: $this->_nombre | Legajo: $this->_legajo ]" . PHP_EOL;
    }

    public static function guardarJson($profesor)
    {
        $listaDeProfesores = Profesor::leerJson();

        array_push($listaDeProfesores, $profesor);

        parent::jsonWrite("./archivos/profesores.json", $listaDeProfesores);
    }

    public static function leerJson()
    {
        $lista = parent::jsonRead("./archivos/profesores.json");
        $listaDeProfesores = array();

        foreach ($lista as $datos) {

            if (count((array)$datos) == 2) {
                $profesorNuevo = new Profesor($datos->_nombre, $datos->_legajo);
                array_push($listaDeProfesores, $profesorNuevo);
            }
        }
        return $listaDeProfesores;
    }

    public static function guardarTxt($profesor)
    {
        $listaDeProfesores = Profesor::leerTxt();

        array_push($listaDeProfesores, $profesor);

        parent::txtWrite("./archivos/profesores.txt", $listaDeProfesores);
    }

    public static function leerTxt()
    {
        $lista = parent::txtRead("./archivos/profesores.txt");
        $listaDeProfesores = array();

        foreach ($lista as $datos) {

            if (count((array)$datos) == 2) {
                $profesorNuevo = new Profesor($datos[0], $datos[1]);
                array_push($listaDeProfesores, $profesorNuevo);
            }
        }

        return $listaDeProfesores;
    }

    public static function guardarSrlz($profesor)
    {
        $listaDeProfesores = Profesor::leerSrlz();

        array_push($listaDeProfesores, $profesor);

        parent::serializeWrite("./archivos/profesores.ser", $listaDeProfesores);
    }

    public static function leerSrlz()
    {
        $lista = parent::serializeRead("./archivos/profesores.ser");
        $listaDeProfesores = array();

        foreach ($lista as $datos) {

            if (count((array)$datos) == 2) {
                $profesorNuevo = new Profesor($datos->_nombre, $datos->_legajo);
                array_push($listaDeProfesores, $profesorNuevo);
            }
        }
        return $listaDeProfesores;
    }
}