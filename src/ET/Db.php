<?php
namespace ET;

use \ET\Db\BackendInterface as Backend;
use \ET\Db\Raw as DbRaw;

class Db
{
    /** @var Config */
    private $config;

    /** @var Backend */
    private $backend;

    private $database;
    private $lastQuery;

    public function __construct(Config $config, Backend $backend, $database = 'db')
    {
        $this->config   = $config;
        $this->backend  = $backend;
        $this->database = $database;

        return $this->backend->connect($this->config, $this->database);
    }

    public function query($sql, $params = [])
    {
        // Handle params
        foreach ($params as $key => $value) {
            switch (gettype($value)) {
                case 'object':
                    if (get_class($value) == 'ET\Db\Raw') {
                        $fixedValue = (string) $value;
                    }
                    break;
                default:
                    $fixedValue = $this->backend->escape($value);
                    break;
            }

            $sql = str_replace($key, $fixedValue, $sql);
        }
        // Save prepared query
        $this->lastQuery = $sql;

        // Run query
        return $this->backend->query($sql);
    }

    public function insertId($name = null)
    {
        return (int) $this->backend->insertId($name);
    }

    public function lastQuery()
    {
        return $this->lastQuery;
    }
}
