<?php
require_once 'FileManager.php';

class Materia extends FileManager
{
    public $_nombre;
    public $_cuatrimestre;
    public $_id;

    public function __construct($nombre, $cuatrimestre, $id = 0)
    {
        if (!is_null($nombre) && is_string($nombre)) {
            $this->_nombre = $nombre;
        }
        if (!is_null($cuatrimestre) && is_string($cuatrimestre)) {
            $this->_cuatrimestre = $cuatrimestre;
        }
        $this->_id = $id;
    }

    public static function isUniqueAndSet($materia)
    {
        //RTA ES UN ARRAY QUE TIENE UN CAMPO QUE ES TRUE POR DEFAULT
        $rta = [true];
        if (
            isset($materia->_cuatrimestre) && isset($materia->_nombre)
            && !empty($materia->_nombre) && !empty($materia->_cuatrimestre)
        ) {

            $materias = Materia::leerJson();

            foreach ($materias as $value) {
                if ($value->_nombre == $materia->_nombre) {
                    //SI EL OBJETO SE REPITE ASIGNO MENSAJE AL SEGUNDO INDICE
                    $rta = [false, "Esta materia está repetida... No se guardó"];
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
    //     return $this->_nombre.'*'.$this->_cuatrimestre.'*'.$this->_id.PHP_EOL;
    // }

    public function __toString()
    {
        return "Materia: $this->_nombre | Cuatrimestre: $this->_cuatrimestre";
    }

    public static function guardarJson($materia)
    {
        $listaDeMaterias = Materia::leerJson();
        $materia = Materia::asignarId($listaDeMaterias, $materia);

        array_push($listaDeMaterias, $materia);

        parent::jsonWrite("./archivos/materias.json", $listaDeMaterias);
    }

    public static function leerJson()
    {
        $lista = parent::jsonRead("./archivos/materias.json");
        $listaDeMaterias = array();

        foreach ($lista as $datos) {

            if (count((array)$datos) == 3) {
                $materiaNueva = new Materia($datos->_nombre, $datos->_cuatrimestre, $datos->_id);
                array_push($listaDeMaterias, $materiaNueva);
            }
        }
        return $listaDeMaterias;
    }


    public static function asignarId($listaDeMaterias, $materia)
    {
        if (count($listaDeMaterias) == 0) {
            $materia->_id = 1;
        } else {
            $ultimaMateria = end($listaDeMaterias);
            $materia->_id = $ultimaMateria->_id + 1;
        }
        return $materia;
    }
}