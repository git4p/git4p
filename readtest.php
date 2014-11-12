#!/usr/bin/php
<?php

include "git4php.php";

use org\git4p\Git;
use org\git4p\GitUser;
use org\git4p\GitBlob;
use org\git4p\GitTree;
use org\git4p\GitCommit;
use org\git4p\GitTag;

// Test setup
$dir = dirname(__FILE__).'/mytestrepo';
$git = false;

$git = new Git($dir);

$tip = $git->getTip('master');

echo "Tip of master: $tip\n";

$c = new GitCommit($git);
$c = $c->load($tip);
echo "commit ".$c->sha()."\n";

//var_dump($c);
