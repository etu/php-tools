<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Etu\PhpTools\Db;

use \Etu\PhpTools\Config;

interface BackendInterface
{
    public function connect(Config $config, $database = 'db');

    public function query($query);
    public function insertId($name = null);
    public function escape($string);
    public function fetchRow();
    public function fetchAll();
}
