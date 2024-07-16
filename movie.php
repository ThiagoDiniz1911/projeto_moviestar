<?php
require_once('templates/header.php');

//VERIFICA SE O USUÁRIO ESTÁ AUTENTICADO
require_once('models/Movie.php');
require_once('dao/MovieDAO.php');
require_once('dao/ReviewDAO.php');

//PEGAR O ID DO FILME
$id = filter_input(INPUT_GET, "id");

$movie;

$movieDao = new MovieDAO($conn, $BASE_URL);

$reviewDao = new ReviewDAO($conn, $BASE_URL);

if(empty($id)){

    $message->setMessage("O filme não foi encontrado!", "error", "index.php");
} else {

    $movie = $movieDao->findByID($id);

    //VERIFICA SE O FILME EXISTE
    if(!$movie){
        
        $message->setMessage("O filme não foi encontrado", "error", "index.php");

    }

}

//CHECAR SE O FILME TEM IMAGEM

if($movie->image == ""){
    $movie->image = "movie_cover.jpg";
}

//CHECAR SE O FILME É DO USUÁRIO

$userOwnsMovie = false;

if(!empty($userData)){
    if($userData->id === $movie->users_id){
        $userOwnsMovie = true;
    }

    //RESGATAR AS REVIEWS DO FILME
    $alreadyReviewed = $reviewDao->hasAlreadyReviewed($id, $userData->id);

}


//RESGATAR AS REVIEWS DO FILME
$movieReviews = $reviewDao->getMoviesReview($id);



?>

<div id="main-container" class="container-fluid">
    <div class="row">
        <div class="offset-md-1 col-md-6 movie-container">
            <h1 class="page-title"><?= $movie->title?></h1>
            <p class="movie-details">
                <span>Duração: <?= $movie->length?></span>
                <span class="pipe"></span>
                <span><?=$movie->category?></span>
                <span class="pipe"></span>
                <span class="fas fa-star"><?= $movie->rating ?></span>
            </p>
            <iframe width="560" height="315" src="<?=$movie->trailer?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            <p><?=$movie->description?></p>
        </div>
        <div class="col-md-4">
            <div class="movie-image-container" style="background-image: url('img/movies/<?=$movie->image?>')"></div>
        </div>
        <div class="offset-md-1 col-md-10" id="review-container">
            <h3 id="reviews-title">Avaliações:</h3>
            <!-- VERFIRICA SE HABILITA A REVIEW PARA O USUÁRIO OU NÃO -->
            <?php if(!empty($userData) && !$userOwnsMovie && !$alreadyReviewed): ?>
            <div class="col-md-12" id="reviw-form-container">
                <h4>Envie sua avaliação:</h4>
                <p class="page-description">Preencha o formulário com a nota e o comentáio sobre o filme</p>
                <form action="review_process.php" method="post" id="review-form">
                    <input type="hidden" name="type" value="create">
                    <input type="hidden" name="movies_id" value="<?=$movie->id?>">
                    <div class="form-group">
                        <label for="rating">Nota do filme:</label>
                        <select name="rating" id="rating" class="form-control">
                            <option value="">Selecione</option>
                            <option value="10">10</option>
                            <option value="9">9</option>
                            <option value="8">8</option>
                            <option value="7">7</option>
                            <option value="6">6</option>
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                    <div class="form-gruop">
                        <label for="review">Seu comentário</label>
                        <textarea name="review" id="review" rows="3" class="form-control" placeholder="O que você achou do filme?"></textarea>
                    </div>
                    <input type="submit" class="btn card-btn" value="Enviar comentário" >
                </form>
            </div>
            <?php endif; ?>
            <!--COMENTÁRIOS-->
            <?php foreach($movieReviews as $review):?>
                <?php require("templates/user_review.php"); ?>
            <?php endforeach; ?>
            <?php if(count($movieReviews) == 0): ?>
                <p class="empty-list">Não há comentários para este filme ainda</p>    
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once('templates/footer.php')
?>