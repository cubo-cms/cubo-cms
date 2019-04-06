<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Framework
  * @version        0.0.3
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
    protected $templates;
    protected $view;

    /**
      * @section    Magic methods
      **/

    // Upon construct save the router
    public function __construct($template) {
      $this->params = $template;
      $this->templates = new Set($template->get('templates'));
      $this->view = $template->get('views')->{self::class()};
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

    /**
      * @section    Private methods
      **/

    // Get the template to be used
    private function getTemplate() {
      return $this->templates->get(self::class(), $this->templates->get('default', '{{main}}'));
    }

    // Render a single item
    private function render(&$template, &$item) {
      $output = preg_replace_callback("/\{{2}(.+?)\}{2}/m", function($match) use($item) {
        return $item->{$match[1]};
      }, $template);
      return $output;
    }

    // Render a list of items
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

    // Render the template
    private function renderTemplate(&$template, &$output) {
      $data = (object)['header'=>'HEADER', 'main'=>$output, 'footer'=>'FOOTER'];
      return $this->render($template, $data);
    }

    /**
      * @section    Standard view methods
      **/

    // Method: item
    public function item($data) {
      // Retrieve format from configuration
      $format = $this->view->{__FUNCTION__};
      // Retrieve template from configuration
      $template = $this->getTemplate();
      // Render formatted data
      $output = $this->render($format, $data);
      // Render template
      return $this->renderTemplate($template, $output);
    }

    // Method: items
    public function items($data) {
      // Retrieve format from configuration
      $format = $this->view->{__FUNCTION__};
      // Retrieve template from configuration
      $template = $this->getTemplate();
      // Render formatted data
      $output = $this->renderAll($format, $data);
      // Render template
      return $this->renderTemplate($template, $output);
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
