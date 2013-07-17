<?php

class GitCommit extends GitObject {
    protected $tree = false;
    protected $parent = false;
    protected $authorName;
    protected $authorEmail;
    protected $authorTimestamp;
    protected $authorTimestampOffset;
    protected $committerName;
    protected $committerEmail;
    protected $committerTimestamp;
    protected $committerTimestampOffset;
    protected $description='';
    
    public function readFromDisk($sha) {
        parent::readFromDisk($sha);
        
        $tosplit = $this->content;
        $split = explode("\n", $tosplit);
        foreach($split as $line) {
            $line = trim($line);
            
            if (startsWith($line, 'tree')) {
                $line = explode(' ', $line);
                $this->tree = $line[1];
                continue;
            }
            
            if (startsWith($line, 'parent')) {
                $line = explode(' ', $line);
                $this->parent = $line[1];
                continue;
            }
            
            if (startsWith($line, 'author')) {
                $line = explode(' ', $line);
                $this->authorName = $line[1];
                $this->authorEmail = $line[2];
                $this->authorTimestamp = $line[3];
                $this->authorTimestampOffset = $line[4];
                continue;
            }
            
            if (startsWith($line, 'committer')) {
                $line = explode(' ', $line);
                $this->committerName = $line[1];
                $this->committerEmail = $line[2];
                $this->committerTimestamp = $line[3];
                $this->committerTimestampOffset = $line[4];
                continue;
            }
            
            $this->description .= "\n";
            $this->description .= $line;
        }
    }
    
    public function __toString() {
        return parent::__toString();
    }

    public function getParent() {
        return $this->parent;
    }
    
    public function getTree() {
        return $this->tree;
    }
}

?>
