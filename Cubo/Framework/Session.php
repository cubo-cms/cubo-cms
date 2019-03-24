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
    private static $messages;
    private static $params;

    // Upon construct initialise the session
    public function __construct($name = __CUBO__, $lifetime = 3600) {
      $this->init($name);
    }

    // Short method to get property
    public function __get($property) {
      return self::get($property);
    }

    // Short method to validate presence of property
    public function __isset($property) {
      return self::exists($property);
    }

    // Short method to set property
    public function __set($property, $value) {
      self::set($property, $value);
    }

    // Allow returning session properties as JSON
    public function __toString() {
      return (string)self::$params;
    }

    // Delete property
    public static function delete($property) {
      unset($_SESSION[$property]);
      self::$params->delete($property);
    }

    // Determine if property exists
    public static function exists($property) {
      return isset($_SESSION[$property]);
    }

    // Determine if session is expired
    public static function expired() {
      return time() > self::get('expires');
    }

    // Get session property
    public static function get($property, $default = null) {
      return self::$params->get($property, $_SESSION[$property] ?? $default);
    }

    // Return session id
    public static function getId() {
      return self::$params->get('id');
    }

    // Return messages
    public static function getMessages() {
      $messages = self::get('messages');
      self::delete('messages');
      return $messages;
    }

    // Return session name
    public static function getName() {
      return self::$params->get('name');
    }

    // Initialise the session
    public static function init($name = __CUBO__, $lifetime = 3600) {
      is_null(self::$params) && self::$params = new Set;
      // Apply session name
      session_name($name);
      // Determine if there was a session cookie before starting
      $newSession = !isset($_COOKIE[$name]);
      // Start the session
      session_set_cookie_params($lifetime, '/');
      session_start();
      // Clear session data when expired
      $now = time();
      if($newSession) {
        session_unset();
        self::set('started', $now);
      }
      // Store session data
      self::set('visited', $now);
      setcookie(self::set('name', $name), self::set('id', session_id()), self::set('expires', $now + self::set('lifetime', $lifetime)), '/');
    }

    // Add message
    public static function message($message) {
      if(!self::exists('messages'))
        self::set('messages', []);
      self::set('messages', self::get('messages')[] = $message);
    }

    // Set session property
    public static function set($property, $value) {
      return $_SESSION[$property] = self::$params->set($property, $value);
    }
  }
?>
