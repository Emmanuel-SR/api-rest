<?php
namespace db;

/**
 * The base class for all resources.
 */
class Model extends ORM
{
    /**
     *
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    protected static $table;

    /**
     * Initializes a new instance of the Model class.
     * @param array $data
     * @return void
     */
    public function __construct(array $data = null)
    {
        $this->data = ($data === null) ? [] : $data;
    }

    /**
     * Description
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    /**
     * Description
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @return array
     */
    public function getColumnas()
    {
        return $this->data;
    }

    /**
     * Return the string representation of the current element
     * @return void
     */
    public function __toString()
    {
        return json_encode($this->data);
    }

}
