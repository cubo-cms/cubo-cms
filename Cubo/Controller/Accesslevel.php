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
  * @description    Accesslevel controller class extends the controller
  *                 framework class and adds additional methods.
  **/
  namespace Cubo\Controller;
  use Cubo\Framework\Controller;

  class Status extends Controller {
    protected $columns = ['accesslevel', 'name', 'status', 'title'];
  }
?>
