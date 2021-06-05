<?php
header("Content-type: text/css; charset=UTF-8");

if (!empty($_GET['main_color'])) {
    $main_color = '#'.$_GET['main_color'];
} else {
  $main_color = '#'.'BD5D38';
}

?>
a {
  color: <?php echo $main_color; ?>;
}

a:hover, a:focus, a:active {
  color: <?php echo $main_color; ?>;
  filter: brightness(65%);
}

.social-icons a:hover {
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
  color: #fff;
  vertical-align: top;
  font-variant: small-caps;
  height: 2em;
  -webkit-transition: all .3s ease-in-out;
  -moz-transition: all .3s ease-in-out;
  transition: all .3s ease-in-out;
  font-weight: bold;
}

#search .submit:focus {
  background: <?php echo $main_color; ?>;
}

button.preview,
button.submit {
  border: none;
  padding: 5px 10px;
  text-transform: uppercase;
  -webkit-transition: all .3s ease-in-out;
  -moz-transition: all .3s ease-in-out;
  transition: all .3s ease-in-out;
  color: #fff;
  background-color: <?php echo $main_color; ?>;
  font-weight: bold;
}