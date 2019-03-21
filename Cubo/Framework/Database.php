<?php
  namespace Cubo\Framework;

  final class Database {
    private $connection;
    private $driver;
    private $params;

    // Upon construct connect to database driver
    public function __construct($params = null) {
      $this->connect($params);
    }

    // Connect to database driver
    public function connect($params = null) {
      $this->params = new Set($params);
      try {
        $driver = Driver::className($this->params->get('driver', 'mysql'));
        // Determine if driver exists
        if(Driver::exists($driver)) {
          // Initiate driver
          return $this->driver = new $driver($this->params);
        } else {
          // The driver does not exist
          throw new Error(['message'=>'driver-does-not-exist', 'params'=>$this->params]);
        }
      } catch(Error $error) {
        $error->render();
      }
    }

    // Method: find
    public function find($table, $columns = null, $options = null) {
      return $this->driver->find($table, $columns, $options);
    }

    // Method: findOne
    public function findOne($table, $columns = null, $options = null) {
      return $this->driver->findOne($table, $columns, $options);
    }

    // Method: insert
    public function insert($table, $item) {
      $this->driver->insert($table, $item);
    }
  }
?>
