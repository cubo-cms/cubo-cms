<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Controller
  * @version        0.0.1
  * @copyright      2019 Cubo CMS <https://cubo-cms.com/COPYRIGHT.md>
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

  class User extends Controller {
    protected $columns = ['accesslevel', 'name', 'status', 'title'];
  }
?>
