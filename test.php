#!/usr/bin/php

<?php

date_default_timezone_set('UTC');

include "git4php.php";

$dir = dirname(__FILE__).'/testrepo/';
$git = new Git($dir);

$head = $git->getHeadObject();

echo "HEAD COMMIT OBJ\n";
//echo $head."\n";

echo 'Author timestamp for commit: '.$head->getAuthorTimestamp(true)."\n\n";

echo "HEAD TREE OBJ\n\n";
$tree = $git->getObject($head->getTree());
echo $tree."\n";

//var_dump($tree);

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
