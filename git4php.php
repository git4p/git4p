<?php

/*

Git4PHP Library.

Development plan:

- Read only support
- Write support
- Management / cleanup support

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