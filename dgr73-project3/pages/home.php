<?php
include("includes/init.php");
$title = "Home";
$nav_movie_class = "current_page";
$sql_select_query = 'SELECT * FROM movies';
$url = '/';

//////////////////// UPLOAD NEW ENTRY /////////////////////////////////////////

// initialize feedback classes
$name_feedback_class = "hidden";
$genre_feedback_class = "hidden";
$summary_feedback_class = "hidden";
$rating_feedback_class = "hidden";
$location_feedback_class = "hidden";
$file_feedback_class = "hidden";
$source_feedback_class = 'hidden';

// upload fields
$upload_title = NULL;
$upload_genre = NULL;
$upload_summary = NULL;
$upload_rating = NULL;
$upload_location = NULL;
$upload_url = NULL;
$upload_filename = NULL;
$upload_ext = NULL;

// sticky values
// file upload not sticky
$sticky_name = '';
$sticky_genre = '';
$sticky_summary = '';
$sticky_rating = '';
$sticky_location = '';
$sticky_url = '';
$sticky_source = '';

// Set maximum file size for uploaded files.
// 1 MB = 1000000 bytes
define("MAX_FILE_SIZE", 1000000);

if (isset($_POST['upload'])) {
  $upload_title = trim($_POST['movie_name']); // untrusted
  $upload_genre = trim($_POST['genre']); // untrusted
  $upload_summary = trim($_POST['summary']); // untrusted
  $upload_rating = trim($_POST['rating']); // untrusted
  $upload_location = trim($_POST['loc']); // untrusted
  $upload_url = trim($_POST['url']); // untrusted
  $upload_source = trim($_POST['source']); // untrusted

  // get info about uploaded file
  $upload = $_FILES['file'];

  // assume form is valid

  $rating = $upload_rating;

  $form_valid = True;

  // file is required
  if ($upload['error'] == UPLOAD_ERR_OK) {
    // get name of uploaded file without path
    $upload_filename = basename($upload['name']);

    // get file ext
    $upload_ext = strtolower(pathinfo($upload_filename, PATHINFO_EXTENSION));
  } else {
    $file_feedback_class = '';
    $form_valid = false;
  }

  // VERIFY ALL UPLOAD INPUTS //

  // name is required
  if (empty($upload_title)) {
    $form_valid = False;
    $name_feedback_class = '';
  }

  // name cannot already exist in db
  $records = exec_sql_query(
    $db,
    "SELECT movie_name FROM movies WHERE (movie_name = :name)",
    array(
      ':name' => $upload_title
    )
  )->fetchAll();

  if (count($records) > 0) {
    $form_valid = false;
    $name_feedback_class = '';
  }

  // genre is required
  if (empty($upload_genre)) {
    $form_valid = False;
    $genre_feedback_class = '';
  }

  // summary is required
  if (empty($upload_summary)) {
    $form_valid = False;
    $summary_feedback_class = '';
  }

  // rating is required
  if (empty($upload_rating)) {
    $form_valid = False;
    $rating_feedback_class = '';
  }

  $upload_rating = (int)$upload_rating;
  // rating must be an int and between 0 and 5
  if (is_int($upload_rating)) {
    if ($upload_rating <= 0 || $upload_rating > 5) {
      $form_valid = False;
      $rating_feedback_class = '';
    }
  } else {
    $form_valid = False;
    $rating_feedback_class = '';
  }

  // location is required
  if (empty($upload_location)) {
    $form_valid = False;
    $location_feedback_class = '';
  }

  // location must exist
  $records = exec_sql_query(
    $db,
    "SELECT loc FROM movies WHERE (loc = :loc)",
    array(
      ':loc' => $upload_location
    )
  )->fetchAll();

  if (count($records) == 0) {
    $form_valid = false;
    $location_feedback_class = '';
  }

  // source is required
  if (empty($upload_source)) {
    $form_valid = False;
    $source_feedback_class = '';
  }

  if ($form_valid == 1) {
    $db->beginTransaction();

    // insert new entry into DB
    $result = exec_sql_query(
      $db,
      "INSERT INTO movies (user_id, movie_name, genre, summary, rating, loc, file_name, file_ext, img_url, source) VALUES (:user_id, :movie_name, :genre,  :summary, :rating, :loc, :file_name, :file_ext, :img_url, :source)",
      array(
        ':user_id' => $current_user['id'],
        ':movie_name' => $upload_title,
        ':genre' => $upload_genre,
        ':summary' => $upload_summary,
        ':rating' => $upload_rating,
        ':loc' => $upload_location,
        ':file_name' => $upload_filename,
        ':file_ext' => $upload_ext,
        ':img_url' => $upload_url,
        ':source' => $upload_source
      )
    );

    if ($result) {
      $record_id = $db->lastInsertId('id');
      $id_filename = 'public/uploads/documents/' . $record_id . '.jpeg';
      move_uploaded_file($upload['tmp_name'], $id_filename);
      $ADDED = true;
    }
    $db->commit();
  } else {
    $sticky_name = $upload_title;
    $sticky_genre = $upload_genre;
    $sticky_summary = $upload_summary;
    $sticky_rating = $upload_rating;
    $sticky_location = $upload_location;
    $sticky_url = $upload_url;
    $sticky_source = $upload_source;
  }
}
//////////////////////////END OF UPLOAD NEW ENTRY /////////////////////////////

////////////////////////////// FILTER BY TAG  /////////////////////////////////
if (isset($_GET["tag"])) {
  // get which tag is selected
  $tag_id = $_GET['tag'];

  // create select query that finds all movies of given tag
  $sql_select_query = "SELECT * FROM movies LEFT OUTER JOIN movie_tags ON movies.id = movie_tags.movie_id WHERE (movie_tags.tag_id = $tag_id)";

  $records = exec_sql_query(
    $db,
    "SELECT tag FROM tags WHERE (id = :id)",
    array(
      ':id' => $tag_id
    )
  )->fetchAll();

  $selected_tag = $records[0][0];
} else {
  // selects all movies if no tags
  $sql_select_query = "SELECT * FROM movies";

  // sets description to all
  $selected_tag = 'None';
}
////////////////////////////// END FILTER /////////////////////////////////////

////////////////////////////// ADD NEW TAG ////////////////////////////////////

// check if new tag is added
if (isset($_GET['add'])) {
  $add_valid = true;
  $tag_name = trim($_GET['add']);

  $db->beginTransaction();

  // check if new tag is not empty
  if (empty($tag_name)) {
    $add_feedback_class = '';
    $add_valid = false;
  }

  // check to see if new tag is unique
  $records = exec_sql_query(
    $db,
    "SELECT * FROM tags WHERE (tag = :tag)",
    array(
      ':tag' => $tag_name
    )
  )->fetchAll();

  if (count($records) > 0) {
    $add_valid = false;
  }

  if ($add_valid) {
    // insert new entry into DB
    $result = exec_sql_query(
      $db,
      "INSERT INTO tags (tag) VALUES (:tag)",
      array(
        ':tag' => $tag_name
      )
    );

    if ($result) {
      $db->commit();
      $TAG_ADDED = true;
    }
  }
}
/////////////////////////// END OF ADD NEW TAG /////////////////////////////////

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
      <form class="top-center" method="get">
        <?php
        // query the database for the tag records
        $records = exec_sql_query(
          $db,
          'SELECT * FROM tags'
        )->fetchAll();

        if (count($records) > 0) { ?>
          <?php
          foreach ($records as $record) { ?>

            <button class="tag" type="submit" name="tag" value=<?php echo htmlspecialchars(($record['id'])) ?>>
              <?php echo htmlspecialchars(($record['tag'])); ?>
            </button>

          <?php } ?>
      </form>
    <?php
        } ?>


    <?php if (is_user_logged_in()) { ?>
      <div class="top-center-1">
        <form method="get" class="add-input">
          <label class="top-center-1-within"><input type="text" name="add"><button class="tag-2" type="submit" value=<?php $new_tag ?>>Add Tag</button></input> </label>
        </form>
      </div>
    <?php } ?>

    <?php if ($ADDED) { ?>
      <div class="top-center-4">
        <p class="added-new"> Successfully Added New Movie </p>
      </div>
    <?php } ?>

    <?php if ($TAG_ADDED) { ?>
      <div class="top-center-4">
        <p class="added-new"> Successfully Added New Tag </p>
      </div>
    <?php } ?>

    <div class="top-center-4">
      <p class="added-new"> Tag Selected: <?php echo htmlspecialchars($selected_tag) ?> </p>
    </div>

    <div class="cards">

      <?php if (is_user_logged_in()) { ?>
        <form class="upload" method="post" enctype="multipart/form-data" novalidate>
          <div class="upload-middle">

            <h2>Add a New Movie</h2>

            <div class="upload-container">
              <label class="edit-label"> Title:
                <input type="text" name="movie_name" value="<?php echo htmlspecialchars($sticky_name); ?>" class="edit-input" required />
              </label>

              <p class="<?php echo $name_feedback_class ?> feedback">Please enter a new movie name</p>

              <label class="edit-label"> Genre:
                <input name="genre" value="<?php echo htmlspecialchars($sticky_genre); ?>" class="edit-input" required />
              </label>

              <p class="<?php echo $genre_feedback_class ?> feedback">Please enter a valid genre</p>

              <label class="edit-label"> Summary:
                <textarea name="summary" class="edit-input" required> <?php echo htmlspecialchars($sticky_summary); ?> </textarea>
              </label>

              <p class="<?php echo $summary_feedback_class ?> feedback">Please enter a summary</p>

              <label class="edit-label"> Rating (out of 5):
                <input name="rating" class="edit-input" value="<?php echo htmlspecialchars($sticky_rating); ?>" required />
              </label>

              <p class="<?php echo $rating_feedback_class ?> feedback">Please enter a rating out of 5</p>

              <label class="edit-label"> Location:
                <input name="loc" class="edit-input" value="<?php echo htmlspecialchars($sticky_location); ?>" required />
              </label>

              <p class="<?php echo $location_feedback_class ?> feedback">Please enter a valid location</p>

              <!-- Enforces Max file size of 1 MB -->
              <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />

              <label class="edit-label"> File Upload (must be jpeg):
                <input name="file" type="file" class="edit-input" accept=".jpeg" required />
              </label>

              <p class="<?php echo $file_feedback_class ?> feedback">Please upload a valid jpeg file</p>

              <label class=" edit-label"> File Source:
                <input name="source" class="edit-input" value="<?php echo htmlspecialchars($sticky_source) ?>" />
              </label>

              <p class="<?php echo $source_feedback_class ?> feedback">Please enter the source of the file</p>

              <label class=" edit-label"> File Source URL (optional):
                <input name="url" class="edit-input" value="<?php echo htmlspecialchars($sticky_url); ?>" />
              </label>

              <button type="submit" name="upload" class="edit-save">Save</button>
            </div>
          </div>
        </form>
      <?php } ?>
      <?php
      // query the database for the movie records
      $records = exec_sql_query(
        $db,
        $sql_select_query
      )->fetchAll();

      // Only show the movie gallery if we have records to display.
      if (count($records) > 0) { ?>
        <?php
        foreach ($records as $record) {

          $curr_mov_name = "'" . $record['movie_name'] . "'";

          ////// WEIRD THING TO CORRECT THE WRONG MOVIE ID ////////////

          $temp_movie = exec_sql_query(
            $db,
            "SELECT * from movies WHERE (movies.movie_name = $curr_mov_name)"
          )->fetchAll();

          $usable_id = $temp_movie[0]['id'];
        ?>

          <div class="card-container">


            <div class="top">
              <p class="sneaky"> </p>
              <h1 class="title"><?php echo htmlspecialchars($record['movie_name']); ?></h1>
            </div>
            <div class="top-link">
              <a href="<?php echo htmlspecialchars($record['img_url']) ?>" aria-text="image">img: <?php echo htmlspecialchars($record['source']) ?>
              </a>
            </div>
            <div class="middle">
              <a href="/movie?<?php echo http_build_query(array('id' => $usable_id)); ?> ">
                <img src="/public/uploads/documents/<?php echo htmlspecialchars($usable_id) . '.jpeg' ?>" class="movie-pic" alt="<?php echo htmlspecialchars($record['movie_name']); ?>" />
              </a>
            </div>
            <div class="bottom">
              <div class="genre"><?php echo htmlspecialchars($record['genre']); ?></div>
              <div class="stars">
                <?php
                $count = (int)$record['rating'];
                for ($x = 0; $x < $count; $x++) {
                ?>
                  <img src="public/images/star.jpeg" class="star" alt="star" /> <?php                                                   } ?>
              </div>
            </div>
          </div>


        <?php
        } ?>

      <?php } else { ?>
        <div class="top-center-4">
          <p class="added-new"> No Movies Found </p>
        </div>
      <?php } ?>
    </div>
    </div>
  </div>
  <?php include("includes/footer.php") ?>


</body>

</html>
