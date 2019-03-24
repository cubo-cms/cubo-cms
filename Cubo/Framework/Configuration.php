<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Framework
  * @version        0.0.2
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

  define('ACCESSLEVEL_ANY', -1);
  define('ACCESSLEVEL_GUEST', 2);
  define('ACCESSLEVEL_PRIVATE', 4);
  define('ACCESSLEVEL_PUBLIC', 1);
  define('ACCESSLEVEL_REGISTERED', 3);
  define('ACCESSLEVEL_RESTRICTED', 5);
  define('CATEGORY_ANY', -1);
  define('CATEGORY_ROOT', 0);
  define('CATEGORY_UNDEFINED', 1);
  define('ROLE_ADMINISTRATOR', 6);
  define('ROLE_ANY', -1);
  define('ROLE_AUTHOR', 2);
  define('ROLE_EDITOR', 3);
  define('ROLE_MANAGER', 5);
  define('ROLE_PUBLISHER', 4);
  define('ROLE_USER', 1);
  define('STATUS_ANY', -1);
  define('STATUS_ARCHIVED', 4);
  define('STATUS_PUBLISHED', 1);
  define('STATUS_TRASHED', 3);
  define('STATUS_UNPUBLISHED', 2);
  define('USER_ADMIN', 2);
  define('USER_ANY', -1);
  define('USER_NOBODY', 0);
  define('USER_SYSTEM', 1);

  final class Configuration {
    private static $params;
    private static $path;

    // Upon construct load configuration
    public function __construct($file = 'config') {
      self::load(basename($file, '.json'), $file);
    }

    // Allow returning parameters as JSON
    public function __toString() {
      return (string)$this->params;
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
