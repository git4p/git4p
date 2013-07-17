<?php

class GitBlob extends GitObject {
    
    /* Blob object specific variables */
    
    public function __construct($sha, $data, $git) {
        parent::__construct($sha, $data, $git);
        
        //$this->loadData();
    }
    
}