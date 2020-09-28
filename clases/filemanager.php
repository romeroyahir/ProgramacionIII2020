<?php

class FileManager
{

    //Modos:
    // 'r' 	L -> puntero al principio del fichero.
    // 'r+' L/E -> puntero al principio del fichero.
    // 'w' 	E -> puntero al principio del fichero y "suprime" el archivo. Si no existe se intenta crear.
    // 'w+' L/E -> puntero al principio del fichero y "suprime" el archivo. Si no existe se intenta crear.
    // append
    // 'a' 	E -> puntero del fichero al final. Si no existe, se intenta crear. 
    // 'a+' L/E -> puntero del fichero al final. Si no existe, se intenta crear. 

    public static function txtRead($fileName)
    {
        $listaDeDatos = array();

        if (!file_exists($fileName))
            return $listaDeDatos;

        $fileStream = fopen($fileName, 'r');

        while (!feof($fileStream)) {
            $linea = fgets($fileStream);
            $objeto = explode('*', $linea);
            if(count($objeto) > 0){
                array_push($listaDeDatos, $objeto);
            }
        }

        fclose($fileStream);
        
        return $listaDeDatos;
    }

    public static function txtWrite($fileName, $elementos)
    {
        $fileStream = fopen($fileName, 'w');
        if ($fileStream != null) {
            foreach ($elementos as $item) {
                fwrite($fileStream, $item);
            }
        }
        fclose($fileStream);
    }

    public static function jsonRead($fileName)
    {
        $listaDeDatos = array();
        $fread = "{}";

        if (file_exists($fileName)) 
        {
            $fileStream = fopen($fileName, 'r');
            $size = filesize($fileName);
            if ($size > 0) 
            {
                $fread = fread($fileStream, $size);
            }
            $listaDeDatos = json_decode($fread);
            fclose($fileStream);
        }
        return $listaDeDatos;

    }

    public static function jsonWrite($fileName, $elementos)
    {
        $fileStream = fopen($fileName, 'w');
        fwrite($fileStream, json_encode($elementos));
        fclose($fileStream);
    }

    public static function serializeRead($fileName)
    {
        $listaDeDatos = array();
        $fread = "{}";

        if(file_exists($fileName))
        {
            $fileStream = fopen($fileName, 'r');
            $size = filesize($fileName);
            if($size > 0)
            {
                $fread = fread($fileStream, $size);
            }
            $listaDeDatos = unserialize($fread);
            fclose($fileStream);
        }

        return $listaDeDatos;
    }

    public static function serializeWrite($fileName, $elementos)
    {
        $fileStream = fopen($fileName, 'w');
        fwrite($fileStream, serialize($elementos));
        fclose($fileStream);
    }

}