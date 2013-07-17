<?php

/**
 * A Git object is stored in a subdirectory that's named for the first two
 * characters of the object's SHA1. The object's filename is the remaining
 * 38 characters.
 */
class GitObject {

    const   TYPE_BLOB   = 0;
    const   TYPE_TREE   = 1;
    const   TYPE_COMMIT = 2;
    const   TYPE_TAG    = 3;

    const   BASEDIR     = '.git/';
    const   OBJECTSDIR  = '.git/objects/';
    const   REFSDIR     = '.git/refs/';

    protected $header='';
    protected $content=false;
    protected $sha1='';
    protected $type=false;
    protected $repodir='';
    protected $dir='';
    protected $objectname='';
    
    public function __construct($repodir, $sha=false) {
        if (is_string($repodir) == false) {
            throw new Exception('GitObject should be initialized with a string containing the absolute path to the directory in which the ".git" directory is located.');
        }
        
        $this->repodir = $repodir;
        if ($sha !== false) {
            $this->readFromDisk($sha);
        }
    }
    
    private function setHeader() {
        if ($this->content === false) {
            throw new Exception('setHeader - Cannot generate git object header if there is no content set.');
        }
        
        if ($this->type === false) {
            throw new Exception('setHeader - Cannot generate git object header if there is no type set.');
        }
        
        $this->header = self::generateHeader($this->type, $this->content);
    }
    
    private static function generateHeader($type, $content) {
        return self::constToType($type)." ".strlen($content)."\0";
    }
    
    private static function generateSha($header, $content) {
        return sha1($header.$content);
    }
    
    public function getHeader() {
        return $this->header;
    }
    
    public function setContent($content, $sha=false) {
        $this->content = $content;
        $this->setHeader();
        
        var_dump($content);
        
        if ($sha === false) {
            $this->sha1 = self::generateSha($this->header, $this->content);
        }
        else {
            if (self::generateSha($this->header, $this->content) !== $sha) {
                throw new Exception('setContent - Given sha1 is not the same as calculated sha1!');
            }
            $this->sha1 = $sha;
        }
    }
    
    public function getSha1() {
        return $this->sha1;
    }
    
    public static function typeToConst($type) {
        switch ($type) {
            case 'commit': return self::TYPE_COMMIT;
            case 'blob'  : return self::TYPE_BLOB;
            case 'tree'  : return self::TYPE_TREE;
            case 'tag'   : return self::TYPE_TAG;
            default      : throw new Exception('typeToConst - Cannot convert type to constant, unknown type supplied.');
        }
    }

    public static function constToType($const) {
        switch ($const) {
            case self::TYPE_COMMIT: return 'commit';
            case self::TYPE_BLOB  : return 'blob';
            case self::TYPE_TREE  : return 'tree';
            case self::TYPE_TAG   : return 'tag';
            default      : throw new Exception('constToType - Cannot convert constant to type, unknown constant supplied.');
        }
    }
    
    public function readFromDisk($sha) {
        $dir = substr($sha, 0, 2);
        $objectname = substr($sha, 2,38);
        
        $filename = $this->repodir.self::OBJECTSDIR.$dir.'/'.$objectname;
        
        if (file_exists($filename) && is_readable ($filename)) {        
            $content = file_get_contents($filename);
            $content = gzuncompress($content);
            
            echo "TEST-".$content."\n";
            
            $content = explode("\0",$content);
            $header = $content[0];
            $content = $content[1];
            
            $header = trim($header);
            $header = explode(' ', $header);
            
            $type = self::typeToConst($header[0]);
            $length = $header[1];

            //$obj = new GitObject($repodir);
            //$obj->setType($type);
            $this->setType($type);
            //$obj->setContent($content, $sha);
            $this->setContent($content, $sha);
            $this->dir = $dir;
            $this->objectname = $objectname;

            return true;
        }

        return false;
    }
    
    public function setType($type) {
        if (is_string($type) && !is_int($type)) {
            $type = self::typeToConst($type);
        }
        
        $this->type = $type;
    }

    public function __toString() {
        $ret = '';
        
        //return ''.self::constToType($this->type).' '.$this->sha1."\n\n".$this->content."\n";
        foreach(get_object_vars($this) as $key => $var) {
            $ret .= "$key: $var\n";
        }
        
        $ret .= "\n";
        return $ret;
    }
    
    public function getContent() {
        return $this->content;
    }

}