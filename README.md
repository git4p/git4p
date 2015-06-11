[![StyleCI](https://styleci.io/repos/35836750/shield)](https://styleci.io/repos/35836750)
[![Code Climate](https://codeclimate.com/github/git4p/git4p/badges/gpa.svg)](https://codeclimate.com/github/git4p/git4p)
[![Build Status](https://drone.io/github.com/git4p/git4p/status.png)](https://drone.io/github.com/git4p/git4p/latest)

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

This library currently has no pack support. Not for actual packs nor for packed
refs. Support is currently being worked on however.

Some TODOs
----------

- Add support for packed-refs
- Add pack support
- Add proper unit tests
- ~~Add some basic examples in readme~~
- Add proper phpdoc blocks
- ~~Add proper styling (PSR-2, without braces, with other extra measures)~~
- ~~Add proper autoloading (PSR-4)~~
- Add support for logging? (PSR-3)
