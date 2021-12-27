  <?php if (!is_user_logged_in()) { ?>
    <div class="login-bar">
      <?php
      echo_login_form($url, $session_messages); ?>
    </div>

  <?php } ?>

  <?php if (is_user_logged_in() && !$edit_mode) { ?>
    <a href="<?php echo logout_url(); ?>" class="sign-out">Sign Out</a>
  <?php } ?>
  <div class="header">
    <a href="/">
      <h1 class='header-text'>Galaxy Theatres</h1>
    </a>
  </div>
  <div class='subtext'>
    <p>An out of this world experience!
  </div>
  <div class="navbar">
    <a href="/" class="nav-link">Movies</a>
    <a href="/theatres" class="nav-link">Theatres</a>
  </div>
