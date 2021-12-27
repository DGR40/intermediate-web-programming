<?php
include("includes/init.php");
$nav_movie_class = "current_page";

//////////////////////// CREATE INITIAL PAGE //////////////////////////////////

// choose what movie should be displayed on it's own page
$movie_id = (int)trim($_GET['id']);
$url = "/movie?" . http_build_query(array('id' => $movie_id));

// get all info regarding this movie
$records = exec_sql_query(
  $db,
  "SELECT * FROM movies WHERE id = :id;",
  array(':id' => $movie_id)
)->fetchAll();
if (count($records) > 0) {
  $movie = $records[0];
} else {
  $movie = NULL;
}

// put info into vars to use in html below
$movie_name = htmlspecialchars($movie['movie_name']);
$movie_genre = htmlspecialchars($movie['genre']);
$movie_rating = htmlspecialchars($movie['rating']);
$movie_summary = htmlspecialchars($movie['summary']);
$movie_location = htmlspecialchars($movie['loc']);

$site_title = $movie_name;

//////////////////////////////// DELETE ENTRY /////////////////////////////////

if (isset($_POST['delete'])) {

  // get id to delete
  $del_id = $_GET['edit'];

  $db->beginTransaction();

  // delete movie
  $result = exec_sql_query(
    $db,
    "DELETE FROM movies WHERE (id = :movie_id)",
    array(
      ':movie_id' => $del_id
    )
  );

  // delete reference
  $result_2 = exec_sql_query(
    $db,
    "DELETE FROM movie_tags WHERE (movie_id = :movie_id)",
    array(
      ':movie_id' => $del_id
    )
  );

  $id_filename = 'public/uploads/documents/' . $del_id . '.jpeg';

  $result_3 = unlink($id_filename);



  if ($result && $result_2 && $result_3) {
    $DELETED = true;
    $db->commit();
    $site_title = "Deleted";
  }
}

//////////////////////////// END OF DELETE ENTRY //////////////////////////////

///////////////////////// TAG REMOVAL FROM ENTRY /////////////////////////////

if (isset($_POST['remove'])) {

  $db->beginTransaction();
  $removed_tag = $_POST['remove'];

  // get tag_id of tag name
  $removed_tag = exec_sql_query(
    $db,
    "SELECT * FROM tags WHERE (tag = :tag)",
    array(
      ':tag' => $removed_tag
    )
  )->fetchAll();

  $removed_tag_id = $removed_tag[0]['id'];

  $curr_movie_id = (int)trim($_GET['edit']);

  $result = exec_sql_query(
    $db,
    "DELETE FROM movie_tags WHERE (movie_tags.tag_id = :tag_id AND movie_tags.movie_id = :movie_id)",
    array(
      ':tag_id' => $removed_tag_id,
      ':movie_id' => $curr_movie_id
    )
  );

  if ($result) {
    $db->commit();
  }
}

//////////////////////// END OF TAG REMOVAL FROM ENTRY ///////////////////////

//////////////////////// END OF CREATE INITIAL PAGE ///////////////////////////

/////////////////////////// EDIT THE ENTRY /////////////////////////////////////

// user must be logged in to edit
if (is_user_logged_in()) {
  $edit_mode = false;
  $edit_authorization = true;

  // feedback CSS classes
  $name_feedback_class = 'hidden';
  $summary_feedback_class = 'hidden';
  $rating_feedback_class = 'hidden';
  $genre_feedback_class = 'hidden';
  $location_feedback_class = 'hidden';

  // check if user editing
  if (isset($_GET['edit'])) {
    $edit_mode = True;

    // if so set movie id to edit param
    $movie_id = (int)trim($_GET['edit']);
  }

  // choose right movie record
  if ($movie_id) {
    $records = exec_sql_query(
      $db,
      "SELECT * FROM movies WHERE id = :id;",
      array(':id' => $movie_id)
    )->fetchAll();
    if (count($records) > 0) {
      $movie = $records[0];
    } else {
      $movie = NULL;
    }
  }


  // only continue if movie valid
  if ($movie) {

    $site_title = 'Editing ' . $movie['movie_name'];

    // Check user permissions, must be user and owner to edit this movie
    if ($current_user['id'] == $movie['user_id']) {
      $edit_authorization = true;
    }
  }

  // Check if movie was edited
  if ($edit_authorization && isset($_POST['save'])) {
    $movie_name = trim($_POST['movie_name']); // untrusted
    $summary = trim($_POST['summary']); // untrusted
    $rating = trim($_POST['rating']);    // untrusted
    $genre = trim($_POST['genre']);    // untrusted
    $location = trim($_POST['loc']); // untrusted
    $tag = trim($_POST['tag']);  // untrusted

    // form starts valid
    $form_valid = True;

    // check if movie name is not empty
    if (empty($movie_name)) {
      $form_valid = False;
      $name_feedback_class = '';
    }

    // If form is valid, update movie record
    if ($form_valid) {

      exec_sql_query(
        $db,
        "UPDATE movies SET movie_name = :movie_name, user_id = :user_id, summary = :summary, rating = :rating, img_url = :img_url, genre = :genre, loc = :loc  WHERE (id = :id);",
        array(
          'id' => $movie_id,
          'user_id' => $current_user,
          'movie_name' => $movie_name,
          'summary' => $summary,
          'rating' => $rating,
          'img_url' => $upload_source,
          'genre' => $genre,
          'loc' => $location,
        )
      );

      ////////////////////////// ASSIGN NEW TAG TO NEW ENTRY ///////////////////

      $add_valid = true;

      $db->beginTransaction();

      // check if new tag is not empty
      if (empty($tag)) {
        $add_valid = false;
      }

      // check to see if new tag exists
      $records = exec_sql_query(
        $db,
        "SELECT * FROM tags WHERE (tag = :tag)",
        array(
          ':tag' => $tag
        )
      )->fetchAll();

      if (count($records) == 0) {
        $add_valid = false;
      }

      // get tag_id of tag name
      $added_tag = exec_sql_query(
        $db,
        "SELECT * FROM tags WHERE (tag = :tag)",
        array(
          ':tag' => $tag
        )
      )->fetchAll();

      $added_tag_id = $added_tag[0]['id'];

      // check if entry does not already have this proposed new tag
      $records = exec_sql_query(
        $db,
        "SELECT * FROM movie_tags WHERE (tag_id = :tag_id AND movie_id = :movie_id)",
        array(
          ':tag_id' => $added_tag_id,
          ':movie_id' => $movie_id
        )
      )->fetchAll();

      if (count($records) > 0) {
        $add_valid = false;
      }

      if ($add_valid) {

        $added_tag_id = $added_tag[0]['id'];

        // insert new entry into DB
        $result = exec_sql_query(
          $db,
          "INSERT INTO movie_tags (movie_id, tag_id) VALUES (:movie_id, :tag_id)",
          array(
            ':movie_id' => $movie_id,
            ':tag_id' => $added_tag_id
          )
        );

        if ($result) {
          $db->commit();
        }
      }
      //////////////////////////// END OF ASSIGN NEW TAG  //////////////////////

      ///////////////////// END OF EDIT ENTRY ///////////////////////////////////////

      // get updated movie
      $records = exec_sql_query(
        $db,
        "SELECT * FROM movies WHERE id = :id;",
        array(':id' => $movie_id)
      )->fetchAll();
      $movie = $records[0];

      // put info into vars to use in html below
      $movie_name = htmlspecialchars($movie['movie_name']);
      $movie_genre = htmlspecialchars($movie['genre']);
      $movie_rating = htmlspecialchars($movie['rating']);
      $movie_summary = htmlspecialchars($movie['summary']);
      $movie_location = htmlspecialchars($movie['loc']);
    }
  }

  // get information re movie
  $title = htmlspecialchars($movie['movie_name']);
  $url = "/movie?" . http_build_query(array('id' => $movie['id']));
  $edit_url = "/movie?" . http_build_query(array('edit' => $movie['id']));










  // put info into vars to use in html below
  $movie_name = htmlspecialchars($movie['movie_name']);
  $movie_genre = htmlspecialchars($movie['genre']);
  $movie_rating = htmlspecialchars($movie['rating']);
  $movie_summary = htmlspecialchars($movie['summary']);
  $movie_location = htmlspecialchars($movie['loc']);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title><?php echo htmlspecialchars($site_title); ?></title>

  <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all" />
</head>

<body>
  <?php include("includes/header.php") ?>


  <?php if ($DELETED) { ?>
    <div class="top-center-5">
      <p class="added-new"> Successfully Deleted Movie. <a href="/">Click here to return home.</a> </p>
    </div>
  <?php } else { ?>

    <div class="content-1">
      <div class="body-1">

        <div class="big-pic-container">
          <div class="big-pic">
            <img src="/public/uploads/documents/<?php echo htmlspecialchars($movie_id) . '.jpeg' ?>" class="movie-pic-big" alt="<?php echo htmlspecialchars($movie['movie_name']); ?>" />
          </div>
          <div class="top-link-1">
            <a href="<?php echo htmlspecialchars($movie['img_url']) ?>" class="big-link">img: <?php echo htmlspecialchars($movie['source']) ?>
            </a>
          </div>
        </div>


        <div class="info">
          <?php if (!$edit_mode) { ?>
            <div class="info-box">
              <p class="info-box-text">Title: <?php echo htmlspecialchars($movie_name) ?> </p>
              <p class="info-box-text">Genre: <?php echo htmlspecialchars($movie_genre) ?> </p>
              <p class="info-box-text">Summary: <?php echo htmlspecialchars($movie_summary) ?> </p>
              <p class="info-box-text">Rating: <?php echo htmlspecialchars($movie_rating) ?> / 5 stars</p>
              <p class="info-box-text">Location: <?php echo htmlspecialchars($movie_location) ?> </p>
              <p class="info-box-test">Tags:</p>
              <?php
              // query the database for the tag records
              $records = exec_sql_query(
                $db,
                "SELECT * FROM tags LEFT OUTER JOIN movie_tags ON tags.id = movie_tags.tag_id WHERE movie_tags.movie_id = $movie_id"
              )->fetchAll();

              if (count($records) > 0) { ?>
                <?php
                foreach ($records as $record) { ?>

                  <button class="tag-1" type="submit" name="tag" value=<?php echo htmlspecialchars($record['tag_id']) ?>>
                    <a class="link-1" href="/?<?php echo http_build_query(array('tag' => $record['tag_id'])); ?> ">
                      <?php echo htmlspecialchars($record['tag']); ?>
                    </a>
                  </button>

                <?php } ?>
                </form>
              <?php
              } ?>

              <?php if ($edit_authorization && !$edit_mode) { ?>
                <a href="<?php echo htmlspecialchars($edit_url); ?>" class='edit-button'>(Edit)</a>
              <?php } ?>

            <?php } ?>


            <?php if ($edit_mode) { ?>
              <form class="edit-box" action="<?php echo htmlspecialchars($url); ?>" method="post" enctype="multipart/form-data" novalidate>
                <h3>Edit a Movie Card</h3>
                <label class="edit-label"> Title:
                  <input type="text" name="movie_name" value="<?php echo htmlspecialchars($movie['movie_name']); ?>" class="edit-input" />
                </label>

                <label class="edit-label"> Genre:
                  <input name="genre" value="<?php echo htmlspecialchars($movie['genre']); ?>" class="edit-input" />
                </label>

                <label class="edit-label"> Summary:
                  <textarea name="summary" class="edit-input"> <?php echo htmlspecialchars($movie['summary']); ?> </textarea>
                </label>

                <label class="edit-label"> Rating:
                  <input name="rating" class="edit-input" value="<?php echo htmlspecialchars($movie['rating']); ?>" />
                </label>

                <label class="edit-label"> Location:
                  <input name="loc" class="edit-input" value="<?php echo htmlspecialchars($movie['loc']); ?>" />
                </label>

                <label class="edit-label"> Add Existing Tag:
                  <input name="tag" class="edit-input" />
                </label>
                <button type="submit" name="save" class="edit-save">Save</button>
              </form>

              <form class="del-tag-box" method="post" novalidate>
                <label class="delete-tag-label">Current Tags: Click Tag to Remove from Movie</label>
                <?php
                // query the database for the tag records
                $records = exec_sql_query(
                  $db,
                  "SELECT * FROM tags LEFT OUTER JOIN movie_tags ON tags.id = movie_tags.tag_id WHERE movie_tags.movie_id = $movie_id"
                )->fetchAll();

                if (count($records) > 0) { ?>
                  <?php
                  foreach ($records as $record) { ?>

                    <button class="tag-1" type="submit" name="remove" value=<?php echo htmlspecialchars($record['tag']) ?>>
                      <?php echo htmlspecialchars($record['tag']); ?>
                    </button>

                  <?php } ?>
              </form>

            <?php } ?>
            <form class="del-tag-box" method="post" novalidate>
              <label class="hidden">Click to Delete Entire Entry</label>
              <button class="tag-3" type="submit" name="delete" value=<?php echo htmlspecialchars($record['id']) ?>>
                Delete Movie
              </button>
            </form>
          <?php
            } ?>
            </div>
          <?php
        } ?>
        </div>
      </div>

      <?php include("includes/footer.php") ?>
</body>

</html>
