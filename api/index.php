<?php
require __DIR__."/../vendor/autoload.php";

use Source\App\HomeController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

$app = new App();



### USERS ###
//user
$app->get("/user/{id}", "Source\App\Api\ApiUserController:getUser");
$app->post("/user", "Source\App\Api\ApiUserController:postUserCreate");
$app->put("/user", "Source\App\Api\ApiUserController:getUserUpdate");
$app->delete("/user", "Source\App\Api\ApiUserController:getUserDelete");
//users
$app->post("/users", "Source\App\Api\ApiUserController:postUsersCreate");
$app->get("/users", "Source\App\Api\ApiUserController:getUsers");

//blog
$app->get("/post", "Source\App\Api\ApiPostController:getPosts");
$app->get("/post/{id}", "Source\App\Api\ApiPostController:getPost");

$app->run();

