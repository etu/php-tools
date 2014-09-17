<?php
namespace ET\Db;

class Raw
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->data;
    }
}
