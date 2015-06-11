<?php

use Git4p\Git;
use Git4p\GitBlob;

class GitBlobTest extends PHPUnit_Framework_TestCase {

    protected $git     = false;
    protected $gitblob = false;

    function setup() {
        $this->git     = new Git();
        $this->gitblob = new GitBlob($this->git);
    }

    function teardown() {
        $this->git     = false;
        $this->gitblob = false;
    }

    function testShouldReturnGitBlobType() {
        $obj = new GitBlob();
        assertEquals($obj->type(), GitObject::TYPE_BLOB);
    }
}
