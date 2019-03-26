<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Framework
  * @version        0.0.2
  * @copyright      2019 Cubo CMS <https://cubo-cms.com/COPYRIGHT.md>
  * @license        MIT license <https://cubo-cms.com/LICENSE.md>
  * @author         papiando
  * @link           <https://github.com/cubo-cms/cubo-cms>
  *
  * @description    View framework class contains base methods for all
  *                 object views. Object views extend this class
  *                 and could define additional methods, if needed.
  *                 Views render the retrieved data to visible output in
  *                 different formats.
  **/
  namespace Cubo\Framework;

  class View {
    protected $params;
    protected $template;

    /**
      * @section    Magic methods
      **/

    // Upon construct save the router
    public function __construct($template) {
      $this->params = $template;
      $this->template = $template->get('views')->{self::class()};
    }

    // Allow returning parameters as JSON
    public function __toString() {
      return (string)$this->params;
    }

    /**
      * @section    Basic public methods
      **/

    // Pass calling object
    public function calledBy($caller = null) {
      return $caller? $this->caller = $caller: $this->caller;
    }

    // Determine if method exists
    public function methodExists($method) {
      return method_exists($this, $method);
    }

    private function render(&$template, &$item) {
      $output = preg_replace_callback("/\{{2}(.+?)\}{2}/m", function($match) use($item) {
        return $item->{$match[1]};
      }, $template);
      return $output;
    }

    private function renderAll(&$template, &$items) {
      $class = self::class();
      $output = preg_replace_callback("/\{{2}#$class\}{2}(.+?)\{{2}\/$class\}{2}/m", function($match) use($items) {
        $output = '';
        foreach($items->getAll() as $item) {
          $item = new Set($item);
          $output .= $this->render($match[1], $item);
        }
        return $output;
      }, $template);
      return $output;
    }

    /**
      * @section    Standard controller methods
      **/

    // Method: item
    public function item($data) {
      // Retrieve template from configuration
      $template = $this->template->{__FUNCTION__};
      $output = $this->render($template, $data);
      return $output;
    }

    // Method: items
    public function items($data) {
      // Retrieve template from configuration
      $template = $this->template->{__FUNCTION__};
      $output = $this->renderAll($template, $data);
      return $output;
    }

    /**
      * @section    Static methods
      **/

    // Return class name without namespace
    public static function class() {
      return strtolower(basename(str_replace('\\', '/', static::class)));
    }

    // Return class name with namespace
    public static function className($view = null) {
      return $view? (__CUBO__ == explode('\\', $view)[0]? $view: __CUBO__.'\\View\\'.ucfirst($view)): static::class;
    }

    // Static method to determine if view exists
    public static function exists($view) {
      return class_exists(self::className($view));
    }
  }
?>
