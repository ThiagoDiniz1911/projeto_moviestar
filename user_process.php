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

//ATUALIZAR USUÁRIO

if($type === "update"){

    //RESGATA DADOS DO USUÁRIO
    $userData = $userDao->verifyToken();

    //RECEBER DADOS DO POST
    $name = filter_input(INPUT_POST, "name");
    $lastname = filter_input(INPUT_POST, "lastname");
    $email = filter_input(INPUT_POST, "email");
    $bio = filter_input(INPUT_POST, "bio");

    //CRIAR NOVO OBJETO DE USUÁRIO

    $user = new User();

    //PREENCHER OS DADOS DO USUÁRIO

    $userData->name = $name;
    $userData->lastname = $lastname;
    $userData->email = $email;
    $userData->bio = $bio;

    //UPLOAD DA IMAGEM

    if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

        $image = $_FILES["image"];
        $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
        $jpgArray= ["image/jpeg", "image/jpg"];

        //CHECAR O TIPO DE IMAGEM
        if(in_array($image["type"], $imageTypes)){

            //CHECAR SE É JPG
            if(in_array($image["type"], $jpgArray)){

                $imageFile = imagecreatefromjpeg($image["tmp_name"]);
            
                //IMAGEM É PNG
            } else {

                $imageFile = imagecreatefrompng($image["tmp_name"]);
            
            }

            $imageName = $user->imageGenerateName();

            imagejpeg($imageFile, "./img/users/" . $imageName, 100);

            $userData->image = $imageName;

        }else {

            $message->setMessage("Tipo inválido de imagem, insira png ou jpg", "error", "back");
        
        }

    } 
    

    $userDao->update($userData);


//ATUALIZAR SENHA DO USUÁRIO
} else if($type === "changepassword"){

        //RECEBER DADOS DO POST
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");
        
        //RESGATA DADOS DO USUÁRIO
        $userData = $userDao->verifyToken();
        $id = $userData->id;

        if($password == $confirmpassword){

            //CRRIAR UM NOVO OBJETO DE USUÁRIO
            $user = new User();

            $finalPassword = $user->generatePassword($password);

            $user->password = $finalPassword;

            $user->id = $id;

            $userDao->changePassword($user);
        } else{

            $message->setMessage("As senhas não são iguais", "error", "back");

        }


} else {

    $message->setMessage("Informações inválidas", "error", "index.php");

}


?>