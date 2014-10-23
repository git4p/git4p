<?php

namespace git4p;

class GitBlob extends GitObject {
    
    /* Blob object specific variables */
    protected $name = false;            // filename
    
    public function __construct($sha, $data, $git) {
        parent::__construct($sha, $data, $git);
        
        //$this->loadData();
    }
    
    public function setMode($mode) {
        $this->mode = $mode;
    }
    
    public function setname($name) {
        $this->name = $name;
    }
    
    public function getMode() {
        return $this->mode;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getType() {
        return GitObject::TYPE_BLOB;
    }
    
}