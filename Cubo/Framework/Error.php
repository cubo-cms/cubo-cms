<?php
  namespace Cubo\Framework;

  final class Error extends \Exception {
    private $params;

    // Upon construct throw error
    public function __construct($error = null) {
      $this->throw($error);
    }

    // Render error page
    public function render() {
      echo('<p><strong>ERROR</strong>: '.$this->params->get('message').'</p>');
    }

    // Throw error
    public function throw($error = null) {
      empty($error) && $error = ['message'=>'unknown-error'];
      $this->params = new Set($error);
    }
  }
?>
