<?php
require __DIR__."/../vendor/autoload.php";

use Source\App\HomeController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

$app = new App();

//users
$app->get("/user", "Source\App\Api\ApiUserController:getUsers");
$app->get("/user/{id}", "Source\App\Api\ApiUserController:getUser");
$app->post("/user", "Source\App\Api\ApiUserController:postUserCreate");
$app->put("/user", "Source\App\Api\ApiUserController:getUserUpdate");
$app->delete("/user", "Source\App\Api\ApiUserController:getUserDelete");

//blog
$app->get("/post", "Source\App\Api\ApiPostController:getPosts");
$app->get("/post/{id}", "Source\App\Api\ApiPostController:getPost");

$app->run();

