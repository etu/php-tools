<?php
namespace ET\Db;

use \ET\Config;

interface BackendInterface
{
    public function connect(Config $config);

    public function query($query);
    public function insertId($name = null);
    public function escape($string);
}
