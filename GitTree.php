<?php

//namespace git4p;

/**
 * Tree object
 *
 * File format:
 * <code>
tree [content size]\0
100644 blob [sha1]  [filename]
040000 tree [sha1]  [dirname]
 * </code>
 *
 * Other data relevant to the blob is stored in a tree referencing the blob.
 * 
 * @see GitTree
 */
class GitTree extends GitObject {
    
    /* Tree object specific variables */
    protected $entries = array();
    protected $name    = false;             // directory name
    protected $mode    = 040000;            // mode for trees
    
    public function __construct($git) {
        parent::__construct($git);
    }
    
    protected static function compare(&$a, &$b) {
        return strcmp($a->name, $b->name);
    }
    
    
    // GETTERS
    public function type() {
        return GitObject::TYPE_TREE;
    }
    
    public function data() {
        $data = '';
        
        uasort($this->entries(), 'GitTree::compare');
        foreach ($this->entries() as $name => $entry) {
            $data .= sprintf("%s %s\0%s", $entry->mode(), $name, Git::sha2bin($entry->sha()));
        }
        
        return $data;
    }
    
    public function entries() {
        return $this->entries;
    }
        
    
    // SETTERS
    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }
    
    public function setData($data) {
        $this->entries = $data;
        
        return $this;
    }
    
    
    // DATA LOADER
    public function loadData() {        
        $start = 0;
        while ($start < strlen($this->rawdata)) {
          $pos = strpos($this->rawdata, "\0", $start);
          list($mode, $name) = explode(' ', substr($this->rawdata, $start, $pos-$start), 2);

          $imode = intval($mode, 8);
          $is_dir = !!($imode & 040000);

          $sha = substr($this->rawdata, $pos+1, 20);
          $sha = bin2hex($sha);

          $obj = $this->git->getObject($sha);
          $obj->setMode($mode);
          $obj->setName($name);
          
          // @todo replace by actual objects
          $this->entries[$sha] = $obj;
          $start = $pos+21;
        }
    }
}
