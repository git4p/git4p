<?php

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
    
    public function getAuthorTimestamp() {
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
    
    public function getCommitterTimestamp() {
        return $this->aTimestamp;
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
                    sscanf($elements[1], "%s %s %d %s", $this->aName, $this->aEmail, $this->aTimestamp, $this->aOffset);
                    break;
                case 'committer':
                    sscanf($elements[1], "%s %s %d %s", $this->cName, $this->cEmail, $this->cTimestamp, $this->cOffset);
                    break;
                default:
                    $this->message .= "\n";
                    $this->message .= $elements[0].' '.$elements[1];
                    break;
            }
        }
    }
}