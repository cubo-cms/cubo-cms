<?php
  namespace Cubo\Framework;

  error_reporting(E_ALL);
  ini_set('display_errors',1);

  require('.bootstrap.php');

  //$db = new Database;

  $app = new Application;

  $app->run();

  $session = $app->getSession();
  echo('<p>Started: '.gmdate('d-m-Y H:i:s',$session->get('started')).'</p>');
  echo('<p>Last access: '.gmdate('d-m-Y H:i:s',$session->get('lastAccessed')).'</p>');
  echo('<p>Expires: '.gmdate('d-m-Y H:i:s',$session->get('expires')).'</p>');
  echo('<p>User: '.json_encode($session->get('user')).'</p>');
die;

  //echo '<pre>'; print_r($app); echo '</pre>';

  $database = new Database(['driver'=>'json', 'source'=>__ROOT__.DS.'data']);

  $result = $database->find('Accesslevel');

  /*
  $database->insert('Accesslevel', ['_id'=>'4','name'=>'private','title'=>'Private','accesslevel'=>'1','status'=>'1']);
*/
  echo '<pre>'; print_r($result); echo '</pre>';

?>
