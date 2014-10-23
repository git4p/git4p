<?php

//namespace git4p;

/**
 * A Git object is stored in a subdirectory that's named for the first two
 * characters of the object's SHA1. The object's filename is the remaining
 * 38 characters.
 */
abstract class GitObject {

    const TYPE_COMMIT = 'commit';
    const TYPE_TREE   = 'tree';
    const TYPE_BLOB   = 'blob';

    // Back reference to main Git repository object
    protected $git      = false;
    
    /* Generic variables for Git objects */
    protected $sha      = false;
    protected $rawdata  = false;
    protected $location = false;
    protected $filename = false;
    
    public function __construct($git, $data) {
        $this->git = $git;

/*        
        $this->sha      = $sha;
        $this->location = substr($sha, 0, 2);
        $this->filename = substr($sha, 2);
        $this->rawdata  = $data;
*/
    }

    public function setData($data) {
        $this->rawdata = $data;
    }

    public function getSha() {
        return $this->sha;
    }
    
    public function getRawData() {
        return $this->rawdata;
    }
    
    public function getLocation() {
        return $this->location;
    }
    
    public function getFilename() {
        return $this->filename;
    }
    
    public function getHeader($type) {
        return sprintf("%s %d\0", $type, strlen($this->rawdata));
    }
    
    public function __toString() {
        $ret = '';
        $vars = get_object_vars($this);
        ksort($vars);
        
        foreach($vars as $key => $var) {
            if ($key == 'rawdata') continue;
            $ret .= sprintf("%s: %s\n", $key, print_r($var, true));
        }
        
        $ret .= "\n";
        return $ret;
    }
}
