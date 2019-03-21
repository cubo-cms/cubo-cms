<?php
  namespace Cubo\Controller;
  use Cubo\Framework\Controller;
  use Cubo\Framework\Error;
  use Cubo\Framework\Model;

  class Category extends Controller {
    // Method: all
    public function all() {
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get all categories
      return $model->getAll();
    }

    // Method: articles
    public function articles() {
    }

    // Method: default
    public function default() {
      return $this->all();
    }

    // Method: read
    public function read() {
      return $this->view();
    }

    // Method: view
    public function view() {
      // Invoke model
      $model = $this->invokeModel();
      // Pass controller object to model
      $model->calledBy($this);
      // Get category by name
      return $model->get($this->params->get('name'));
    }
  }
?>
