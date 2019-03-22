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
  * @description    Controller framework class contains base methods for all
  *                 object controllers. Object controllers extend this class
  *                 and could define additional methods, if needed.
  *                 Controllers define what actions can be performed for each
  *                 object and retrieves or saves the data through models.
  **/
  namespace Cubo\Framework;
  use Cubo\Framework\Error;
  use Cubo\Framework\Model;

  class Controller {
    protected $caller;          // Pointer to calling object
    protected $model;           // Pointer to model object
    protected $params;          // Parameter set
    protected $router;          // Pointer to router object

    /**
      * @section    Magic methods
      **/

    // Upon construct save the router
    public function __construct($router = null) {
      if($router) {
        $this->router = $router;
        $this->params = $router->getParams();
      }
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

    // Return the router
    public function getRouter() {
      return $this->router;
    }

    // Invoke model
    public function invokeModel() {
      try {
        $model = Model::className($this->params->get('controller'));
        $this->method = $this->params->get('method', 'default');
        // Determine if model exists
        if(Model::exists($model)) {
          // Initiate model
          return $this->model = new $model();
        } else {
          // The model does not exist
          throw new Error(['message'=>'model-does-not-exist', 'params'=>$this->params]);
        }
      } catch(Error $error) {
          $error->render();
      }
    }

    // Determine if method exists
    public function methodExists($method) {
      return method_exists($this, $method);
    }

    /**
      * @section    Standard controller methods
      **/

    // Method: all
    public function all() {
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get all access levels
      return $model->getAll();
    }

    // Method: default
    public function default() {
      return $this->all();
    }

    // Method: read
    public function read() {
      return $this->view();
    }

    // Method: view
    public function view() {
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get access level by name
      return $model->get($this->params->get('name'));
    }

    /**
      * @section    Static methods
      **/

    // Return class name without namespace
    public static function class() {
      return strtolower(basename(str_replace('\\', '/', static::class)));
    }

    // Return class name with namespace
    public static function className($controller = null) {
      return $controller? (__CUBO__ == explode('\\', $controller)[0]? $controller: __CUBO__.'\\Controller\\'.ucfirst($controller)): static::class;
    }

    // Retrieve the _id for the object with given name
    public static function getId($name) {
      $model = Model::className(self::class());
      // Determine if model exists
      if(Model::exists($model)) {
        // Initiate model
        $model = new $model();
        return $model->getByName($name, '_id')->_id ?? null;
      } else
        return null;
    }

    // Static method to determine if controller exists
    public static function exists($controller) {
      return class_exists(self::className($controller));
    }
  }
?>
