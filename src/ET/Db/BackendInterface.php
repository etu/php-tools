<?php
namespace ET\Db;

use \ET\Config;

interface BackendInterface
{
    public function __construct(Config $config);
    public function connect();

    public function query($query);
    public function insertId($name = null);
    public function escape($string);
}
