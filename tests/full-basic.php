#!/usr/bin/php
<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

 /* EXAMPLE FROM README */

 /*
  * How to use this example:
  *
  * - install php-cli if needed
  * - run it: php <filename>
  * - check the result using instaweb
  *   cd /tmp/mytestrepo
  *   git instaweb
  */

include '../src/git4p.php';

use Git4p\Git;
use Git4p\GitBlob;
use Git4p\GitCommit;
use Git4p\GitTree;
use Git4p\GitUser;

$git = Git::init('/tmp/mytestrepo');

$readme = "GIT4P\n=====\n\nThis is a simple test repo for git4p.\n";
$user = new GitUser();
$user->setName('Some User')
     ->setEmail('some.user@example.com')
     ->setTimestamp('1374058686')
     ->setOffset('+0200');

$b = new GitBlob($git);
$b->setData($readme)
  ->store();

$arr = ['README.md' => $b];
$t = new GitTree($git);
$t->setData($arr)
  ->store();

$c = new GitCommit($git);
$c->setTree($t->sha())
  ->setMessage('Initial commit.')
  ->addAuthor($user)
  ->addCommiter($user)
  ->store();

$git->updateBranch('master', $c->sha());
