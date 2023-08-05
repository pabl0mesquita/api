<?php
require __DIR__."/vendor/autoload.php";

use Source\App\HomeController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App();

$app->get("/", "Source\App\HomeController:index");
$app->get("/hello/{user}", "Source\App\HomeController:index");

$app->run();

