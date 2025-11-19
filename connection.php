<?php


function readIniFile($file = "db_settings.ini"): array
{
    //https://www.php.net/manual/es/function.parse-ini-file.php
//carga el fichero ini especificado en $file, y devuelve las configuraciones que hay en él a un array asociativo $settings 
//o false si hay algún error y no consigue leer el fichero. 
    if (!$settings = parse_ini_file($file, TRUE))
        throw new exception('Unable to open ' . $file . '.');
    return $settings;


  
}

function getConnection(): mysqli
{
    //leemos datos del ini file en un array asociativo
    $settings = readIniFile();


    //Creamos cadena de conexión concatenando
    $host = $settings['database']['host'];
    //Obtenemos la bd
    $db = $settings['database']['schema'];
    $user = $settings['database']['username'];
    $pass = $settings['database']['password'];

    //Creamos el objeto mysqli
    $con = new mysqli($host, $user, $pass,$db);
    
   
    return $con;

}