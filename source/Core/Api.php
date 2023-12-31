<?php

namespace Source\Core;

use Psr\Http\Message\ResponseInterface as Response;
use Source\Models\AuthModel;
use Source\Models\ApiModel;
class Api
{
    /** @var array|null */
    protected $response;

    /** @headers */
    protected $headers;

    public function __construct()
    {
        $this->headers = getallheaders();
    }
    /**
     * @param int $code
     * @param string|null $type
     * @param string|null $message
     * @param string $rule
     * @return Api
     */
    protected function call(array $info = null, int $code = null, string $type = null, string $message = null, string $rule = "errors"): Api
    {
       
        if (!empty($type)) {
            $this->response = [
                $rule => [
                    "type" => $type,
                    "message" => (!empty($message) ? $message : null)
                ]
            ];
        }

        if(!empty($info)){
            $this->response = $info;
            http_response_code($info["status"]);
        }
        return $this;
    }

    /**
     * @param array|null $response
     * @return Api
     */
    protected function back(array $response = null): Api
    {
        if (!empty($response)) {
            $this->response = (!empty($this->response) ? array_merge($this->response, $response) : $response);
        }

        echo json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function validateInt($int)
    {
        $pattern = "/^([0-9]+)$/";
        $result = preg_match($pattern, $int, $matches);
        return $result;
    }


    public function validateRequestLimit(string $endpoint, int $limit, int $seconds)
    {
        $request = $this->requestLimit($endpoint, $limit, $seconds);

        if(!$request){
            exit;
        }
    }
     /**
     * @param string $endpoint
     * @param int $limit
     * @param int $seconds
     * @param bool $attempt
     * @return bool
     */
    protected function requestLimit(string $endpoint, int $limit = 3, int $seconds = 60, bool $attempt = false): bool
    {

        //$userToken = (!empty($this->headers["email"]) ? base64_encode($this->headers["email"]) : null);
        $userToken = base64_encode($_SERVER["REMOTE_ADDR"]);

         if (!$userToken) {
            $this->call([
                    "request" => "error",
                    "type" => "invalid_data",
                    "message" => "Ip de origem não informado.",
                    "status" => 400
                ]
    
            )->back();

            return false;
        } 

        $cacheDir = __DIR__ . "/../../" . CONF_UPLOAD_DIR . "/requests";
        if (!file_exists($cacheDir) || !is_dir($cacheDir)) {
            mkdir($cacheDir, 0755);
        }

        $cacheFile = "{$cacheDir}/{$userToken}.json";
        if (!file_exists($cacheFile) || !is_file($cacheFile)) {
            fopen($cacheFile, "w");
        }

        $userCache = json_decode(file_get_contents($cacheFile));
        $cache = (array)$userCache;

        $save = function ($cacheFile, $cache) {
            $saveCache = fopen($cacheFile, "w");
            fwrite($saveCache, json_encode($cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            fclose($saveCache);
        };

        if (empty($cache[$endpoint]) || $cache[$endpoint]->time <= time()) {
            if (!$attempt) {
                $cache[$endpoint] = [
                    "limit" => $limit,
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "requests" => 1,
                    "time" => time() + $seconds,
                    
                ];

                $save($cacheFile, $cache);
            }

            return true;
        }

        if ($cache[$endpoint]->requests >= $limit) {
            $this->call([
                    "request" => "error",
                    "type" => "request_limit",
                    "message" => "Você exedeu o limite de requisições para essa ação",
                    "status" => 408
                ]
            )->back();
            
            return false;
        }

        if (!$attempt) {
            $cache[$endpoint] = [
                "limit" => $limit,
                "ip" => $_SERVER["REMOTE_ADDR"],
                "requests" => $cache[$endpoint]->requests + 1,
                "time" => $cache[$endpoint]->time
            ];

            $save($cacheFile, $cache);
        }
        return true;
    }

    /**
     * basicAuth
     * @var int $level
     * @return bool
     */
    public function basicAuth(int $level = 1): bool
    {    
        if(!isset($_SERVER["PHP_AUTH_USER"]) || empty($_SERVER["PHP_AUTH_USER"] || !isset($_SERVER["PHP_AUTH_PW"]) || empty($_SERVER["PHP_AUTH_PW"]))){

            $this->call([
                "request" => "error",
                "type" => "empty_data",
                "message" => "É necessário autenticação para esta ação. Por favor, envie suas credenciais.",
                "status" => 400]
            )->back();
            return false;
        }

        $auth = new AuthModel();
        $user = $auth->verify($_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"], $level);
        
        if(!$user){
            $this->call([
                "request" => "error",
                "type" => "invalid_auth",
                "message" => $auth->message(),
                "status" => 401
            ]

            )->back();
            return false;
        }

        return true;

    }

    /**
     * apiKey
     * @var string $key
     * @var int $level
     * @return bool
     */
    public function apiKey(string $key , int $level = 1): bool
    {
        if(empty($key)){
            $this->call([
                "request" => "error",
                "type" => "empty_data",
                "message" => "É necessário autenticação para esta ação. Por favor, envie a chave da api.",
                "status" => 400]
            )->back();
            return false;
        }

        $api = new ApiModel();
        $keyAuthentication = $api->columns("users.level")
                                 ->get()
                                 ->join("users")
                                 ->on("app_api.user_id", "=", "users.id")
                                 ->where("`key`", "=", $key)
                                 ->fetch();

        if(empty($keyAuthentication)){
            $this->call([
                "request" => "error",
                "type" => "invalid_key_api",
                "message" => "Não foi possível realizar a autenticação: chave da api inválida.",
                "status" => 401
            ]);
            return false;
        }

        if($keyAuthentication->level < $level){
            $this->call([
                "request" => "error",
                "type" => "invalid_permission",
                "message" => "Você não tem permissão para executar essa ação.",
                "status" => 401
            ]);
            return false;
        }

        return true;

    }
}