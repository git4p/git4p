<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace Git4p;

/**
 * A Git object is stored in a subdirectory that's named for the first two
 * characters of the object's SHA1. The object's filename is the remaining
 * 38 characters.
 */
abstract class GitObject {

    const TYPE_COMMIT = 'commit';
    const TYPE_TREE   = 'tree';
    const TYPE_BLOB   = 'blob';
    const TYPE_TAG    = 'tag';

    // Back reference to main Git repository object
    protected $git      = false;

    // Generic variables for Git objects
    protected $sha      = false;
    protected $rawdata  = false;

    public function __construct($git) {
        $this->git = $git;
    }

    public function __toString() {
        return sprintf('%6s %s', $this->type(), $this->shortSha());
    }


    // GETTERS
    public function sha() {
        if ($this->sha === false) {
            $this->sha = sha1($this->header().$this->data());
        }

        return $this->sha;
    }

    public function shortSha() {
        return substr($this->sha(), 0, 8);
    }

    public function location() {
        return substr($this->sha(), 0, 2);
    }

    public function filename() {
        return substr($this->sha(), 2);
    }

    public function data() {
        return $this->rawdata;
    }

    public function header() {
        return sprintf("%s %d\0", $this->type(), strlen($this->data()));
    }

    public function mode() {
        return $this->mode;
    }

    public function git() {
        return $this->git;
    }


    // SETTERS
    public function setData($data) {
        $this->rawdata = $data;

        return $this;
    }


    // STORAGE
    public function store() {
        $path = sprintf('%s/%s/%s', $this->git->dir(), Git::DIR_OBJECTS, $this->location());

        if (file_exists($path) === false) {
            $result = mkdir($path, 0774, true);
            if ($result === false) {
                throw new Exception('Unable to create path '.$path);
            }
        }

        Git::writeFile($path.'/'.$this->filename(), $this->header().$this->data(), true);
    }

    public function loadRawData($sha) {
        $path = sprintf('%s/%s/%s/%s', $this->git->dir(), Git::DIR_OBJECTS, substr($sha, 0, 2), substr($sha, 2));
        $data = Git::readFile($path, true);
        $data = explode("\0", $data, 2);

        return $data[1];
    }

    /*
    public function __toString() {
        $ret = '';
        $vars = get_object_vars($this);
        ksort($vars);

        foreach($vars as $key => $var) {
            if ($key == 'rawdata') continue;
            $ret .= sprintf("%s: %s\n", $key, print_r($var, true));
        }

        $ret .= "\n";
        return $ret;
    }*/
}
