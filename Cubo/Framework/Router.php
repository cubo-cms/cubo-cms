<?php
  namespace Cubo\Framework;

  final class Router {
    private $caller;            // Pointer to calling object
    private $controller;        // Pointer to controller object
    private $method;            // Invoked method
    private $params;            // Parameter set
    private $query;             // Query set
    private $routes;            // Set of routes

    // Upon construct initialise router
    public function __construct($routes = null) {
      $this->init($routes);
    }

    // Allow returning parameters as JSON
    public function __toString() {
      return (string)$this->params;
    }

    // Pass calling object
    public function calledBy($caller = null) {
      return $caller? $this->caller = $caller: $this->caller;
    }

    // Get parameter
    public function get($property, $default = null) {
      is_null($this->params) && $this->params = new Set();
      return $this->params->get($property, $default);
    }

    // Return controller object
    public function getController() {
      return $this->controller;
    }

    // Return invoked method
    public function getMethod() {
      return $this->method;
    }

    // Return parameter set
    public function getParams() {
      return $this->params;
    }

    // Return query set
    public function getQuery() {
      is_null($this->query) && $this->query = $this->parseQuery();
      return $this->query;
    }

    // Initialise router
    public function init($routes = null) {
      $this->routes = $routes;
    }

    // Invoke controller
    public function invokeController($uri = null) {
      // Parse requested URI if not yet parsed
      $this->params || $this->parse($uri ?? $_SERVER['REQUEST_URI']);
      try {
        $controller = Controller::className($this->params->get('controller', 'undefined'));
        // Determine if controller exists
        if(Controller::exists($controller)) {
          // Initiate controller
          return $this->controller = new $controller($this);
        } else {
          // The controller does not exist
          throw new Error(['message'=>'controller-does-not-exist', 'params'=>$this->params]);
        }
      } catch(Error $error) {
          $error->render();
      }
    }

    // Invoke method
    public function invokeMethod() {
      // Invoke controller if not yet invoked
      empty($this->controller) && $this->invokeController();
      try {
        $this->method = $this->params->get('method', 'default');
        if($this->controller->methodExists($this->method)) {
          // Call method
          return $this->controller->{$this->method}();
        } else {
          // The method does not exist
          throw new Error(['message'=>'method-does-not-exist', 'params'=>$this->params]);
        }
      } catch(Error $error) {
        $error->render();
      }
    }

    // Parse route
    public function parse($uri = null, $routes = null) {
      is_null($routes) || $this->$routes = $routes;
      $parts = explode('/', trim(parse_url($uri ?? $_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
      // Expand url and match to route list
      foreach($this->routes as $route) {
        $params = new Set();
        if(count($parts) == count($route->parts)) {
          $matched = true;
          for($n = 0; $n < count($parts); $n++) {
            if($parts[$n] == $route->parts[$n]) {
              // Do nothing
            } elseif(Set::isVariable($route->parts[$n])) {
              $params->set($route->parts[$n], $parts[$n]);
            } else {
              $matched = false;
            }
          }
          if($matched) {
            $route->params->merge($params);
            return $this->params = new Set($route->params->getAll());
          }
        }
      }
    }

    // Parse query string
    public function parseQuery($uri = null) {
      // Parse query
      parse_str(parse_url($uri ?? $_SERVER['REQUEST_URI'], PHP_URL_QUERY), $query);
      // Normalise values (a:1,b:2 becomes a=1&b=2)
      foreach($query as $property=>$value) {
        if(is_string($value) && strpos($value, ':'))
          $query[$property] = $this->parseQuery('?'.str_replace(',', '&', str_replace(':', '=', $value)));
      }
      // Store query
      return new Set((object)$query);
    }

    // Redirect to specified URI
    public static function redirect($uri, $responseCode = 301) {
      Session::set('responseCode', $responseCode);
      exit(header('Location: '.$uri));
    }
  }
?>
