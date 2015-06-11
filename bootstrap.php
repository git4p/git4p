<?php

ini_set('include_path', ini_get('include_path')
                        .PATH_SEPARATOR.dirname(__FILE__).'/src/');

require_once 'Git.php';
require_once 'GitObject.php';
require_once 'GitBlob.php';
require_once 'GitTree.php';
require_once 'GitCommit.php';
require_once 'GitTag.php';
require_once 'GitUser.php';
