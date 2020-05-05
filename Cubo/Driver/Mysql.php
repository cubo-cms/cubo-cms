<?php
/**
  * @package        cubo-cms/cubo-cms
  * @category       Driver
  * @version        0.0.3
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
  use Cubo\Framework\Error;
  use Cubo\Framework\Set;

  class Mysql extends Driver {
    private $connection;

    // Upon construct save parameters and open connection
    public function __construct($params) {
      $this->params = $params;
      $this->connected() || $this->connect();
    }

    // Upon destruct close connection
    public function __destruct() {
      $this->connected() && $this->disconnect();
    }

    // Open connection
    public function connect() {
      try {
        if(empty($this->params->source) || empty($this->params->user) || empty($this->params->secret)) {
          throw new Error(['message'=>'mysql-invalid-dsn']);
        } else {
          try {
            $this->connection = new \PDO('mysql:'.$this->params->source, $this->params->user, $this->params->secret);
          } catch(\PDOException $exc) {
            throw new Error(['message'=>'mysql-cannot-connect']);
          }
        }
      } catch(Error $error) {
        $error->render();
      }
      return $this->connected();
    }

    // Determine if connected
    public function connected() {
      return !is_null($this->connection);
    }

    // Disconnect
    public function disconnect() {
      $this->connection = null;
    }

    // Apply options
    private function applyOptions(&$options) {
      is_object($options) && $options = (array)$options->getAll();
      $optionString = '';
      if(is_array($options)) {
        // Iterate through options
        foreach($options as $option=>$properties) {
          $method = $option.'Option';
          if(method_exists($this, $method))
            $optionString .= $this->$method($properties);
        }
      }
      return $optionString;
    }

    // Filter results
    private function filterOption($filter) {
      is_object($filter) && $filter = (array)$filter->getAll();
      $filters = [];
      foreach($filter as $key=>$condition) {
        if(is_array($condition)) {
          $filters[] = "`{$key}` IN ('".implode("','", $condition)."')";
        } else {
          $filters[] = "`{$key}`='{$condition}'";
        }
      }
      if(empty($filters)) {
        return "";
      } else {
        return " WHERE ".implode(' AND ', $filters);
      }
    }

    // Select columns
    private function selectColumns(&$columns) {
      is_object($columns) || $columns = (array)$columns;
      if(empty($columns) || $columns == '*') {
        return '*';
      } else {
        return '`'.implode('`,`', $columns).'`';
      }
    }

    // Method: find
    public function find($table, $columns = null, $options = null) {
      $query = "SELECT ".$this->selectColumns($columns)." FROM `{$table}`";
      // Apply options
      $query .= $this->applyOptions($options);
      // Perform query
      $sth = $this->connection->prepare($query);
      $sth->execute();
      $sth->setFetchMode(\PDO::FETCH_OBJ);
      $result = $sth->fetchAll();
      return $result;
    }

    // Method: findOne
    public function findOne($table, $columns = null, $options = null) {
      $result = $this->find($table, $columns, $options);
      // Return first object
      return count($result)? $result[0]: null;
    }

    // Determine if source exists
    public static function sourceExists($driver, $source) {
      return true;
    }
  }
?>
