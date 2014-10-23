<?php

namespace git4p;

/*

Git4PHP Library.

Development plan:

- Add read only support for single objects
- Add pack support
- Add write support
- Add management / cleanup support (git gc)

 */

function startsWith($haystack, $needle) {
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

include("Git.php");
include("GitObject.php");
include("GitCommit.php");
include("GitTree.php");
include("GitBlob.php");