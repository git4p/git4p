#!/usr/bin/php

<?php

date_default_timezone_set('UTC');

include "git4php.php";

$dir = dirname(__FILE__).'/testrepo/';
$git = new git4p\Git($dir);

//
// Test if we can figure out HEAD information
//
echo "HEAD tests\n------------------\n\n";

echo "MASTER branch HEAD\n------------------------------\n";
$head = $git->getHead();
echo 'SHA: '.$head->getSha()."\n";
echo 'Author: '.$head->getAuthorName()."\n";
echo 'Tree: '.$head->getTree()."\n\n";

echo "ALT-BRANCH branch HEAD\n------------------------------\n";
$head = $git->getHead('alt-branch');
echo 'SHA: '.$head->getSha()."\n";
echo 'Author: '.$head->getAuthorName()."\n";
echo 'Tree: '.$head->getTree()."\n\n";
