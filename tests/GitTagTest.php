<?php

use Git4p\Git;
use Git4p\GitObject;
use Git4p\GitTag;

class GitTagTest extends PHPUnit_Framework_TestCase {

    protected $git    = false;
    protected $gittag = false;

    public function setup() {
        $this->git    = new Git('/tmp/phpunit/gittestrepo');
        $this->gittag = new GitTag($this->git);
    }

    public function teardown() {
        $this->git    = false;
        $this->gittag = false;
    }

    public function testShouldReturnGitTagType() {
        $this->assertEquals($this->gittag->type(), GitObject::TYPE_TAG);
    }

    public function testShouldHaveTagName() {
        $this->gittag->setTag('1.0.0');
        $this->assertEquals($this->gittag->tag(), '1.0.0');
    }

    public function testShouldReturnFalseOnEmptyMessage() {
        $this->assertFalse($this->gittag->message());
    }

    public function testCanHaveMessage() {
        $this->gittag->setMessage('Some sort of message about the tag.');
        $this->assertEquals($this->gittag->message(), 'Some sort of message about the tag.');
    }

    public function testCanHaveUserAsTagger() {
        $this->gittag->setTagger('Some User');
        $this->assertEquals($this->gittag->tagger(), 'Some User');
    }

    public function testShouldHaveObjectSha() {
        $this->markTestIncomplete();
    }

    public function testShouldHaveObjectType() {
        $this->markTestIncomplete();
    }
}
