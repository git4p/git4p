<?php

/**
 * Class representing a single Git repository.
 * 
 * @todo Add pack support
 */
class Git {
    const   DIR_BASE     = '/';
    const   DIR_OBJECTS  = '/objects/';
    const   DIR_REFS     = '/refs/';
    const   HEAD         = '/HEAD';
    
    /* Repository instance data */
    private $dir = false;
    
    
    public static function init($dir) {
        // Test if directory is already a repo
        if (file_exists($dir.'/HEAD') === true) {
            throw new Exception('Unable to initialize repository. Already is a repository.');
        }
    
        // always init as bare repo
        $paths = array(
            'branches',
            'hooks',
            'info',
            'objects/info',
            'objects/pack',
            'refs/heads',
            'refs/tags'
        );
        
        // Create bare repo dirs
        foreach ($paths as $path) {
            $result = mkdir($dir.'/'.$path, 0774, true);
            if ($result === false) {
                throw new Exception('Unable to create path '.$path);
            }
        }
        
        // Create bare repo files
        self::writeFile($dir.'/HEAD', "ref: refs/heads/master\n");
        self::writeFile($dir.'/description', "Unnamed repository; edit this file 'description' to name the repository.\n");
        self::writeFile($dir.'/config', "[core]\n\trepositoryformatversion = 0\n\tfilemode = true\n\tbare = true\n");
        
        return new Git($dir);
    }

    private static function writeFile($path, $content) {
        $result = file_put_contents($path, $content, LOCK_EX);
        if ($result === false) {
            throw new Exception('Unable to write to file '.$path);
        }
    }

    private static function readFile($path, $uncompress=false) {
        $result = file_get_contents($path);
        if ($result === false) {
            throw new Exception('Unable to read from file '.$path);
        }
        
        if ($uncompress === true) {
            $result = gzuncompress($result);
        }
        
        return $result;
    }
    
    public function __construct($dir=false) {
        if (is_string($dir) === false) {
            throw new Exception("Git repository should be initialized with the absolute path to the repository's directory.");
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

    private static function getHead($repodir) {
        $headref = false;
        $filename = $repodir.self::HEAD;
        
        $headref = trim(self::readFile($filename));
        $headref = explode(' ', $headref);

        $filename = $repodir.self::DIR_BASE.$headref[1];
        $rootsha = trim(self::readFile($filename));
        
        return $rootsha;
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

        $file = Git::readFile($path, true);

        list($header, $data) = explode("\0", $file, 2);
        sscanf($header, "%s %d", $type, $object_size);

        $class = "Git".ucfirst($type);
        $obj   = new $class($sha, $data, $this);
        
        return $obj;
    }
}