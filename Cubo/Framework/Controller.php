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
  * @description    Controller framework class contains base methods for all
  *                 object controllers. Object controllers extend this class
  *                 and could define additional methods, if needed.
  *                 Controllers define what actions can be performed for each
  *                 object and retrieves or saves the data through models.
  **/
  namespace Cubo\Framework;
  use Cubo\Framework\Error;
  use Cubo\Framework\Model;
  use Cubo\Controller\User;

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

    // Invoke view
    public function invokeView() {
      try {
        $view = View::className($this->params->get('controller'));
        // Determine if view exists
        if(View::exists($view)) {
          // Initiate view
          return $this->view = new $view($this->loadTemplate());
        } else {
          // The view does not exist
          throw new Error(['message'=>'view-does-not-exist', 'params'=>$this->params]);
        }
      } catch(Error $error) {
        $error->render();
      }
    }

    // Load template from configuration
    private function loadTemplate() {
      return Configuration::load('template',Configuration::get('config')->get('template'));
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
      if($model = $this->invokeModel()) {
        // Pass controller object to model
        $model->calledBy($this);
        // Retrieve filters from query
        $query = $this->router->getQuery();
        $columns = array_filter(explode(',', $query->get('columns')), 'strlen');
        $query->delete('columns');
        // Add access filter
        $query->merge(self::canList());
        // Get all access levels
        $data = $model->getAll(empty($columns)? $this->columns ?? ['_id', 'name']: $columns, $query);
        // Invoke View
        if($view = $this->invokeView()) {
          // Pass controller object to view
          $view->calledBy($this);
          // Call view method
          return $view->default($data->getAll());
        }
      }
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
      if($model = $this->invokeModel()) {
        // Pass controller object to model
        $model->calledBy($this);
        // Retrieve filters from query
        $query = $this->router->getQuery();
        $columns = array_filter(explode(',', $query->get('columns')), 'strlen');
        $query->delete('columns');
        // Add access filter
        $query->merge(self::canView());
        // Get object by name
        $data = $model->get($this->params->get('name'), empty($columns)? $this->columns ?? ['_id', 'name']: $columns, $query);
        // Invoke View
        if($view = $this->invokeView()) {
          // Pass controller object to view
          $view->calledBy($this);
          // Call view method
          return $view->default($data->getAll());
        }
      }
    }

    /**
      * @section    Static methods
      **/

    // Construct filter to restrict objects that can be viewed
    public static function canList() {
      $filter = [];
      $filter['accesslevel'] = [ACCESSLEVEL_PUBLIC, User::Guest()? ACCESSLEVEL_GUEST: ACCESSLEVEL_REGISTERED];
      $filter['status'] = [STATUS_PUBLISHED];
      return array('filter'=>$filter);
    }

    // Construct filter to restrict objects that can be viewed
    public static function canView() {
      $filter = [];
      $filter['accesslevel'] = [ACCESSLEVEL_PUBLIC, User::Guest()? ACCESSLEVEL_GUEST: ACCESSLEVEL_REGISTERED, ACCESSLEVEL_PRIVATE];
      $filter['status'] = [STATUS_PUBLISHED, STATUS_ARCHIVED];
      return array('filter'=>$filter);
    }

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
