<?php

//namespace git4p;

/**
 * Blob object
 *
 * File format:
 * <code>
blob [content size]\0
This is your raw content.
 * </code>
 *
 * Other data relevant to the blob is stored in a tree referencing the blob.
 * 
 * @see GitTree
 */
class GitBlob extends GitObject {
    
    /* Blob object specific variables */
    protected $name = false;            // filename
    protected $mode = 100644;           // mode for blobs
    
    public function __construct($git, $data) {
        parent::__construct($git, $data);
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
