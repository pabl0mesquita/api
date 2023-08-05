<?php

namespace Source\App\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Source\Core\Api;
use Source\Models\UserModel;

/**
 * Class
 * ApiHomeController
 */
class ApiHomeController extends Api
{

    public function getUsers(Request $request, Response $response, $args)
    {
        //var_dump(get_class_methods($request), $request->getQueryParams(), $args);

        $users = (new UserModel())->getAll()->fetch();

        foreach($users as $user){
            $json[] = $user->datas();
        }

        $this->call([
            "request" => "success",
            "status" => 200,
            "datas" => $json
            ]);
        $this->back();
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
    }

    /**
     * getUser
     */
    public function getUser(Request $request, Response $response, $args)
    {
        /**
         * limita o número de requisições
         */
        $request = $this->requestLimit('User',3, 10);
        if(!$request){
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(408);
        }

        $validateInt = $this->validateInt($args['id']);
        /**
         * valida parâmetro id de pesquisa
         */
        if(!$validateInt){
            $this->call([
                    "request" => "error",
                    "type" => "invalid_param",
                    "message" => "Parâmetro de pesquisa inválido. Certifique-se de que o valor seja um inteiro.",
                    "status" => 400
                    ]);
            $this->back();
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);
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
                    "status" => 400
                    ]);
            $this->back();
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);
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
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
    }
}
