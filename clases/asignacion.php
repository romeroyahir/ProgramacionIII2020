<?php
require_once 'FileManager.php';

class Asignacion extends FileManager
{
  public $_legajoProfesor;
  public $_idMateria;
  public $_turno;

  public function __construct($legajoProfesor, $idMateria, $turno)
  {
    if (!is_null($legajoProfesor) && is_numeric($legajoProfesor)) {
      $this->_legajoProfesor = $legajoProfesor;
    }
    if (!is_null($idMateria) && is_numeric($idMateria)) {
      $this->_idMateria = $idMateria;
    }
    if (!is_null($turno) && is_string($turno)) {
      $this->_turno = $turno;
    }
  }

  public static function isUniqueAndSet($asignacion)
  {
    //RTA ES UN ARRAY QUE TIENE UN CAMPO QUE ES FALSE POR DEFAULT
    $rta = [true];
    $jsonobj = json_encode($asignacion,JSON_PRETTY_PRINT);
    //echo "$jsonobj\n";
    if (
      isset($asignacion->_legajoProfesor) && isset($asignacion->_idMateria) && isset($asignacion->_turno)
      && !empty($asignacion->_legajoProfesor) && !empty($asignacion->_idMateria) && !empty($asignacion->_turno)
    ) {
      $asignaciones = Asignacion::leerJson();
      foreach ($asignaciones as $value) {
        if (
          $asignacion->_legajoProfesor == $value->_legajoProfesor
          && $asignacion->_idMateria == $value->_idMateria
          && $asignacion->_turno == $value->_turno
        ) {
          //SI EL OBJETO SE REPITE ASIGNO MENSAJE AL SEGUNDO INDICE
          $rta = [false, "Esta asignación está repetida... No se guardó"];
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

  public function __toString()
  {
    return "$this->_legajoProfesor*$this->_idMateria*$this->_turno" . PHP_EOL;
  }

  public static function guardarJson($asignacion)
  {
    $listaDeAsignaciones = Asignacion::leerJson();

    array_push($listaDeAsignaciones, $asignacion);

    parent::jsonWrite("./archivos/materias-profesores.json", $listaDeAsignaciones);
  }

  public static function leerJson()
  {
    $lista = parent::jsonRead("./archivos/materias-profesores.json");
    $listaDeAsignaciones = array();

    foreach ($lista as $datos) {

      if (count((array)$datos) == 3) {
        $asignacion = new Asignacion($datos->_legajoProfesor, $datos->_idMateria, $datos->_turno);
        array_push($listaDeAsignaciones, $asignacion);
      }
    }
    return $listaDeAsignaciones;
  }
}