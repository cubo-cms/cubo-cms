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
  * @description    Set framework class allows handling property sets. This
  *                 class is used throughout the framework to store parameters,
  *                 properties, configurations, object data or any other data
  *                 that needs to be stored for a class.
  **/
  namespace Cubo\Framework;

  final class Set {
    private $params;

    // Upon construct load the property set
    public function __construct($params = null) {
      $this->load($params);
    }

    // Short method to get property
    public function __get($property) {
      return $this->get($property);
    }

    // Short method to validate presence of property
    public function __isset($property) {
      return $this->exists($property);
    }

    // Short method to set property
    public function __set($property, $value) {
      $this->set($property, $value);
    }

    // Allow returning property set as JSON
    public function __toString() {
      return json_encode($this->params, JSON_PRETTY_PRINT);
    }

    // Delete property
    public function delete($property) {
      unset($this->params->$property);
    }

    // Validate presence of property
    public function exists($property) {
      return isset($this->params->$property);
    }

    // Get property
    public function get($property, $default = null) {
      if($this->exists($property)) {
        $value = $this->params->$property;
        return self::isVariable($value)? $this->get($value, $default): $value;
      } else {
        return $default;
      }
    }

    // Return property set
    public function getAll() {
      return $this->params;
    }

    // Load the property set
    public function load($params = null) {
      if(empty($params)) {
        // Return empty object
        return $this->params = (object)[];
      } elseif(is_string($params)) {
        // Load property set from file
        $this->loadFromFile($params);
      } elseif(is_array($params)) {
        // Convert array to object
        return $this->params = (object)$params;
      } elseif(is_object($params)) {
        // Return object
        return $this->params = $params;
      } else {
        // Return empty object
        return $this->params = (object)[];
      }
    }

    // Load the property set from a file
    public function loadFromFile($file, $ext = 'json') {
      if(!file_exists($file)) {
        // Attempt to add extension or return empty object
        $this->params = $ext? $this->loadFromFile($file.'.'.$ext, false): (object)[];
      } elseif('json' == pathinfo($file, PATHINFO_EXTENSION)) {
        // Load from JSON
        return $this->params = json_decode(file_get_contents($file));
      } else {
        // Return empty object
        return $this->params = (object)[];
      }
    }

    // Merge another property set with this one
    public function merge($params) {
      if(is_array($params))
        $this->params = (object)array_merge((array)$this->params, $params);
      else
        $this->params = (object)array_merge((array)$this->params, (array)$params->getAll());
    }

    // Set property
    public function set($property, $value) {
      is_null($this->params) && $this->params = (object)[];
      return $this->params->$property = $value;
    }

    // Static method to determine if the property is a variable
    public static function isVariable($property) {
      if(!is_string($property))
        return false;
      if(preg_match("/^\{{2}(.*)\}{2}$/", $property, $match) && !empty($match[1]))
        return $match[1];
      else {
        return false;
      }
    }
  }
?>
