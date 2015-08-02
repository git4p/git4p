<?php

use Git4p\Git;
use Git4p\GitBlob;
use Git4p\GitObject;
use Git4p\GitTag;

class GitTagTest extends PHPUnit_Framework_TestCase {

    protected $git    = false;
    protected $gittag = false;

    public function setup() {
        $this->git    = new Git(GIT4P_TESTDIR);
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
        $blob = new GitBlob($this->git);
        $blob->setData('Simulate that a README file was commited and pushed to master.')
             ->store();

        $this->gittag->setObject($blob);

        $this->assertEquals($this->gittag->objSha(), '747e95f8ffd8d29f62135b2f9c9b216f8ade17bd');
    }

    public function testShouldHaveObjectType() {
        $blob = new GitBlob($this->git);
        $blob->setData('Simulate that a README file was commited and pushed to master.')
             ->store();

        $this->gittag->setObject($blob);

        $this->assertEquals($this->gittag->objType(), GitObject::TYPE_BLOB);
    }
}
