[![Build Status](https://travis-ci.org/git4p/git4p.svg?branch=master)](https://travis-ci.org/git4p/git4p)
[![Code Climate](https://codeclimate.com/github/git4p/git4p/badges/gpa.svg)](https://codeclimate.com/github/git4p/git4p)
[![Test Coverage](https://codeclimate.com/github/git4p/git4p/badges/coverage.svg)](https://codeclimate.com/github/git4p/git4p/coverage)
[![StyleCI](https://styleci.io/repos/35836750/shield)](https://styleci.io/repos/35836750)

Git4P
=====

"Git4P" stands for "Git for PHP" and is a native PHP Git library that can access
a Git repository without using external help like the standard git commands.

Examples
--------

### Create a new bar repository

```php
Git::init('/tmp/mytestrepo');
```

### Add a simple blob

In this example the blob is 'orphaned' which means it is unreachable through any
commits.

```php
$git = Git::init('/tmp/mytestrepo');

$readme = "GIT4P\n=====\n\nThis is a simple test repo for git4p.\n";

$blob = new GitBlob($git);
$blob->setData($readme)
     ->store();
```

### Full basic repository with a single commit

We programmatically create a blob (file), a tree (directory) that points to it
and then a commit pointing to the tree. Finally we make sure the `master` branch
points to this commit.

```php
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
```

Note
----

This library currently has limited pack support. Reading in and listing packed
refs is supported through GitRef but support for packed objects is not implemented
yet.

Some TODOs
----------

- Add pack support
- Add proper phpdoc blocks
