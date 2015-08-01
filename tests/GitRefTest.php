<?php

use Git4p\Git;
use Git4p\GitRef;

class GitRefTest extends PHPUnit_Framework_TestCase {

    protected $git    = false;
    protected $gitref = false;

    public function setup() {
        $this->git    = new Git(GIT4P_TESTDIR);
        $this->gitref = new GitRef($this->git);
    }

    public function teardown() {
        $this->git    = false;
        $this->gitref = false;
    }

    public function testShouldListRefsAsArray() {
        $this->assertEquals($this->gitref->listRefs(), []);
    }

    public function testShouldAllowCheckIfRefExists() {
        $this->assertFalse($this->gitref->exists('some/random/ref'));
    }

    public function testShouldReturnShaForValidRef() {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
