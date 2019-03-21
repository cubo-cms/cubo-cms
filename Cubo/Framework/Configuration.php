<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Framework
  * @version        0.0.1
  * @copyright      2019 Cubo CMS <https://cubo-cms.com/COPYRIGHT.md>
  * @license        MIT license <https://cubo-cms.com/LICENSE.md>
  * @author         papiando
  * @link           <https://github.com/cubo-cms/cubo-cms>
  *
  * @description    Configuration framework class is a helper class to enable
  *                 passing core configuration data between framework classes.
  *                 The most essential data is [config], which is loaded from
  *                 the default location </.config/config.json>.
  **/
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
