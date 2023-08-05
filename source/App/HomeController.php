<?php

namespace Source\App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Source\Core\Controller;

class HomeController extends Controller
{

    public function __construct(){
        parent::__construct(__DIR__."/../../themes/".CONF_VIEW_THEME_WEB."/");
    }

    public function index(Request $request, Response $response, $args)
    {
        //var_dump(get_class_methods($request), $request->getQueryParams(), $args);

        echo $this->view->render("home", []);
        return;
    }
}
