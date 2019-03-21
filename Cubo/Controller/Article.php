<?php
  namespace Cubo\Controller;
  use Cubo\Framework\Controller;
  use Cubo\Framework\Error;
  use Cubo\Framework\Model;

  class Article extends Controller {
    // Method: all
    public function all() {
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get all articles
      return 'Showing '.$this->params->get('controller').': ALL... ';
    }

    // Method: category
    public function category() {
    }

    // Method: default
    public function default() {
      return $this->all();
    }

    // Method: read
    public function read() {
      return $this->view();
    }

    // Method: status
    public function status() {
    }

    // Method: view
    public function view() {
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get article by name
      return $model->get($this->params->get('name'));
    }
  }
?>
