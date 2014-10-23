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
    
    public function __construct($sha, $data, $git) {
        parent::__construct($sha, $data, $git);
        
        $this->loadData();
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
        return GitObject::TYPE_TREE;
    }
    
    public function getEntries() {
        return $this->entries;
    }
    
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
    
    public function __toString() {
        $txt = '';
        
        $txt = "Tree sha: $this->sha\n\nFiles and directories:\n\n";
        
        foreach ($this->getEntries() as $entry) {
            $txt .= "".$entry->getType()." ".$entry->getName()."\n";
            if ($entry->getType() == 'tree') {
                $txt .= $entry;
            }
        }
        
        return $txt;
    }
}
