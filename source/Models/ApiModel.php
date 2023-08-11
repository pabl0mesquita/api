<?php

namespace Source\Models;
use Source\Core\Model;

class ApiModel extends Model
{
    public $message;
    public function __construct()
    {
        parent::__construct("app_api", [""], [""]);
    }

}