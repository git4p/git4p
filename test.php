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
$c->setTree($t->sha())
  ->setMessage("Initial commit.")
  ->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'))
  ->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'))
  ->store();
echo "Created commit ".$c->sha()."\n";

$oc = $c;
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
$c->setTree($t->sha())
  ->addParent($oc->sha())
  ->setMessage("Update readme.")
  ->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'))
  ->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'))
  ->store();
echo "Created commit ".$c->sha()."\n";

$p = $c->sha();

// Add a commit to develop
$b = new GitBlob($git);
$b->setData("Altered README.MD file!\n");
$b->store();
echo "Created blob   ".$b->shortSha()."\n";

$arr = array('README.md' => $b);
$t = new GitTree($git);
$t->setData($arr);
$t->store();
echo "Created tree   ".$t->shortSha()."\n";

$c = new GitCommit($git);
$c->setTree($t->sha())
  ->addParent($p)
  ->setMessage("Correct readme.")
  ->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374158776', 'offset'=>'+0200'))
  ->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374158776', 'offset'=>'+0200'))
  ->store();
echo "Created commit ".$c->shortSha()."\n";

$sc = new GitCommit($git);
$sc->setTree($t->sha())
   ->addParent($firstcommit)
   ->addParent($c->sha())
   ->setMessage("Merge develop into master.")
   ->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1384158776', 'offset'=>'+0200'))
   ->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1384158776', 'offset'=>'+0200'))
   ->store();
echo "Created commit ".$sc->shortSha()."\n";


// Update develop branch's pointer
Git::writeFile($dir.'/refs/heads/develop', ''.$c->sha()."\n");
Git::writeFile($dir.'/refs/heads/master', ''.$sc->sha()."\n");

$tag = new GitTag($git);
$tag->setObject($oc);
$tag->setTag("v0.1");
$tag->setTagger(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'));
$tag->setMessage("Tagging the first commit...");
$tag->store();
echo "Created tag    ".$tag->shortSha()."\n";

// Create the tag's reference
Git::writeFile($dir.'/refs/tags/'.$tag->tag(), ''.$tag->sha()."\n");
