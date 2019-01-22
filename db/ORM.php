<?php
namespace db;

use \PDO;

class ORM extends \Conexion
{

    /**
     * @var PDO
     */
    private static $cnx;

    /**
     * Creates a PDO instance representing a connection to a database
     * @return void
     */
    public static function getConexion(): void
    {
        if (self::$cnx === null) {
            self::$cnx = \Conexion::conectar();
        }
    }

    /**
     * Close the connection
     * @return void
     */
    public static function closeConexion(): void
    {
        self::$cnx = null;
    }

    /**
     * Description
     * @param string $procedure
     * @param array|null $params
     * @return type
     */
    public static function call(string $procedure, array $params = null): array
    {
        self::getConexion();

        $query = "CALL " . $procedure;
        if (!is_null($params)) {
            $paramsa = "";
            for ($i = 0; $i < count($params); $i++) {
                $paramsa .= ":" . $i . ",";
            }
            $paramsa = trim($paramsa, ",");
            $paramsa .= ")";
            $query .= "(" . $paramsa;
        } else {
            $query .= "()";
        }
        #echo $query;
        //agregando parametros al query
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindParam(":" . $i, $params[$i]);
        }

        $stmt->execute();
        $data = [];
        foreach ($stmt as $row) {
            $data[] = $row;
        }

        self::closeConexion();
        return $data;
    }

    /**
     * Show a view
     * @param string $name
     * @return array
     */
    public static function view(string $name): array
    {
        $query = 'SELECT * FROM ' . $name;
        self::getConexion();
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $stmt->execute();
        $data = [];
        foreach ($stmt as $row) {
            $data[] = $row;
        }
        self::closeConexion();
        return $data;
    }

    /**
     * Create or update a resource
     * @return bool
     */
    public function save(): bool
    {
        $values   = $this->getColumnas();
        $filtered = null;
        foreach ($values as $key => $value) {
            if (!is_integer($key) && strpos($key, 'obj_') === false && $key !== 'id') {
                if ($value === false) {
                    $value = 0;
                }
                $filtered[$key] = $value;
            }
        }
        $columns = array_keys($filtered);
        if ($this->id) {
            $params = "";
            foreach ($columns as $column) {
                $params .= $column . " = :" . $column . ",";
            }
            $params = trim($params, ",");
            $query  = "UPDATE " . static::$table . " SET $params WHERE id =" . $this->id;
        } else {
            $params  = join(", :", $columns);
            $params  = ":" . $params;
            $columns = join(", ", $columns);
            $query   = "INSERT INTO " . static::$table . " ($columns) VALUES ($params)";
        }
        try {
            self::getConexion();
            $stmt = self::$cnx->prepare($query);
            foreach ($filtered as $key => &$value) {
                $stmt->bindParam(":" . $key, $value);
            }
            if ($stmt->execute()) {
                //si es update devuelve 0
                if (self::$cnx->lastInsertid()) {
                    $this->id = self::$cnx->lastInsertid();
                }
                self::closeConexion();
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage() . "\n";
        }
    }

    /**
     * Delete a resource
     * @param string|null $value
     * @param string|null $column
     * @return bool
     */
    public function delete(string $value = null, string $column = null): bool
    {
        $query = "DELETE FROM " . static::$table . " WHERE " . (is_null($column) ? "id" : $column) . " = :p";
        self::getConexion();
        $stmt = self::$cnx->prepare($query);
        if (!is_null($value)) {
            $stmt->bindParam(":p", $value);
        } else {
            $stmt->bindParam(":p", (is_null($this->id) ? null : $this->id));
        }
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
        self::closeConexion();
    }

    /**
     * Description
     * @param array $arguments
     * @return array
     */
    public static function where(array $arguments): array
    {
        $class = get_called_class();
        self::getConexion();
        $columns = array_keys($arguments);
        $params  = "";
        foreach ($columns as $value) {
            $params .= $value . " = :" . $value . " AND ";
        }
        $params = rtrim($params, " AND ");
        $query  = "SELECT * FROM " . static::$table . " WHERE " . $params;
        $stmt   = self::$cnx->prepare($query);
        foreach ($arguments as $key => &$value) {
            $stmt->bindParam(":" . $key, $value);
        }
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $data = [];
        foreach ($stmt as $row) {
            $data[] = new $class($row);
        }
        self::closeConexion();
        return $data;
    }

    /**
     * Description
     * @param int $id
     * @return midex
     */
    public static function find(int $id)
    {
        $data = self::where(["id" => $id]);
        if (count($data)) {
            return $data[0];
        } else {
            return null;
        }
    }

    /**
     * Description
     * @return array
     */
    public static function all()
    {
        $query = "SELECT * FROM " . static::$table;
        $class = get_called_class();
        self::getConexion();
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $data = [];
        foreach ($stmt as $row) {

            $data[] = new $class($row);
        }
        self::closeConexion();
        return $data;
    }

}
