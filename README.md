[![StyleCI](https://styleci.io/repos/35836750/shield)](https://styleci.io/repos/35836750)
[![Code Climate](https://codeclimate.com/github/git4p/git4p/badges/gpa.svg)](https://codeclimate.com/github/git4p/git4p)

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

Note
----

This library currently has no pack support. Not for actual packs nor for packed
refs. Support is currently being worked on however.

Some TODOs
----------

- Add support for packed-refs
- Add pack support
- Add proper unit tests
- Improve examples in readme
- Add proper phpdoc blocks
- ~~Add proper styling (PSR-2, without braces, with other extra measures)~~
- ~~Add proper autoloading (PSR-4)~~
- Add support for logging? (PSR-3)
