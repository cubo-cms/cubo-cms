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
  * @description    Article controller class extends the controller framework
  *                 class and adds additional methods.
  **/
  namespace Cubo\Controller;
  use Cubo\Framework\Controller;

  class Article extends Controller {
    protected $columns = ['accesslevel', 'body', 'name', 'status', 'title'];

    /**
      * @section    Additional controller methods
      **/

    // Method: category
    public function category() {
      // Retrieve category id
      $category = Category::getId($this->params->get('category'));
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get all articles of requested category
      return $model->getAll(null, ['filter'=>['category'=>$category]]);
    }

    // Method: status
    public function status() {
      // Retrieve status id
      $status = Status::getId($this->params->get('status'));
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get all articles of requested status
      return $model->getAll(null, ['filter'=>['status'=>$status]]);
    }
  }
?>
