<?php

use Git4p\Git;
use Git4p\GitCommit;
use Git4p\GitObject;

class GitCommitTest extends PHPUnit_Framework_TestCase {

    protected $git       = false;
    protected $gitcommit = false;

    public function setup() {
        $this->git       = new Git('/tmp/phpunit/gittestrepo');
        $this->gitcommit = new GitCommit($this->git);
    }

    public function teardown() {
        $this->git       = false;
        $this->gitcommit = false;
    }

    public function testShouldReturnGitCommitType() {
        $this->assertEquals($this->gitcommit->type(), GitObject::TYPE_COMMIT);
    }

    public function testShouldHaveCommitMessage() {
        $this->gitcommit->setMessage('Initial commit.');

        $this->assertEquals($this->gitcommit->message(), 'Initial commit.');
    }

}
