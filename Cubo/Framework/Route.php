<?php
  namespace Cubo\Framework;

  final class Route {
    public $path;
    public $params;
    public $parts;

    // Upon construct parse route
    public function __construct($path, $params = null) {
      $this->parse($path, $params);
    }

    // Parse path and construct parameter set
    public function parse($path, $params = null) {
      // Preset parameter set
      $this->params = new Set($params);
      // Save path
      $this->path = $path;
      // Detect variables and add these to parameter set
      foreach($this->parts = explode('/', trim(parse_url($path, PHP_URL_PATH), '/')) as $part) {
        if($match = Set::isVariable($part))
          $this->params->set($match, $part);
      }
    }
  }
?>
