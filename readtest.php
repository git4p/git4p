#!/usr/bin/php
<?php

include "git4php.php";

// Test setup
$dir = dirname(__FILE__).'/mytestrepo';
$git = false;

$git = new Git($dir);

$tip = $git->getTip('master');

$c = new GitCommit($git);
$c = $c->load($tip);
echo "commit ".substr($c->sha(),0,7)."\n";

