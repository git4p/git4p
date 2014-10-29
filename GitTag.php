<?php

//namespace git4p;

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
    
    public function setObject($obj) {
        $this->objtype = $obj->type();
        $this->objsha  = $obj->sha();
    }
    
    public function setTag($data) {
        $this->tag = $data;
    }
    
    public function setTagger($data) {
        $this->tagger = $data;
    }
    
    public function setMessage($data) {
        $this->message = $data;
    }
    
    public function data() {
        $data = '';
        
        $data .= sprintf("object %s\ntype %s\ntag %s\ntagger %s\n\n%s",
                         $this->objSha(), $this->objType(), $this->tag(), implode(' ', $this->Tagger()), $this->message());
        
        return $data;
    }
    
    public function type() {
        return GitObject::TYPE_TAG;
    }
    
}
