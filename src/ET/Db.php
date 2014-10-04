<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
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

        // Run Query
        try {
            $this->backend->query($sql);
        } catch (\ET\Db\DbException $e) {
            throw $e;
        }

        // Return Db object
        return $this;
    }

    public function insertId($name = null)
    {
        return (int) $this->backend->insertId($name);
    }

    public function lastQuery()
    {
        return $this->lastQuery;
    }

    public function fetchRow()
    {
        return $this->backend->fetchRow();
    }

    public function fetchAll()
    {
        return $this->backend->fetchAll();
    }
}
