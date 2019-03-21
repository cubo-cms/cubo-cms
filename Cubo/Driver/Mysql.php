<?php
  namespace Cubo\Driver;
  use Cubo\Framework\Driver;

  class Mysql extends Driver {
    // Determine if source exists
    public static function sourceExists($driver, $source) {
      return true;
    }
  }
?>
