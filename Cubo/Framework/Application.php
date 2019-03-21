/**
  * @package        cubo-cms/cms
  * @category       Framework
  * @version        0.0.1
  * @copyright      2019 Cubo CMS <https://cubo-cms.com/COPYRIGHT.md>
  * @license        MIT license <https://cubo-cms.com/LICENSE.md>
  * @author         papiando
  * @link           <https://github.com/cubo-cms/cubo-cms>
  * @description    Application framework class is the core of the web
  *                 application. In its simplest form, creating an application
  *                 object and calling the "run" method is enough to have a
  *                 web application running.
  *
  **/
<?php
  namespace Cubo\Framework;

  final class Application {
    private $configuration;     // Configuration set
    private $route;             // Pointer to router object
    private $routes;            // Set of routes

    // Upon construct initialise the application
    public function __construct($config = 'config') {
      $this->init($config);
    }

    // Allow returning configuration as JSON
    public function __toString() {
      return (string)$this->configuration;
    }

    // Add a new route
    public function addRoute($path, $params = null) {
      is_null($this->routes) && $this->routes = [];
      $this->routes[] = new Route($path, $params);
    }

    // Get configuration parameter
    public function get($property, $default = null) {
      is_null($this->configuration) && $this->configuration = new Set();
      return $this->configuration->get($property, $default);
    }

    // Return router object
    public function getRouter() {
      return $this->router ?? $this->router = new Router($this->routes ?? []);
    }

    // Retrieve set of routes
    public function getRoutes() {
      return $this->routes ?? [];
    }

    // Initialise the application
    public function init($config = null) {
      // Load configuration
      new Configuration($config);
      $this->configuration = Configuration::get(basename($config, '.json'));
      // Load routes from configuration
      $routes = Configuration::load('routes', $this->configuration->get('routes'));
      $this->loadRoutes($routes->getAll());
    }

    // Load routes from configuration
    private function loadRoutes($routes) {
      is_array($routes) && $routes = (object)$routes;
      // Iterate through routes
      foreach($routes as $path=>$params) {
        $this->addRoute($path, $params);
      }
    }

    // Start the application
    public function run() {
      // Get router object
      $router = $this->getRouter();
      // Pass application object to router
      $router->calledBy($this);
      // Invoke controller
      $controller = $router->invokeController();
      // Pass application object to controller
      $controller->calledBy($this);
      // Invoke method
      echo $router->invokeMethod($this);            // ****************** CURRENTLY JUST SHOW OUTPUT
    }

    // Set configuration parameter
    public function set($property, $value) {
      is_null($this->configuration) && $this->configuration = new Set();
      $this->configuration->set($property, $value);
    }
  }
?>
