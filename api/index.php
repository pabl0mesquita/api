<?php
require __DIR__."/../vendor/autoload.php";

use Source\App\HomeController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

$app = new App();
$app->get("/", "Source\App\Api\ApiHomeController:index");

//users
$app->get("/user", "Source\App\Api\ApiHomeController:getUsers");
$app->get("/user/{id}", "Source\App\Api\ApiHomeController:getUser");

//blog
$app->get("/post", "Source\App\Api\ApiPostController:getPosts");


$app->run();

