<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace Git4p;

/**
 * Abstract class for generic git objects.
 *
 * The basic storage format for any object is:
 * <code>
 * [type] [content lenght]NUL[data]
 * </code>
 *
 * There are four objects: blob, tree, commit and tag.
 */
abstract class GitObject {

    // The four Git object types
    const TYPE_COMMIT = 'commit';
    const TYPE_TREE   = 'tree';
    const TYPE_BLOB   = 'blob';
    const TYPE_TAG    = 'tag';

    // Back reference to main Git repository object
    protected $git      = false;

    // Generic variables for Git objects
    protected $sha      = false;
    protected $rawdata  = false;

    /**
     * @param   object  $git    Backreference to main Git object.
     */
    public function __construct($git) {
        $this->git = $git;
    }

    /**
     * Basic **__toString** implementation that returns:
     * a string conforming to <code>[type] [short sha1]</code>
     */
    public function __toString() {
        return sprintf('%6s %s', $this->type(), $this->shortSha());
    }

    /**
     * Returns full sha1 for an object.
     *
     * @return  string  Full sha1 for object.
     */
    public function sha() {
        if ($this->sha === false) {
            $this->sha = sha1($this->header().$this->data());
        }

        return $this->sha;
    }

    /**
     * Returns short sha1 code.
     *
     * @return  string  Short 8 character sha1 for object.
     */
    public function shortSha() {
        return substr($this->sha(), 0, 8);
    }

    /**
     * Returns the directory in which the object should be stored.
     *
     * @return  string  Two character directory name.
     */
    public function location() {
        return substr($this->sha(), 0, 2);
    }

    /**
     * Returns the Git object filename.
     *
     * @return  string  Filename based on sha1 of object.
     */
    public function filename() {
        return substr($this->sha(), 2);
    }

    /**
     * @todo make private/protected?
     */
    public function data() {
        return $this->rawdata;
    }

    /**
     * @todo make private/protected?
     */
    public function header() {
        return sprintf("%s %d\0", $this->type(), strlen($this->data()));
    }

    public function mode() {
        return $this->mode;
    }

    /**
     * @todo remove?
     */
    public function git() {
        return $this->git;
    }

    abstract public function type();

    // SETTERS
    public function setData($data) {
        $this->rawdata = $data;

        return $this;
    }

    /**
     * Stores the object on disk.
     *
     * @throws  Exception   'Unable to create path [filename]'
     */
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

    /**
     * Loads an object's data from disk based on the object's sha1.
     *
     * @param   string  $sha    Sha1 for object to load.
     */
    public function loadRawData($sha) {
        $path = sprintf('%s/%s/%s/%s', $this->git->dir(), Git::DIR_OBJECTS, substr($sha, 0, 2), substr($sha, 2));

        if (file_exists($path)) {
            $data = Git::readFile($path, true);
        } else {
            $data = GitPack::readObject($this->git->dir(), $sha);
        }

        $data = explode("\0", $data, 2);

        return $data[1];
    }

}
