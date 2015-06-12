<?php

use Git4p\GitUser;

class GitUserTest extends PHPUnit_Framework_TestCase {

    protected $gituser = false;

    public function setup() {
        $this->gituser = new GitUser();
        $this->gituser->setName('Some User');
        $this->gituser->setEmail('some.user@example.com');
        $this->gituser->setTimestamp('1374058686');
        $this->gituser->setOffset('+0100');
    }

    public function teardown() {
        $this->gituser = false;
    }

    public function testShouldHaveName() {
        $this->assertEquals($this->gituser->name(), 'Some User');
    }

    public function testShouldHaveEmail() {
        $this->assertEquals($this->gituser->email(), 'some.user@example.com');
    }

    public function testShouldHaveTimestamp() {
        $this->assertEquals($this->gituser->timestamp(), '1374058686');
    }

    public function testShouldHaveOffset() {
        $this->assertEquals($this->gituser->offset(), '+0100');
    }

    public function testShouldBePrintableAsString() {
        $this->assertEquals($this->gituser->__toString(), 'Some User <some.user@example.com> 1374058686 +0100');
    }

}
