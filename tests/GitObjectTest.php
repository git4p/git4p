<?php

use Git4p\Git;
use Git4p\GitObject;

class GitObjectTest extends PHPUnit_Framework_TestCase {

    protected $git     = false;

    public function setup() {
        $this->git  = new Git('/tmp/phpunit/gittestrepo');
        $this->stub = $this->getMockForAbstractClass('Git4p\GitObject', [$this->git]);

        $this->stub->expects($this->any())
                   ->method('type')
                   ->will($this->returnValue('object'));
    }

    public function teardown() {
        $this->git     = false;
    }

    public function testShouldBePrintable() {
        $this->assertEquals($this->stub->__toString(), 'object 108a7d59');
    }

    public function testShouldReturnGitObjectTypeForMocks() {
        $this->assertEquals($this->stub->type(), 'object');
    }

    public function testShouldCalculateSha() {
        $this->assertEquals($this->stub->sha(), '108a7d597e69c56ed4ff3bd3c4d6c2141cc70f81');
    }

    public function testShouldReturnShortSha() {
        $this->assertEquals($this->stub->shortSha(), '108a7d59');
    }

    public function testShouldDetermineDirectoryWithinRepository() {
        $this->assertEquals($this->stub->location(), '10');
    }

    public function testShouldDetermineFilenameWithinRepository() {
        $this->assertEquals($this->stub->filename(), '8a7d597e69c56ed4ff3bd3c4d6c2141cc70f81');
    }
}
