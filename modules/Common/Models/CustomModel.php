<?php namespace Modules\Common\Models;

/**
 * DB 직접 접근
 */
class CustomModel
{
    public $db;

    public function __construct()
    {
        $this->db = db_connect();
    }
}