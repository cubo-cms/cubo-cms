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
  * @description    Session framework class allows session handling and
  *                 storing session data.
  **/
  namespace Cubo\Framework;

  final class Session {
    private $params;

    // Upon construct initialise the session
    public function __construct($name = __CUBO__, $lifetime = 3600) {
      $this->init($name);
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

    // Allow returning session properties as JSON
    public function __toString() {
      return json_encode($this->params, JSON_PRETTY_PRINT);
    }

    // Determine if property exists
    public function exists($property) {
      return $this->params->exists($property);
    }

    // Determine if session is expired
    public function expired() {
      return time() > $this->get('expires');
    }

    // Get session property
    public function get($property, $default) {
      is_null($this->params) && $this->init();
      return $this->params->get($property, $default);
    }

    // Return session id
    public function getId() {
      return $this->params->get('id');
    }

    // Return session name
    public function getName() {
      return $this->params->get('name');
    }

    // Initialise the session
    public function init($name = __CUBO__, $lifetime = 3600) {
      is_null($this->params) && $this->params = new Set;
      // Apply session name
      session_name($name));
      // Determine if there was a session cookie before starting
      $newSession = !isset($_COOKIE[$name]);
      // Start the session
      session_set_cookie_params($lifetime, '/');
      session_start();
      // Store session data
      $this->set('lastAccessed', $now = time());
      setcookie($this->set('name', $name), $this->set('id', session_id()), $this->set('expires', $now + $this->set('lifetime', $lifetime)), '/');
      if($newSession)
        $this->set('started', $now);
    }

    // Set session property
    public function set($property, $value) {
      is_null($this->params) && $this->init();
      $_SESSION[$property] = $this->params->set($property, $value);
    }
  }
?>
