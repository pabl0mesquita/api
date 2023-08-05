<?php

namespace Source\Models;
use Source\Core\Model;

class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct("users", [""],[""]);
    }
}