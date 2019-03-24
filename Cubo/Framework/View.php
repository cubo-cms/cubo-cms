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
  * @description    View framework class contains base methods for all
  *                 object views. Object views extend this class
  *                 and could define additional methods, if needed.
  *                 Views render the retrieved data to visible output in
  *                 different formats.
  **/
  namespace Cubo\Framework;

  class View {
    protected $params;

    /**
      * @section    Magic methods
      **/

    // Upon construct save the router
    public function __construct() {
    }

    // Allow returning parameters as JSON
    public function __toString() {
      return (string)$this->params;
    }

    /**
      * @section    Basic public methods
      **/

    // Pass calling object
    public function calledBy($caller = null) {
      return $caller? $this->caller = $caller: $this->caller;
    }

    // Determine if method exists
    public function methodExists($method) {
      return method_exists($this, $method);
    }

    /**
      * @section    Standard controller methods
      **/

    // Method: default
    public function default($data) {
      return $data;
    }

    /**
      * @section    Static methods
      **/

    // Return class name without namespace
    public static function class() {
      return strtolower(basename(str_replace('\\', '/', static::class)));
    }

    // Return class name with namespace
    public static function className($view = null) {
      return $view? (__CUBO__ == explode('\\', $view)[0]? $view: __CUBO__.'\\View\\'.ucfirst($view)): static::class;
    }

    // Static method to determine if view exists
    public static function exists($view) {
      return class_exists(self::className($view));
    }
  }
?>
