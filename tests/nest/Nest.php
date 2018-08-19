<?php

  /**
   * Nest is a simple testing utility for SleekDB
   * SleekDB is a open-source NoSQL document database for PHP
   * @url https://sleekdb.github.io/
   * @author RakibTG <rakibtg - at - gmail>
   * Copyright - SleekDB
   */
  require_once __DIR__ . '/nest.utils.php';
  class Nest {

    use NestUtils;

    function __construct($root) {
      $this->root = $root . '/';
      $this->testCase = $this->root . 'nest/test-case/';
      $this->dbStorage = $this->root . 'nest/test-db-storage/';
      $this->testStore = $this->dbStorage . 'mysite/';
    }

    function getAllTestCases() {
      return array_diff(scandir($this->testCase), array('..', '.'));
    }

    function translateFileNameToFunctionName($fileName) {
      return trim(str_replace('-', '_', pathinfo($fileName)['filename']));
    }

    function emptyTestStore() {
      $it = new RecursiveDirectoryIterator( $this->testStore, RecursiveDirectoryIterator::SKIP_DOTS);
      $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
      foreach($files as $file) {
        if ($file->isDir()){
          rmdir($file->getRealPath());
        } else {
          unlink($file->getRealPath());
        }
      }
      rmdir( $this->testStore );
    }

    public function runTest() {

      // Import SleekDB.
      require_once $this->dir . '../src/SleekDB.php';

      // Instantiate the object.
      $database = new \SleekDB\SleekDB( $this->testStore, [
        'auto_cache' => true,
        'timeout' => 120
      ] );

      // Empty the test store.
      if ( file_exists( $this->testStore ) ) $this->emptyTestStore();
      foreach ($this->getAllTestCases() as $key => $testCase) {
        require_once $this->testCase . $testCase;
        $this->print_default( $title );
        $this->print_success( 
          $this->translateFileNameToFunctionName( $testCase )( $database ) 
        );
      }
    }

  }