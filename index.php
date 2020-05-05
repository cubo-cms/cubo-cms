<?php
  namespace Cubo\Framework;

  error_reporting(E_ALL);
  ini_set('display_errors',1);

  require('.bootstrap.php');

  //$db = new Database;

  $app = new Application;

  $app->run();

  $session = $app->getSession();
?>
