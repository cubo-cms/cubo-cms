<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Driver
  * @version        0.0.1
  * @copyright      2019 Cubo CMS <https://cubo-cms.com/COPYRIGHT.md>
  * @license        MIT license <https://cubo-cms.com/LICENSE.md>
  * @author         papiando
  * @link           <https://github.com/cubo-cms/cubo-cms>
  *
  * @description    Mysql driver class extends the driver framework class and
  *                 enables access to MySQL databases.
  **/
  namespace Cubo\Driver;
  use Cubo\Framework\Driver;

  class Mysql extends Driver {
    // Determine if source exists
    public static function sourceExists($driver, $source) {
      return true;
    }
  }
?>
