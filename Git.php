<?php

namespace git4p;

/**
 * Class representing a single Git repository.
 * 
 * @todo Add pack support
 */
class Git {
    const   DIR_BASE     = '.git/';
    const   DIR_OBJECTS  = '.git/objects/';
    const   DIR_REFS     = '.git/refs/';
    const   HEAD         = '.git/HEAD';
    
    /* Repository instance data */
    private $dir = false;

    
    public function __construct($dir) {
        if (is_string($dir) === false) {
            throw new Exception("Git repository should be initialized with the absolute path to the repository's directory.");
        }
        
        $this->dir = $dir;
    }
    
    public function getDir() {
        return $this->dir;
    }
    
    public function __toString() {
        return $this->dir;
    }


    public function getHeadObject($branch = 'master') {
        $sha = self::getHead($this->dir);
        
        return $this->getObject($sha);
    }

    private static function getHead($repodir) {
        $headref = false;
        $filename = $repodir.self::HEAD;
        
        if (file_exists($filename) && is_readable ($filename)) {
            $headref = trim(file_get_contents($filename));
            $headref = explode(' ', $headref);
        }
        
        $filename = $repodir.self::DIR_BASE.$headref[1];
        if (file_exists($filename) && is_readable ($filename)) {
            $rootsha = trim(file_get_contents($filename));
            return $rootsha;
        }
        
        return false;
    }
        
    /**
     * Retrieves a basic GitObject from disk based on given SHA.
     * 
     * @todo Add caching?
     * @todo Add pack support
     * 
     * @param type $sha
     * @return boolean
     */
    public function getObject($sha) {
        $dir = substr($sha, 0, 2);
        $objectname = substr($sha, 2,38);
        
        $path = sprintf('%s/%s/%s/%s', $this->dir, self::DIR_OBJECTS, substr($sha, 0, 2), substr($sha, 2));

        if (file_exists($path) && is_readable ($path)) {
            list($header, $data) = explode("\0", gzuncompress(file_get_contents($path)), 2);
            sscanf($header, "%s %d", $type, $object_size);

            $class = "git4p\Git".ucfirst($type);
            $obj   = new $class($sha, $data, $this);
        
            return $obj;
        }
        else {
            throw new Exception("Object $sha not found in path $path");
        }
        
        return false;
    }
}