<?php

require_once("globals.php");
require_once("db.php");
require_once("models/Movie.php");
require_once("models/Message.php");
require_once("dao/UserDAO.php");
require_once("dao/MovieDAO.php");

$message = new Message($BASE_URL);
$userDao = new UserDAO($conn, $BASE_URL);
$movieDao = new MovieDAO($conn, $BASE_URL);

// RESGATA O TIPO DO FORMULÁRIO
$type = filter_input(INPUT_POST, "type");

//RESGATA DADOS DO USUÁRIO
$userData = $userDao->verifyToken();

if($type === "create"){

    //RECEBER OS DADOS DOS INPUTS
    $title = filter_input(INPUT_POST, "title");
    $description = filter_input(INPUT_POST, "description");
    $trailer = filter_input(INPUT_POST, "trailer");
    $category = filter_input(INPUT_POST, "category");
    $length = filter_input(INPUT_POST, "length");

    $movie = new Movie();

    //VALIDAÇÃO MÍNIMA DE DADOS
    if(!empty($title) && !empty($description) && !empty($category)){

        $movie->title = $title;
        $movie->description = $description;
        $movie->trailer = $trailer;
        $movie->category = $category;
        $movie->length = $length;
        $movie->users_id = $userData->id;

        //UPLOAD IMAGEM DO FILME
        if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

            $image = $_FILES["image"];
            $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
            $jpgArray = ["image/jpeg", "image/jpg"];

            //CHECANDO TIPO DA IMAGEM
            if(in_array($image["type"], $imageTypes)){

                //CHECA SE IMAGEM É JPG
                if(in_array($image["type"], $jpgArray)){

                    $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                    
                } else {

                    $imageFile = imagecreatefrompng($image["tmp_name"]);

                }

                $imageName = $movie->imageGenerateName();

                imagejpeg($imageFile, "img/movies/" . $imageName, 100);

                $movie->image = $imageName;

            } else {

                $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");

            }


        }

        $movieDao->create($movie);

    } else {

        $message->setMessage("Você precisa adicionar pelo menos título, descrição e categoria!", "error", "back");

    }


} else if($type === "delete"){

    //RECEBE OS DADOS DO FORMULÁRIO

    $id = filter_input(INPUT_POST, "id");

    $movie = $movieDao->findById($id);

    if($movie){

        //VERIFICAR SE O FILME É DO USUÁRIO
        if($movie->users_id === $userData->id){

            $movieDao->destroy($movie->id);

        } else{

            $message->setMessage("Informações inválidas", "error", "index.php");

        }

    } else{

        $message->setMessage("Informações inválidas", "error", "index.php");

    }


} else if($type === "update"){
    
    //RECEBER OS DADOS DOS INPUTS
    $title = filter_input(INPUT_POST, "title");
    $description = filter_input(INPUT_POST, "description");
    $trailer = filter_input(INPUT_POST, "trailer");
    $category = filter_input(INPUT_POST, "category");
    $length = filter_input(INPUT_POST, "length");
    $id = filter_input(INPUT_POST, "id");
    
    $movieData = $movieDao->findById($id);

    //VERIFICA SE ENCONTROU O FILME

    if($movieData){

        //VERIFICAR SE O FILME É DO USUÁRIO
        if($movieData->users_id === $userData->id){


             //VALIDAÇÃO MÍNIMA DE DADOS
            if(!empty($title) && !empty($description) && !empty($category)){

                //EDIÇÃO DO FILME
                $movieData->title = $title;
                $movieData->description = $description;
                $movieData->trailer = $trailer;
                $movieData->category = $category;
                $movieData->length = $length;

                //UPLOAD IMAGEM DO FILME
        if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

            $image = $_FILES["image"];
            $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
            $jpgArray = ["image/jpeg", "image/jpg"];

            //CHECANDO TIPO DA IMAGEM
            if(in_array($image["type"], $imageTypes)){

                //CHECA SE IMAGEM É JPG
                if(in_array($image["type"], $jpgArray)){

                    $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                    
                } else {

                    $imageFile = imagecreatefrompng($image["tmp_name"]);

                }

                $movie = new Movie();

                $imageName = $movie->imageGenerateName();

                imagejpeg($imageFile, "img/movies/" . $imageName, 100);

                $movieData->image = $imageName;

            } else {

                $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");

            }


        }

        $movieDao->update($movieData);

            } else {

                $message->setMessage("Você precisa adicionar pelo menos título, descrição e categoria!", "error", "back");

            }

        } else{

            $message->setMessage("Informações inválidas", "error", "index.php");

        }

    } else{

        $message->setMessage("Informações inválidas", "error", "index.php");

    }

}else {

    $message->setMessage("Informações inválidas", "error", "index.php");

}

?>