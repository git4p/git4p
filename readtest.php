#!/usr/bin/php
<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

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

echo "Commit tree for master branch\n";
echo "-----------------------------\n\n";
$c = new GitCommit($git);
$c = $c->load($tip);
showLog($c);

function showLog($c) {
    echo "* ".$c->shortSha()."\n";
    
    if (count($c->parents()) >= 1) {
        echo "| \n";
        $p = $c->parents();   
        $c2 = new GitCommit ($c->git());
        $c2 = $c2->load($p[0]);
        showLog($c2);
    }
}



//var_dump($c);
