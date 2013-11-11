<?php

namespace git4p;

class GitCommit extends GitObject {
    
    /* Commit object specific variables */
    protected $tree       = false,
              $parent     = false,
              $aName      = false,
              $aEmail     = false,
              $aTimestamp = false,
              $aOffset    = false,
              $cName      = false,
              $cEmail     = false,
              $cTimestamp = false,
              $cOffset    = false,
              $message    = false;
    
    public function __construct($sha, $data, $git) {
        parent::__construct($sha, $data, $git);
        
        $this->loadData();
    }
    
    public function getType() {
        return GitObject::TYPE_COMMIT;
    }

    public function getTree() {
        return $this->tree;
    }
    
    public function getTreeObject() {
        return $this->git->getObject($this->tree);
    }
    
    public function getParent() {
        return $this->parent;
    }
    
    public function getParentObject() {
        if ($this->parent === false) {
            return false;
        }
        
        return $this->git->getObject($this->parent);
    }
    
    public function getMessage() {
        return $this->message;
    }

    public function getAuthorName() {
        return $this->aName;
    }
    
    public function getAuthorEmail() {
        return $this->aEmail;
    }
    
    public function getAuthorTimestamp($asDate=false, $format='D M j G:i:s Y O') {
        if ($asDate === true) {
            return date($format, $this->aTimestamp);
        }
        
        return $this->aTimestamp;
    }
    
    public function getAuthorOffset() {
        return $this->aOffset;
    }
    
    public function getCommitterName() {
        return $this->aName;
    }
    
    public function getCommitterEmail() {
        return $this->aEmail;
    }
    
    public function getCommitterTimestamp($asDate=false, $format='D M j G:i:s Y O') {
        if ($asDate === true) {
            return date($format, $this->cTimestamp);
        }
        
        return $this->cTimestamp;
    }
    
    public function getCommitterOffset() {
        return $this->aOffset;
    }

    private function loadData() {
        $lines = explode("\n", $this->rawdata);
        
        foreach($lines as $line) {
            $line = trim($line);
            $elements = explode(' ', $line, 2);
            
            if (count($elements) == 1) {
                $this->message .= "\n";
                $this->message .= $elements[0];
                continue;
            }

            switch($elements[0]) {
                case 'tree':
                    $this->tree = $elements[1];
                    break;
                case 'parent':
                    $this->parent = $elements[1];
                    break;
                case 'author':
                    preg_match('/^(.+?)\s+<(.+?)>\s+(\d+)\s+([+-]\d{4})$/', $elements[1], $m);
                    $this->aName = $m[1];
                    $this->aEmail = $m[2];
                    $this->aTimestamp = intval($m[3]);
                    $off = intval($m[4]);
                    $this->aOffset = intval($off/100) * 3600 + ($off%100) * 60;
                    break;
                case 'committer':
                    preg_match('/^(.+?)\s+<(.+?)>\s+(\d+)\s+([+-]\d{4})$/', $elements[1], $m);
                    $this->cName = $m[1];
                    $this->cEmail = $m[2];
                    $this->cTimestamp = intval($m[3]);
                    $off = intval($m[4]);
                    $this->cOffset = intval($off/100) * 3600 + ($off%100) * 60;
                    break;
                default:
                    $this->message .= "\n";
                    $this->message .= $elements[0].' '.$elements[1];
                    break;
            }
        }
    }
}