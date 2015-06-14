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

    public function testShouldHaveReferenceToDirectory() {
        $this->git = new Git('/tmp/some/directory');

        $this->assertEquals($this->git->dir(), '/tmp/some/directory');
    }

    public function testShouldBePrintable() {
        $this->git = new Git('/tmp/some/directory');

        $this->assertEquals($this->git, '/tmp/some/directory');
    }

    public function testShouldBeConvertableToHex() {
        $this->assertEquals(Git::sha2bin('747e95f8ffd8d29f62135b2f9c9b216f8ade17bd'),
                            pack('H40', '747e95f8ffd8d29f62135b2f9c9b216f8ade17bd'));
    }
}
