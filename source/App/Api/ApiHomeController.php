<?php

namespace Source\App\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Source\Core\Api;
use Source\Models\UserModel;

class ApiHomeController extends Api
{

    public function index(Request $request, Response $response, $args): void
    {
        //var_dump(get_class_methods($request), $request->getQueryParams(), $args);

        $user = new UserModel();
        $users = $user->get()->where("id","=", 2)->fetch();

        //echo json_encode($users);

        echo json_encode([
            "errors" => [
                "type " => "endpoint_not_found",
                "message" => "Não foi possível processar a requisição"
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return;
    }

    /**
     * getUser
     * @return void
     */
    public function getUser(Request $request, Response $response, $args): void
    {
        
        $validateInt = $this->validateInt($args['id']);
        /**
         * valida parâmetro id de pesquisa
         */
        var_dump($validateInt);
        if(!$validateInt){
            $this->call([
                    "request" => "error",
                    "type" => "invalid_param",
                    "message" => "Parâmetro de pesquisa inválido. Certifique-se de que o valor seja um inteiro.",
                    "status" => 400
                    ]);
            $this->back();
            return;
        }

        $user = new UserModel();
        $user = $user->get()->where("id","=", $args['id'])->fetch();
        
        /**
         * verifica se o usuário foi retornado da base
         */
        if(!$user){
            $this->call([
                    "request" => "error",
                    "type" => "invalid_id",
                    "message" => "Não encontramos usuário com este id em nossa base",
                    "status" => 401
                    ]);
            $this->back();
            return;
        }

        /**
         * em caso de sucesso, retorna dados do usuário
         */
        $this->call([
                "request" => "success",
                "status" => 200,
                "data" => $user->datas()
                ]
        );
        $this->back();
        return;
    }
}
