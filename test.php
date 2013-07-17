#!/usr/bin/php

<?php

include "git4php.php";

$dir = '/home/klm23563/projects/git4php/testrepo/';
$git = new Git($dir);

$head = $git->getHead();

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