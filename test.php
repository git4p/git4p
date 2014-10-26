#!/usr/bin/php

<?php

include "git4php.php";

$readme = "GIT4P\n=====\n\nThis is a simple test repo for git4p.\n";
$dir = dirname(__FILE__).'/mytestrepo';
$git = false;

// Create the repo if necessary
if (file_exists($dir.'/HEAD') === false) {
    echo "Repo does not exist, creating on disk.\n";
    $git = Git::init($dir);
}
else {
    echo "Repo exists, creating reference object instance.\n";
    $git = new Git($dir);
}

echo "Simulate that a README file was commited and pushed to master.\n";
echo "Create a blob.\n";
$b = new GitBlob($git);
$b->setData($readme);
$b->store();

echo "Create a tree.\n";
$arr = array('README.md' => $b);
$t = new GitTree($git);
$t->setData($arr);
$t->store();

echo "-----\n$t\n-------\n";

echo "Create the commit.\n";
$c = new GitCommit($git);
$c->setTree($t->sha());
$c->setMessage("Initial commit.");
$c->setAuthor(array('name'=>'Martijn van der Kleijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'));
$c->setCommiter(array('name'=>'Martijn van der Kleijn', 'email'=>'<martijn.niji@gmail.com>', 'timestamp'=>'1374058686', 'offset'=>'+0200'));
$c->store();
echo $c;

// Make sure master head ref exists and points to commit
Git::writeFile($dir.'/refs/heads/master', ''.$c->sha()."\n");


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
