<?php
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
    public function getById($id, $properties = null, $options = []) {
      // Retrieve object from class name
      $object = $this->getClass();
      // Construct options
      $options = (object)array_merge((array)$options, ['filter'=>['_id', $id]]);
      // Find object
      $this->data = new Set($this->database->findOne($object, $properties, $options));
      // Return result as data set
      return $this->data;
    }

    // Method: getByName
    public function getByName($name, $properties = null, $options = []) {
      // Retrieve object from class name
      $object = $this->getClass();
      // Construct options
      $options = (object)array_merge((array)$options, ['filter'=>['name', $name]]);
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
    public function get($id, $properties = null, $options = []) {
      if(is_numeric($id))
        // Number supplied, get by Id
        return $this->getById($id, $properties);
      else
        // Name supplied, get by Name
        return $this->getByName($id, $properties);
    }

    // Method: all
    public function getAll() {
      //
    }

    // Return class name
    public static function className($model = null) {
      return $model? (__CUBO__ == explode('\\', $model)[0]? $model: __CUBO__.'\\Model\\'.ucfirst($model)): __CLASS__;
    }

    // Static method to determine if model exists
    public static function exists($model) {
      return class_exists(self::className($model));
    }
  }
?>
