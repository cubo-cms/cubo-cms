<?php
  namespace Cubo\Framework;

  final class Configuration {
    private static $params;
    private static $path;

    // Upon construct load configuration
    public function __construct($file = 'config') {
      self::load(basename($file, '.json'), $file);
    }

    // Get configuration
    public static function get($property) {
      return self::$params->get($property);
    }

    // Load configuration from file
    public static function load($property, $file) {
      // Create parameter set if it does not exist
      is_null(self::$params) && self::$params = new Set;
      // If no path provided prepend it
      if(is_string($file) && false === strpos($file, DS))
        $file = self::path().$file;
      // Save configuration and return
      return self::$params->set($property, new Set($file));
    }

    // Set or get configuration path
    public static function path($path = null) {
      return self::$path = $path ?? self::$path ?? __ROOT__.DS.'.config'.DS;
    }
  }
?>
