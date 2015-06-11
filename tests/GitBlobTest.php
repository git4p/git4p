<?php

use Git4p\Git;
use Git4p\GitObject;
use Git4p\GitBlob;

class GitBlobTest extends PHPUnit_Framework_TestCase {

    protected $git     = false;
    protected $gitblob = false;

    public function setup() {
        $this->git     = new Git('/tmp/phpunit/gittestrepo');
        $this->gitblob = new GitBlob($this->git);
    }

    public function teardown() {
        $this->git     = false;
        $this->gitblob = false;
    }

    public function testShouldReturnGitBlobType() {
        $this->assertEquals($this->gitblob->type(), GitObject::TYPE_BLOB);
    }
}
