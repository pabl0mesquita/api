<?php

namespace Source\Models;
use Source\Core\Model;

class UserModel extends Model
{
    public function __construct()
    {
        parent::__construct("users", [""],[""]);
    }

    public function bootstrap()
    {
        
    }

    public function findByEmail(string $email)
    {
       return $this->get()->where("email", "=", $email)->fetch();
    }
}