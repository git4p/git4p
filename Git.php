<?php

namespace git4p;

/**
 * Class representing a single Git repository.
 * 
 * @todo Add pack support
 */
class Git {
    const   DIR_BASE     = '.git';
    const   DIR_OBJECTS  = '.git/objects';
    const   DIR_REFS     = '.git/refs';
    const   HEAD         = '.git/HEAD';
    
    /* Repository instance data */
    private $dir = false;

    
    public function __construct($dir=false) {
        if (is_string($dir) === false) {
            throw new \Exception("Git repository should be initialized with the absolute path to the repository's directory.");;
        }
        
        $this->dir = rtrim($dir, '/');
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

    private static function getHead($dir) {
        $headref = false;
        
        $path = sprintf('%s/%s', $dir, self::HEAD);
        
        if (file_exists($path) && is_file($path) && is_readable($path)) {
            $headref = explode(' ', trim(file_get_contents($path)));
        }
        
        $path = sprintf('%s/%s/%s', $dir, self::DIR_BASE, $headref[1]);

        if (file_exists($path) && is_file($path) && is_readable($path)) {
            return trim(file_get_contents($path));
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
        if (!is_string($sha)) {
            return false;
        }
        
        $dir = substr($sha, 0, 2);
        $objectname = substr($sha, 2,38);

        $path = sprintf('%s/%s/%s/%s', $this->dir, self::DIR_OBJECTS, $dir, $objectname);

        if (file_exists($path) && is_file($path) && is_readable($path)) {
            list($header, $data) = explode("\0", gzuncompress(file_get_contents($path)), 2);
            sscanf($header, "%s %d", $type, $object_size);

            $class = "git4p\Git".ucfirst($type);
            $obj   = new $class($sha, $data, $this);
        
            return $obj;
        }
        else {
            throw new \Exception("Object $sha not found in path $path");
        }
        
        return false;
    }
}