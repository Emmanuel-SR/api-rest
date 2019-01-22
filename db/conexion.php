<?php

/**
 * Class to connect to a database
 */
class Conexion
{
    /**
     * Creates a PDO instance to represent a connection to the requested database
     * @return PDO object
     */
    public static function conectar()
    {
        try {

            $cnx = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        return $cnx;
    }
}
