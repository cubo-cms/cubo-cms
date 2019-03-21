<?php
  // Define global constants
  defined('DS') || define('DS',DIRECTORY_SEPARATOR);
  defined('__ROOT__') || define('__ROOT__',$_SERVER['DOCUMENT_ROOT']);
  defined('__CUBO__') || define('__CUBO__','Cubo');
  defined('__BASE__') || define('__BASE__',sprintf("%s://%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['HTTP_HOST']));
  defined('__VERSION') || define('__VERSION__','0.0.1');


  // Auto-register classes
  spl_autoload_register(function($class) {
    // Use namespace as path
    if(file_exists(__ROOT__.DS.str_replace('\\', DS, $class).'.php'))
      require_once __ROOT__.DS.str_replace('\\', DS, $class).'.php';
  });
?>
