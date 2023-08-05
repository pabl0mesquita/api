<?php

namespace Source\Models;
use Source\Core\Model;
use Source\Models\UserModel;
class AuthModel extends Model
{
    public $message;
    public function __construct()
    {
        parent::__construct("users", [""],[""]);
    }

    public function message()
    {
        return $this->message;
    }
    public function verify(string $email, string $password, int $level = 1): ?UserModel
    {

        //busca na base o respectivo email
        $user = (new UserModel())->findByEmail($email);
        
        //se o email nao estiver na base, então dá erro
        if (!$user) {
            $this->message = "As credenciais informadas não conferem.";
            return null;
        }

        //verifica se o password rasheado no banco é compatível com a rash do password enviado (helpers)
        if (!passwd_verify($password, $user->password)) {
            $this->message = "As credenciais informadas não conferem.";
            return null;
        }

        // Se o level do usuário for menor que level padrão, então dá erro
        if ($user->level < $level) {
            $this->message = "Desculpe, mas você não tem permissão para logar-se aqui";
            return null;
        }

        // rehash no password no banco.
        if (passwd_rehash($user->password)) {
            $user->password = $password;
            $user->save();
        }
        // Retorna o usuário autenticado
        return $user;
    }
   
}