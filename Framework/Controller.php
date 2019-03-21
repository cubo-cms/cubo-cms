<?php
  namespace Cubo\Framework;

  class Controller {
    protected $caller;          // Pointer to calling object
    protected $model;           // Pointer to model object
    protected $params;          // Parameter set
    protected $router;          // Pointer to router object

    // Upon construct save the router
    public function __construct($router) {
      $this->router = $router;
      $this->params = $router->getParams();
    }

    // Allow returning parameters as JSON
    public function __toString() {
      return (string)$this->params;
    }

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

    // Return class name
    public static function className($controller = null) {
      return $controller? (__CUBO__ == explode('\\', $controller)[0]? $controller: __CUBO__.'\\Controller\\'.ucfirst($controller)): __CLASS__;
    }

    // Static method to determine if controller exists
    public static function exists($controller) {
      return class_exists(self::className($controller));
    }
  }
?>
