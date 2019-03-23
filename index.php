<?php
  namespace Cubo\Framework;

  error_reporting(E_ALL);
  ini_set('display_errors',1);

  require('.bootstrap.php');

  $app = new Application;

  $app->run();

  echo (string)$app->getSession();
die;

  //echo '<pre>'; print_r($app); echo '</pre>';

  $database = new Database(['driver'=>'json', 'source'=>__ROOT__.DS.'data']);

  $result = $database->find('Accesslevel');

  /*
  $database->insert('Accesslevel', ['_id'=>'4','name'=>'private','title'=>'Private','accesslevel'=>'1','status'=>'1']);
*/
  echo '<pre>'; print_r($result); echo '</pre>';

?>
