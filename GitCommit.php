<?php

//namespace git4p;

/**

Format:

-------------------------------------------
commit <content size>\0
tree <full sha1>
[parent <full sha1>]
author <name> <<email>> <timestamp> <offset>
committer <name> <<email>> <timestamp> <offset>

<message>
-------------------------------------------

note: in case of merge, multiple parent entries

*/
class GitCommit extends GitObject {
    
    /* Commit object specific variables */
    protected $tree       = false,
              $parents    = array(),
              $authors    = array(),
              $committers = array(),
              $aName      = false,
              $aEmail     = false,
              $aTimestamp = false,
              $aOffset    = false,
              $cName      = false,
              $cEmail     = false,
              $cTimestamp = false,
              $cOffset    = false,
              $message    = false;
    
    public function __construct($git) {
        parent::__construct($git);
    }

    public function type() {
        return GitObject::TYPE_COMMIT;
    }

    public function tree() {
        return $this->tree;
    }
    
    public function message() {
        return $this->message;
    }
    
    public function authors() {
        return $this->authors;
    }

    public function committers() {
        return $this->committers;
    }
    
    public function parents() {
        return $this->parents;
    }
    
    public function addParent($data) {
        $this->parents[] = $data;
    }

    public function addAuthor($data) {
        $this->authors[] = $data;
    }

    public function addCommiter($data) {
        $this->committers[] = $data;
    }
    
    public function setMessage($data) {
        $this->message = $data;
    }
    
    public function setTree($sha) {
        $this->tree = $sha;
    }
    
    public function data() {
        $data = "";
        
        $data .= sprintf("tree %s\n", $this->tree());
        foreach ($this->parents() as $parent) {
            $data .= sprintf("parent %s\n", $parent);
        }
        
        foreach ($this->authors() as $author) {
            $data .= sprintf("author %s\n", implode(' ', $author));
        }
        
        foreach ($this->committers as $committer) {
            $data .= sprintf("committer %s\n", implode(' ', $committer));
        }
        
        $data .= sprintf("\n%s", $this->message());
        
        return $data;
    }
    
    public function __toString() {
        return "commit ".$this->sha()."\n".$this->data();
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
