<?php
include("includes/init.php");
$title = "Theatres";
$nav_movie_class = "current_page";
$sql_select_query = 'SELECT * FROM theatres';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all" />
  <title>Galaxy Movies</title>
</head>

<body>
  <?php include("includes/header.php") ?>
  <div class="content">
    <div class="center">
      <div class="cards">
        <div class="card-container-2">
          <div class="top">
            <h1 class="title-1">Brighton</h1>
          </div>
          <div class="middle">
            <img src="/public/images/11.jpeg" class="movie-pic-1" alt="Brighton" />
          </div>
          <div class="bottom-1">
            <a href="https://www.insidetucsonbusiness.com/news/new-movie-theater-brings-hollywood-magic-to-eastside-tucson/article_bf6ae862-dee9-11e8-8a54-bf0d608c3c96.html" class="top-link-5">insidetucsonbusiness</a>
            <p class="address">
              123 North Quarry Street, Brighton NY, 14610
            </p>
          </div>
        </div>

        <div class="card-container-2">
          <div class="top">
            <h1 class="title-1">Churchville</h1>
          </div>
          <div class="middle">
            <img src="/public/images/12.jpeg" class="movie-pic-1" alt="Brighton" />
          </div>
          <div class="bottom-1">
            <a href="https://www.mjindependent.com/music-and-arts/2020/8/14/i1hx985radxfjfuiktc4temmvd8zmi" class="top-link-5">mjindependent</a>
            <p class="address">
              456 Eddy Street, Churchville NY, 14428
            </p>
          </div>
        </div>

        <div class="card-container-2">
          <div class="top">
            <h1 class="title-1">Fairport</h1>
          </div>
          <div class="middle">
            <img src="/public/images/13.jpeg" class="movie-pic-1" alt="Brighton" />
          </div>
          <div class="bottom-1">
            <a href="http://cinematreasures.org/theaters/39591" class="top-link-5">cinematreasures</a>
            <p class="address">
              789 Cook Street, Fairport NY, 14450
            </p>
          </div>
        </div>


      </div>
    </div>
  </div>

  <?php include("includes/footer.php") ?>


</body>

</html>
