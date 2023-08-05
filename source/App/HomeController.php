<?php

namespace Source\App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Source\Core\Controller;
use Source\Models\UserModel;

class HomeController extends Controller
{

    public function __construct(){
        parent::__construct(__DIR__."/../../themes/".CONF_VIEW_THEME_WEB."/");
    }

    public function index(Request $request, Response $response, $args): void
    {
        //var_dump(get_class_methods($request), $request->getQueryParams(), $args);

        $user = new UserModel();
        $users = $user->getAll()->fetch();

        var_dump($users);

        echo $this->view->render("home", []);
        return;
    }
}
