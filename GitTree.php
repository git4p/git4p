<?php

class GitTree extends GitObject {
    
    /* Tree object specific variables */
    protected $entries = array();
    
    public function __construct($sha, $data) {
        parent::__construct($sha, $data);
        
        $this->loadData();
    }
    
    public function getEntries() {
        return $this->entries;
    }
    
    public function loadData() {        
        $start = 0;
        while ($start < strlen($this->rawdata)) {
          $pos = strpos($this->rawdata, "\0", $start);

          list($mode, $name) = explode(' ', substr($this->rawdata, $start, $pos-$start), 2);
          
          $mode = intval($mode, 8);
          $is_dir = !!($mode & 040000);

          $start = $pos+21;

          $sha = substr($this->rawdata, $pos+1, 20);

          // @todo replace by actual objects
          $this->entries[bin2hex($sha)] = $name;
        }
    }
}