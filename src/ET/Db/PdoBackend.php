<?php
namespace ET\Db;

use \ET\Config;

class PdoBackend implements BackendInterface
{
    /** @var Config */
    private $config;

    /** @var PDO */
    private $pdo;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        if (!isset($this->config->db->dsn)) {
            throw new DbException('DSN is not defined in config, this is required for PDO');
        }

        if (!isset($this->config->db->username) || !isset($this->config->db->password)) {
            throw new DbException(
                'Username or Password is not defined in config,'.
                'this is required to be at least empty.'
            );
        }

        try {
            $this->pdo = new \PDO(
                $this->config->db->dsn,
                $this->config->db->username,
                $this->config->db->password
            );
        } catch (\PDOException $e) {
            throw new DbException('PDO Exception: '.$e->getMessage(), (int) $e->getCode());
        }

        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);

        return true;
    }

    public function query($query)
    {
        $result = $this->pdo->query($query);

        if (is_object($result)) {
            $data = (object) [
                'result' => $result->fetchAll(),
                'rows'   => $result->rowCount()
            ];

            return $data;
        }

        return $result;
    }

    public function insertId($name = null)
    {
        return (int) $this->pdo->lastInsertId($name);
    }
}
