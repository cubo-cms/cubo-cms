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
  * @description    Model framework class contains base methods for all
  *                 object models. Object models extend this class and
  *                 generally do not add more methods.
  *                 Models retrieve or save data from the data sources and
  *                 send the result back to the controller.
  **/
  namespace Cubo\Framework;
  use Cubo\Framework\Configuration;
  use Cubo\Framework\Database;
  use Cubo\Framework\Error;

  class Model {
    protected $caller;          // Pointer to calling object
    protected $data;            // Data set
    protected $database;        // Database configuration
    protected $params;          // Parameter set

    // Upon construct connect to data source
    public function __construct() {
      $this->connect();
    }

    // Allow returning parameters as JSON
    public function __toString() {
      return (string)$this->params;
    }

    // Pass calling object
    public function calledBy($caller) {
      return $caller? $this->caller = $caller: $this->caller;
    }

    // Connect to data source
    public function connect() {
      // Load database configuration
      $this->params = Configuration::load('database', Configuration::get('config')->get('database'));
      try {
        $sources = $this->params->get('sources', (object)[]);
        if(isset($sources->{$this->getClass()})) {
          $this->database = new Database($sources->{$this->getClass()});
        } else {
          throw new Error('datasource-does-not-exist');
        }
      } catch(Error $error) {
        $error->render();
      }
    }

    // Method: getById
    public function getById($id, $properties = null, $options = null) {
      // Retrieve object from class name
      $object = $this->getClass();
      // Construct options
      is_object($options) || $options = new Set($options);
      if($options->exists('filter'))
        $options->set('filter', array_merge($options->get('filter'), ['name'=>$name]));
      else
        $options->merge(['filter'=>['name'=>$name]]);
      // Find object
      $this->data = new Set($this->database->findOne($object, $properties, $options));
      // Return result as data set
      return $this->data;
    }

    // Method: getByName
    public function getByName($name, $properties = null, $options = null) {
      // Retrieve object from class name
      $object = $this->getClass();
      // Construct options
      is_object($options) || $options = new Set($options);
      if($options->exists('filter'))
        $options->set('filter', array_merge($options->get('filter'), ['name'=>$name]));
      else
        $options->merge(['filter'=>['name'=>$name]]);
      // Find object
      $this->data = new Set($this->database->findOne($object, $properties, $options));
      // Return result as data set
      return $this->data;
    }

    // Get class name
    public function getClass() {
      return strtolower(basename(str_replace('\\', '/', get_class($this))));
    }

    // Determine if method exists
    public function methodExists($method) {
      return method_exists($this, $method);
    }

    // Method: get
    public function get($id, $properties = null, $options = null) {
      if(is_numeric($id))
        // Number supplied, get by Id
        return $this->getById($id, $properties, $options);
      else
        // Name supplied, get by Name
        return $this->getByName($id, $properties, $options);
    }

    // Method: all
    public function getAll($properties = null, $options = null) {
      // Retrieve object from class name
      $object = $this->getClass();
      // Find object
      $this->data = new Set($this->database->find($object, $properties, $options));
      // Return result as data set
      return $this->data;
    }

    // Return class name without namespace
    public static function class() {
      return strtolower(basename(str_replace('\\', '/', static::class)));
    }

    // Return class name with namespace
    public static function className($model = null) {
      return $model? (__CUBO__ == explode('\\', $model)[0]? $model: __CUBO__.'\\Model\\'.ucfirst($model)): static::class;
    }

    // Static method to determine if model exists
    public static function exists($model) {
      return class_exists(self::className($model));
    }
  }
?>
