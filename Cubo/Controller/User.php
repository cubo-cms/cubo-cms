<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Controller
  * @version        0.0.4
  * @copyright      2020 Cubo CMS <https://cubo-cms.com/COPYRIGHT.md>
  * @license        MIT license <https://cubo-cms.com/LICENSE.md>
  * @author         papiando
  * @link           <https://github.com/cubo-cms/cubo-cms>
  *
  * @description    User controller class extends the controller framework
  *                 class and adds additional methods. Specifically, the user
  *                 controller provides methods to log on and off.
  **/
  namespace Cubo\Controller;
  use Cubo\Framework\Controller;
  use Cubo\Framework\Router;
  use Cubo\Framework\Session;

  class User extends Controller {
    protected $columns = ['accesslevel', 'description', 'name', 'status', 'title'];

    private static $authors = [ROLE_AUTHOR, ROLE_EDITOR, ROLE_PUBLISHER, ROLE_MANAGER, ROLE_ADMINISTRATOR];
    private static $editors = [ROLE_EDITOR, ROLE_PUBLISHER, ROLE_MANAGER, ROLE_ADMINISTRATOR];
    private static $publishers = [ROLE_PUBLISHER, ROLE_MANAGER, ROLE_ADMINISTRATOR];
    private static $managers = [ROLE_MANAGER, ROLE_ADMINISTRATOR];
    private static $administrators = [ROLE_ADMINISTRATOR];

    /**
      * @section    Additional controller methods
      **/

    // Method: login
    public function login() {
      // First apply response code, if given
      if(!empty(Session::get('responseCode'))) {
        http_response_code(Session::get('responseCode'));
        Session::delete('responseCode');
      }
      if(isset($_REQUEST['user']) && isset($_REQUEST['password'])) {
        // Invoke model
        $model = $this->invokeModel();
        // Pass controller object to model
        $model->calledBy($this);
        // Get object by name
        $user = $model->get(strtolower($_REQUEST['user']), ['_id', 'blocked', 'email', 'name', 'password', 'status', 'title', 'verified']);
        if($user) {
          if($user->blocked) {
            // User is blocked
            echo('User is blocked');
          } elseif($user->status != 1) {
            // User is not published
            echo('User is not verified');
          } elseif(!hash_equals($user->password ?? 'none', crypt($_REQUEST['password'], $user->password))) {
            // Incorrect password
            echo password_hash('$3c,Pap;!',PASSWORD_DEFAULT).' ';
            Session::message(['message'=>'invalid-user-or-password']);
            print_r(Session::get('messages'));
          } else {
            // Test password
            Session::message(['message'=>'user-logged-in']);
            $user->delete('password');
            Session::set('user', $user->getAll());
            Session::set('accessToken', substr(strtr(base64_encode(random_bytes(64)), '+/', '-_'), 0, 86));
          }
        } else {
          // User not found
          Session::message(['message'=>'invalid-user-or-password']);
          print_r(Session::get('messages'));
        }
      } else {
        die('Please provide credentials');
      }
    }

    // Method: logout
    public function logout() {
      Session::message(['message'=>'user-logged-out', 'params'=>Session::get('user')]);
      Session::delete('user');
      Router::redirect(Session::get('lastVisited', '/'));
    }

    /**
      * @section    Static methods
      **/

    // Determine if visitor is guest
    public static function guest() {
      return !Session::exists('user');
    }

    // Determine if visitor is an administrator
    public static function isAdministrator() {
      return self::registered() && in_array(Session::get('user')->_id, self::$administrators);
    }

    // Determine if visitor is an author
    public static function isAuthor() {
      return self::registered() && in_array(Session::get('user')->_id, self::$authors);
    }

    // Determine if visitor is an editor
    public static function isEditor() {
      return self::registered() && in_array(Session::get('user')->_id, self::$editors);
    }

    // Determine if visitor is a manager
    public static function isManager() {
      return self::registered() && in_array(Session::get('user')->_id, self::$managers);
    }

    // Determine if visitor is a publisher
    public static function isPublisher() {
      return self::registered() && in_array(Session::get('user')->_id, self::$publishers);
    }

    // Determine if visitor is logged in
    public static function registered() {
      return Session::exists('user');
    }
  }
?>
