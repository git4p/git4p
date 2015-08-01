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
use Git4p\GitBlob;
use Git4p\GitCommit;
use Git4p\GitTag;
use Git4p\GitTree;
use Git4p\GitUser;
use Git4p\GitRef;

// Test setup
$dir = dirname(__FILE__).'/wolfcms.git';
$git = false;

echo "Creating reference object to repository.\n";
$git = new Git($dir);
$gitref = new GitRef($git);

// try {
//     var_dump($gitref->loadRefs());
// }
// catch(Exception $e) {
//     var_dump($e);
// }

echo "List all refs\n";
foreach($gitref->listRefs() as $ref => $sha) {
    echo "$ref\n";
}

echo "Test if ref exists\n";
var_dump($gitref->exists('heads/develop'));

echo "Get sha for ref develop\n";
echo $gitref->ref('heads/develop')."\n";
