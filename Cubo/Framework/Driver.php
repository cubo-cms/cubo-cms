<?php
  namespace Cubo\Framework;

  class Driver {
    protected static $driver;
    protected $params;

    // Return driver object
    public static function get($driver, $source) {
      $driverClass = __CUBO__.'\\Driver\\'.ucfirst($driver);
      return self::$driver ?? self::$driver = new $driverClass($driver, $source);
    }

    // Static method to determine if driver's source exists
    public static function sourceExists($driver, $source) {
      if(self::exists($driver)) {
        $driverClass = __CUBO__.'\\Driver\\'.ucfirst($driver);
        self::$driver ?? self::$driver = new $driverClass($driver, $source);
        return self::$driver::sourceExists($driver, $source);
      }
      return false;
    }

    // Return class name
    public static function className($driver = null) {
      return $driver? (__CUBO__ == explode('\\', $driver)[0]? $driver: __CUBO__.'\\Driver\\'.ucfirst($driver)): __CLASS__;
    }

    // Static method to determine if driver exists
    public static function exists($driver) {
      return class_exists(self::className($driver));
    }
  }
?>
