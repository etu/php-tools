<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace ET\Db;

use \ET\Config;

class PdoBackend implements BackendInterface
{
    /** @var Config */
    private $config;

    /** @var \PDO */
    private $pdo;

    /** @var \PDOStatement  */
    private $lastStatement;

    public function connect(Config $config, $database = 'db')
    {
        $this->config = $config;

        if (!isset($this->config->$database->dsn)) {
            throw new DbException('DSN is not defined in config, this is required for PDO');
        }

        if (!isset($this->config->$database->username) || !isset($this->config->$database->password)) {
            throw new DbException(
                'Username or Password is not defined in config,'.
                'this is required to be at least empty.'
            );
        }

        try {
            $this->pdo = new \PDO(
                $this->config->$database->dsn,
                $this->config->$database->username,
                $this->config->$database->password
            );
        } catch (\PDOException $e) {
            throw new DbException('PDO Exception: '.$e->getMessage(), (int) $e->getCode());
        }

        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return true;
    }

    public function query($query)
    {
        try {
            $this->lastStatement = $this->pdo->query($query);
        } catch (\PDOException $e) {
            throw new DbException('PDO Exception: '.$e->getMessage(), (int) $e->getCode());
        }

        return (bool) $this->lastStatement;
    }

    public function insertId($name = null)
    {
        return (int) $this->pdo->lastInsertId($name);
    }

    public function escape($string)
    {
        return $this->pdo->quote($string);
    }

    public function fetchRow()
    {
        return $this->lastStatement->fetch();
    }

    public function fetchAll()
    {
        return $this->lastStatement->fetchAll();
    }
}
