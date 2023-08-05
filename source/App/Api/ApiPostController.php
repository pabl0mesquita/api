<?php

namespace Source\App\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Source\Core\Api;
use Source\Models\PostModel;

class ApiPostController extends Api
{

    public function getPosts(Request $request, Response $response, $args)
    {
        //var_dump(get_class_methods($request), $request->getQueryParams(), $args);
        $posts = (new PostModel())->getAll()->where("id", ">=", 5)->fetch();

        foreach($posts as $post){
            $json[] = $post->datas();
        }
        
        /**
        * em caso de sucesso, retorna dados do usuário
        */
        $this->call([
            "request" => "success",
            "status" => 200,
            "data" => $json
            ]
        );
        $this->back();
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
    }

    /**
     * getUser
     * @return void
     */
    public function getPost(Request $request, Response $response, $args): void
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

        $user = new PostModel();
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
