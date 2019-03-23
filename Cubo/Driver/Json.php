<?php
  namespace Cubo\Driver;
  use Cubo\Framework\Driver;
  use Cubo\Framework\Error;
  use Cubo\Framework\Set;

  class Json extends Driver {
    private $tables;

    // Upon construct save parameters
    public function __construct($params) {
      $this->params = $params;
    }

    // Apply options
    private function applyOptions($table, &$options) {
      is_object($options) && $options = (array)$options->getAll();
      if(is_array($options)) {
        // Iterate through options
        foreach($options as $option=>$properties) {
          $method = $option.'Option';
          if(method_exists($this, $method))
            $table = $this->$method($table, $properties);
        }
      }
      return $table;
    }

    // Filter results
    private function filterOption($table, $filter) {
      is_object($filter) && $filter = (array)$filter->getAll();
      if(is_array($table) && is_array($filter)) {
        $result = [];
        // Iterate through table to filter
        foreach($table as $item) {
          $valid = true;
          foreach($filter as $key=>$condition) {
            if(isset($item->$key) && $item->$key != $condition) {
              $valid = false;
              break;
            }
          }
          if($valid)
            $result[] = $item;
        }
        return $result;
      } else
        return $table;
    }

    // Limit results
    private function limitOption($table, $offsetLimit) {
      is_object($offsetLimit) && $offsetLimit = (array)$offsetLimit->getAll();
      if(is_array($table)) {
        if(is_array($offsetLimit)) {
          if(isset($offsetLimit['limit']) || isset($offsetLimit['offset'])) {
            // Properties supplied: limit, size or count is set to limit; offset or start set to offset
            $limit = $offsetLimit['limit'] ?? $offsetLimit['size'] ?? $offsetLimit['count'] ??null;
            $offset = $offsetLimit['offset'] ?? $offsetLimit['start'] ?? 0;
          } else {
            // Only values supplied: first is limit; second is offset
            $limit = current($offsetLimit);
            $offset = next($offsetLimit);
          }
        } else {
          // Only one value supplied: set to limit
          $limit = $offsetLimit;
          $offset = 0;
        }
        $table = array_slice($table, (int)$offset, (int)$limit);
      }
      return $table;
    }

    // Load table into memory
    private function loadTable(&$table) {
      is_null($this->tables) && $this->tables = new Set;
      if($this->tables->exists($table))
        return $this->tables->$table;
      else {
        try {
          $file = __ROOT__.$this->params->get('source');
          if(file_exists($file)) {
            $contents = file_get_contents($file);
            $this->tables->set($table, json_decode($contents));
            return $this->tables->$table;
          } else {
            throw new Error(['message'=>'file-not-found']);
          }
        } catch(Error $error) {
          $error->render();
        }
        return false;
      }
    }

    // Save table to file
    private function saveTable(&$table, &$data) {
      try {
        $file = __ROOT__.$this->params->get('source');
        if(false === file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)))
          throw new Error(['message'=>'file-not-saved']);
      } catch(Error $error) {
        $error->render();
      }
    }

    // Select columns
    private function selectColumns(&$table, &$columns) {
      is_object($columns) || $columns = (array)$columns;
      if(is_array($table) && is_array($columns) && !empty($columns)) {
        $result = [];
        // Iterate through table to select columns
        foreach($table as $item) {
          // Iterate through all properties
          foreach($item as $property=>$value) {
            if(!in_array($property, $columns))
              unset($item->$property);
          }
          $result[] = $item;
        }
        return $result;
      } else
        return $table;
    }

    // Sort results
    private function sortOption($table, $sortOrder) {
      is_object($sortOrder) && $sortOrder = (array)$sortOrder->getAll();
      if(is_array($table)) {
        if(is_array($sortOrder))
          $this->sortOrder = $sortOrder;
        else
          $this->sortOrder = [$sortOrder=>1];
        usort($table, function($a, $b) {
          $key = key($this->sortOrder);
          if(!isset($a->$key) || !isset($b->$key))
            return 0;
          $regexp = '/^((?:\+|-)?[0-9]+)$/';
          if(preg_match($regexp, $a->$key) && preg_match($regexp, $b->$key))
            return ((int)$a->$key <=> (int)$b->$key) * current($this->sortOrder);
          else {
            return strcasecmp($a->$key, $b->$key) * current($this->sortOrder);
          }
        });
      }
      return $table;
    }

    // Method: find
    public function find($table, $columns = null, $options = null) {
      // For JSON, the complete table needs to be loaded
      $table = $this->loadTable($table);
      // Apply options
      $table = $this->applyOptions($table, $options);
      // Select columns
      $table = $this->selectColumns($table, $columns);
      // Return result
      return $table;
    }

    // Method: findOne
    public function findOne($table, $columns = null, $options = null) {
      $table = $this->find($table, $columns, $options);
      // Return first object
      return count($table)? $table[0]: null;
    }

    // Method: insert
    public function insert($table, $item) {
      // For JSON, the complete table needs to be loaded
      $data = $this->loadTable($table);
      // Add item
      is_array($item) && $item = (object)$item;
      $data[] = $item;
      // Update cached copy
      $this->tables->$table = $data;
      // Save table
      $this->saveTable($table, $data);
    }

    // Determine if source exists
    public static function sourceExists($driver, $source) {
      // For JSON no good way to check if source exists
      return true;
    }
  }
?>
