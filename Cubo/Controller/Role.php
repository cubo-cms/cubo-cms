<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Controller
  * @version        0.0.3
  * @copyright      2019 Cubo CMS <https://cubo-cms.com/COPYRIGHT.md>
  * @license        MIT license <https://cubo-cms.com/LICENSE.md>
  * @author         papiando
  * @link           <https://github.com/cubo-cms/cubo-cms>
  *
  * @description    Role controller class extends the controller
  *                 framework class and adds additional methods.
  **/
  namespace Cubo\Controller;
  use Cubo\Framework\Controller;

  class Role extends Controller {
    protected $columns = ['accesslevel', 'description', 'name', 'status', 'title'];
  }
?>
