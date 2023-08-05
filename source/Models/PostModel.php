<?php

namespace Source\Models;
use Source\Core\Model;

class PostModel extends Model
{
    public function __construct()
    {
        parent::__construct("posts", [""],[""]);
    }
}