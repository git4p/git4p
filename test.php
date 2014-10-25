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

$f0 = Git::readFile(dirname(__FILE__).'/mytestrepo/objects/'.$b->location().'/'.$b->filename(), true);
echo ">>>>".$f0."<<<<\n";

$f1 = Git::readFile(dirname(__FILE__).'/testrepo/.git/objects/1c/09411743e0a29fb883363e9c70b6434da6f873', true);
echo ">>>>".$f1."<<<<\n";
//$f2 = Git::readFile(dirname(__FILE__).'/testrepo/.git/objects/44/04422a3f4d83bc5e0eaa118d604d165919afd6', true);
//echo $f2."\n==//==";
//$f3 = Git::readFile(dirname(__FILE__).'/testrepo/.git/objects/4b/825dc642cb6eb9a060e54bf8d69288fbee4904', true);
//echo $f3."\n==//==";
//$f4 = Git::readFile(dirname(__FILE__).'/testrepo/.git/objects/c5/b0c3a4822757a4bf098e055260299fe418dc64', true);
//echo $f4."\n==//==";


// Then create a tree
//$t = new GitTree($git, array($b));
// Then create the commit
//$c = new GitCommit




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
