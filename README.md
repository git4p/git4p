
**Status:** No longer active / archived


[![Build Status](https://travis-ci.org/git4p/git4p.svg?branch=master)](https://travis-ci.org/git4p/git4p)
[![Codacy Badge](https://api.codacy.com/project/badge/coverage/0fcd1628445e421791b7a39bbefe41ff)](https://www.codacy.com/app/martijn-niji/git4p)
[![Codacy Badge](https://api.codacy.com/project/badge/grade/0fcd1628445e421791b7a39bbefe41ff)](https://www.codacy.com/app/martijn-niji/git4p)
[![StyleCI](https://styleci.io/repos/35836750/shield)](https://styleci.io/repos/35836750)

Git4P
=====

"Git4P" stands for "Git for PHP" and is a native PHP Git library that can access
a Git repository without using external help like the standard git commands. It
is geared towards server side (bare) repositories.

Please let me know how you're using this library.

Examples
--------

### Create a new bare repository

```php
Git::init('/tmp/mytestrepo');
```

### Add a simple blob

In this example the blob is 'orphaned', which means it is unreachable through any
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

$blob = new GitBlob($git);
$blob->setData($readme)
     ->store();

$arr = ['README.md' => $b];
$tree = new GitTree($git);
$tree->setData($arr)
     ->store();

$commit = new GitCommit($git);
$commit->setTree($tree->sha())
       ->setMessage('Initial commit.')
       ->addAuthor($user)
       ->addCommiter($user)
       ->store();

$git->updateBranch('master', $commit->sha());
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
