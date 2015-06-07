#!/usr/bin/php
<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

 /* QUICK TEST/EXAMPLE FILE */

include 'src/git4p.php';

use Git4p\Git;
use Git4p\GitCommit;

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
    echo '* '.$c->shortSha()."\n";

    if (count($c->parents()) >= 1) {
        echo "| \n";
        $p = $c->parents();
        $c2 = new GitCommit($c->git());
        $c2 = $c2->load($p[0]);
        showLog($c2);
    }
}



//var_dump($c);

