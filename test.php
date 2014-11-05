#!/usr/bin/php
<?php

include "git4php.php";

// Test setup
$readme = "GIT4P\n=====\n\nThis is a simple test repo for git4p.\n";
$dir = dirname(__FILE__).'/mytestrepo';
$git = false;

// Create the repo if necessary or just a reference to existing repo
if (file_exists($dir.'/HEAD') === false) {
    echo "Repo does not exist, creating on disk.\n";
    $git = Git::init($dir);
}
else {
    echo "Repo exists, creating reference object instance.\n";
    $git = new Git($dir);
}

// Create a basic one file initial commit, then simulate a push to the repo
echo "Simulate that a README file was commited and pushed to master.\n";
$b = new GitBlob($git);
$b->setData($readme);
$b->store();
echo "Created blob ".$b->sha()."\n";

$arr = array('README.md' => $b);
$t = new GitTree($git);
$t->setData($arr);
$t->store();
echo "Created tree ".$t->sha()."\n";

$c = new GitCommit($git);
$c->setTree($t->sha());
$c->setMessage("Initial commit.");
$c->addAuthor(array('name'=>'Martijn van der Kleijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'));
$c->addCommiter(array('name'=>'Martijn van der Kleijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'));
$c->store();
$oc = $c;
echo "Created commit ".$c->sha()."\n";

$firstcommit = $c->sha();

// Make sure master head ref exists and points to commit
Git::writeFile($dir.'/refs/heads/master', ''.$c->sha()."\n");

// Lets create an extra branch called 'develop'
Git::writeFile($dir.'/refs/heads/develop', ''.$c->sha()."\n");

// Add a commit to develop
$b = new GitBlob($git);
$b->setData("Altered README.md file!!!\n");
$b->store();
echo "Created blob ".$b->sha()."\n";

$arr = array('README.md' => $b);
$t = new GitTree($git);
$t->setData($arr);
$t->store();
echo "Created tree ".$t->sha()."\n";

$c = new GitCommit($git);
$c->setTree($t->sha());
$c->addParent($oc->sha());
$c->setMessage("Update readme.");
$c->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'));
$c->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'));
$c->store();
echo "Created commit ".$c->sha()."\n";

$p = $c->sha();

// Add a commit to develop
$b = new GitBlob($git);
$b->setData("Altered README.MD file!\n");
$b->store();
echo "Created blob ".$b->sha()."\n";

$arr = array('README.md' => $b);
$t = new GitTree($git);
$t->setData($arr);
$t->store();
echo "Created tree ".$t->sha()."\n";

$c = new GitCommit($git);
$c->setTree($t->sha());
$c->addParent($p);
$c->setMessage("Correct readme.");
$c->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374158776', 'offset'=>'+0200'));
$c->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374158776', 'offset'=>'+0200'));
$c->store();
echo "Created commit ".$c->sha()."\n";

$sc = new GitCommit($git);
$sc->setTree($t->sha());
$sc->addParent($firstcommit);
$sc->addParent($c->sha());
$sc->setMessage("Merge develop into master.");
$sc->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1384158776', 'offset'=>'+0200'));
$sc->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1384158776', 'offset'=>'+0200'));
$sc->store();
echo "Created commit ".$sc->sha()."\n";


// Update develop branch's pointer
Git::writeFile($dir.'/refs/heads/develop', ''.$c->sha()."\n");
Git::writeFile($dir.'/refs/heads/master', ''.$sc->sha()."\n");

$tag = new GitTag($git);
$tag->setObject($oc);
$tag->setTag("v0.1");
$tag->setTagger(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'));
$tag->setMessage("Tagging the first commit...");
$tag->store();
echo "Created tag ".$tag->sha()."\n";

// Create the tag's reference
Git::writeFile($dir.'/refs/tags/'.$tag->tag(), ''.$tag->sha()."\n");
