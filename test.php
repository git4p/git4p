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
echo "Create a blob.\n";
$b = new GitBlob($git);
$b->setData($readme);
$b->store();
echo "Created blob ".$b->sha()."\n";

echo "Create a tree.\n";
$arr = array('README.md' => $b);
$t = new GitTree($git);
$t->setData($arr);
$t->store();
echo "Created tree ".$t->sha()."\n";

echo "Create the commit.\n";
$c = new GitCommit($git);
$c->setTree($t->sha());
$c->setMessage("Initial commit.");
$c->addAuthor(array('name'=>'Martijn van der Kleijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'));
$c->addCommiter(array('name'=>'Martijn van der Kleijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'));
$c->store();
$oc = $c;
echo "Created commit ".$c->sha()."\n";

// Make sure master head ref exists and points to commit
Git::writeFile($dir.'/refs/heads/master', ''.$c->sha()."\n");

// Lets create an extra branch called 'develop'
Git::writeFile($dir.'/refs/heads/develop', ''.$c->sha()."\n");

// Add a commit to develop
echo "Create a blob.\n";
$b = new GitBlob($git);
$b->setData("Altered README.md file!!!\n");
$b->store();
echo "Created blob ".$b->sha()."\n";

echo "Create a tree.\n";
$arr = array('README.md' => $b);
$t = new GitTree($git);
$t->setData($arr);
$t->store();
echo "Created tree ".$t->sha()."\n";

echo "Create the commit.\n";
$c = new GitCommit($git);
$c->setTree($t->sha());
$c->addParent($oc->sha());
$c->setMessage("Update readme.");
$c->addAuthor(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'));
$c->addCommiter(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'));
$c->store();
echo "Created commit ".$c->sha()."\n";

// Update develop branch's pointer
Git::writeFile($dir.'/refs/heads/develop', ''.$c->sha()."\n");

echo "Create a tag for the first commit\n";
$tag = new GitTag($git);
$tag->setObject($oc);
$tag->setTag("v0.1");
$tag->setTagger(array('name'=>'Martijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058776', 'offset'=>'+0200'));
$tag->setMessage("Tagging the first commit...");
$tag->store();
echo "Created tag ".$tag->sha()."\n";

// Create the tag's reference
Git::writeFile($dir.'/refs/tags/'.$tag->tag(), ''.$tag->sha()."\n");

/*
$head = $git->getHeadObject();

echo "HEAD COMMIT OBJ\n";
echo $head."\n";

echo "HEAD TREE OBJ\n";
$tree = $git->getObject($head->getTree());
echo $tree."\n";

/*
$sha = Git::getHeadSha($repodir);
$type = GitObject::TYPE_COMMIT;

$go = new GitCommit($repodir, $sha);

//echo "Root sha: ".$go->getSha1()."\n";
//echo "Tree sha: ".$go->getTree()."\n";
$tree = new GitTree($repodir, $go->getTree());
echo "Tree content: ".$tree->getContent()."\n";

while ($go->getParent() !== false) {
    $go = new GitCommit($repodir, $go->getParent());
    //echo "Child sha: ".$go->getSha1()."\n";
}
 * 
 */
