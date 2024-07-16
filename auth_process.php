<?php 


require_once("globals.php");
require_once("db.php");
require_once("models/User.php");
require_once("models/Message.php");
require_once("dao/UserDAO.php");

$message = new Message($BASE_URL);

$userDao = new UserDAO($conn, $BASE_URL);

// RESGATA O TIPO DO FORMULÁRIO

$type = filter_input(INPUT_POST, "type");

//VERIFICA O TIPO DE FORMULÁRIO

if($type === "register"){

    $name = filter_input(INPUT_POST, "name");
    $lastname = filter_input(INPUT_POST, "lastname");
    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");
    $confirmpassword = filter_input(INPUT_POST, "confirmpassword");


    //VERIFICAÇÃO DE DADOS MÍNIMOS

    if($name && $lastname && $email && $password){

        //VERIFICAR SE AS SENHAS BATEM

        if($password === $confirmpassword){

            //VERIFICAR SE E O E-MAIL JÁ ESTÁ CADASTRADO NO SISTEMA

            if($userDao->findByEmail($email) === false){

                $user = new User();

                //CRIAÇÃO DE TOKEN E SENHA

                $userToken = $user->generateToken();
                $finalPassword = $user->generatePassword($password);
               
                $user->name= $name;
                $user->lastname= $lastname;
                $user->email= $email;
                $user->password= $finalPassword;
                $user->token= $userToken;

                $auth = true;

                $userDao->create($user, $auth);

            } else {

                $message->setMessage("Usuário já cadastrado, tente outro e mail", "error", "back");
            
            }

        } else {

            $message->setMessage("As senhas não são iguais", "error", "back");

        }

    } else {

        $message-> setMessage("Por favor, preencha todos os campos", "error", "back");

    }

} else if ($type === "login"){

    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");

    //TENTA AUTENTICAR USUÁRIO
    if($userDao->authenticateUser($email, $password)){

        $message->setMessage("Seja bem vindo", "sucess", "editprofile.php");

    //REDIRECIONA O USUÁRIO, CASO NÃO CONSEGUIR AUTENTICAR
    } else {

        $message-> setMessage("Usuário e/ou senha incorretos", "error", "back");

    }

} else {

    $message-> setMessage("Informações inválidas", "error", "back");

}

?>