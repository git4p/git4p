Git4P
=====

"Git4P" stands for "Git for PHP" and is a native PHP Git library that can access
a Git repository without using external help like the standard git commands.

Simple example
--------------

```php
$git = Git::init('/tmp/mytestrepo');

$readme = "GIT4P\n=====\n\nThis is a simple test repo for git4p.\n";

$blob = new GitBlob($git);
$blob->setData($readme)
     ->store();
```

This overly simplistic example creates a new bare repository and inside it, an
orphaned blob object. In other words an object that's not pointed to by any
tree object, etc.

Development stages
------------------

This library is being developed using the following rough phases or stages:

- Write support for bare repositories (no pack support)
- Read only support for bare repositories (no pack support)
- Read only support for bare repositories including pack support
- Write support for bare repositories including pack support

*Undecided since this would make it a non-server side library*

- Read support (i.e. did something change) for working dirs
- Write support for working dirs

Status of this library
----------------------

It can now:

- create a bare repo
- create git commit, blob and tree objects on disk
- create tags (simple and object) and branches
- read individual commit objects

