<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace org\git4p;

class GitTag extends GitObject {
    
    /* object specific variables */
    protected $objsha  = false;     // sha1 of object that you're tagging
    protected $objtype = false;     // type of object you're tagging
    protected $tag     = false;
    protected $tagger  = false;
    protected $message = false;
    
    public function __construct($git) {
        parent::__construct($git);
    }
        
    
    // GETTERS
    public function type() {
        return GitObject::TYPE_TAG;
    }
    
    public function tag() {
        return $this->tag;
    }
    
    public function tagger() {
        return $this->tagger;
    }
    
    public function message() {
        return $this->message;
    }
 
    public function objSha() {
        return $this->objsha;
    }
    
    public function objType() {
        return $this->objtype;
    }
    
    public function data() {
        $data = '';
        
        $data .= sprintf("object %s\ntype %s\ntag %s\ntagger %s\n\n%s",
                         $this->objSha(), $this->objType(), $this->tag(), $this->tagger(), $this->message());
        
        return $data;
    }
    
    
    // SETTERS
    public function setObject($obj) {
        $this->objtype = $obj->type();
        $this->objsha  = $obj->sha();
        
        return $this;
    }
    
    public function setTag($data) {
        $this->tag = $data;
        
        return $this;
    }
    
    public function setTagger($data) {
        $this->tagger = $data;
        
        return $this;
    }
    
    public function setMessage($data) {
        $this->message = $data;
        
        return $this;
    }
}
