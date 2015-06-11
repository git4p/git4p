<?php

use Git4p\Git;

class GitTest extends PHPUnit_Framework_TestCase {

    protected $git     = false;

    public function teardown() {
        $this->git     = false;
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Git repository should be initialized with the absolute path to the repository's directory.
     */
    public function testShouldThrowExceptionWhenCreatedWithoutDirectory() {
        $this->git = new Git();
    }
}
