<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
namespace Etu\PhpTools\Db;

class Raw
{
    private $data;

    public function __construct($data)
    {
        $this->data = (string) $data;
    }

    public function __toString()
    {
        return $this->data;
    }
}
