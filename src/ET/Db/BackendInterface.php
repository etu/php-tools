<?php
namespace ET\Db;

use \ET\Config;

interface BackendInterface
{
    public function connect(Config $config, $database = 'db');

    public function query($query);
    public function insertId($name = null);
    public function escape($string);
}
