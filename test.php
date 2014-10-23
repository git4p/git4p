#!/usr/bin/php

<?php

include "git4php.php";

$readme = "GIT4P\n=====\n\nThis is a simple test repo for git4p."
$dir = dirname(__FILE__).'/mytestrepo';
$git = false;

// Create the repo if necessary
if (file_exists($dir.'/HEAD') === false) {
    $git = Git::init($dir);
}
else {
    $git = new Git($dir);
}

// Simulate that a README file was commited and pushed to master
// First create a blob
$b = new GitBlob($git, $readme);
// Then create a tree
$t = new GitTree($git, array($b));
// Then create the commit
$c = new GitCommit




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
