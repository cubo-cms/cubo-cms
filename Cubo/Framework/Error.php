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
  * @description    Error framework class contains enables error handling
  *                 and redirection to customised error pages.
  **/
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
      is_array($error) && $error = (object)$error;
      $this->params = new Set($error);
    }
  }
?>
