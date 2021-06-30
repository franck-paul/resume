<?php
header("Content-type: text/css; charset=UTF-8");

if (!empty($_GET['main_color'])) {
    $main_color = '#'.$_GET['main_color'];
} else {
  $main_color = '#'.'bd5d38';
}

?>
a {
  color: <?php echo $main_color; ?>;
}

a:hover, a:focus, a:active {
  color: <?php echo $main_color; ?>;
  
}

.social-icons .social-icon:hover {
  background-color: <?php echo $main_color; ?>;
}

.dev-icons .list-inline-item i:hover {
  color: <?php echo $main_color; ?>;
}

.bg-primary {
  background-color: <?php echo $main_color; ?> !important;
}

.text-primary {
  color: <?php echo $main_color; ?> !important;
}

a {
  color: <?php echo $main_color; ?>;
}

::selection {
  color: #fff;
  background-color: <?php echo $main_color; ?>;
}

#search .submit {
  background-color: <?php echo $main_color; ?>;
}

#search .submit:focus {
  background: <?php echo $main_color; ?>;
}

button.preview,
button.submit {
  background-color: <?php echo $main_color; ?>;
}