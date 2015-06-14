<?php

use Git4p\Git;
use Git4p\GitObject;
use Git4p\GitTree;

class GitTreeTest extends PHPUnit_Framework_TestCase {

    protected $git     = false;
    protected $gittree = false;

    public function setup() {
        $this->git     = new Git('/tmp/phpunit/gittestrepo');
        $this->gittree = new GitTree($this->git);
    }

    public function teardown() {
        $this->git    = false;
        $this->gittree = false;
    }

    public function testShouldReturnGitTreeType() {
        $this->assertEquals($this->gittree->type(), GitObject::TYPE_TREE);
    }

    public function testShouldHaveDirectoryName() {
        $this->gittree->setName('SomeDirectoryName');

        $this->assertEquals($this->gittree->name(), 'SomeDirectoryName');
    }

    public function testShouldContainReferencesToFiles() {
        $data = ['file1', 'file2'];

        $this->gittree->setData($data);
        $this->assertEquals($this->gittree->entries(), $data);
    }

}
