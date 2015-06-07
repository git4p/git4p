<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace Git4p;

/**

Format:

-------------------------------------------
commit <content size>\0
tree <full sha1>
[parent <full sha1>]
author <name> <<email>> <timestamp> <offset>
committer <name> <<email>> <timestamp> <offset>

<message>
-------------------------------------------

note: in case of merge, multiple parent entries

*/
class GitCommit extends GitObject {

    /* Commit object specific variables */
    protected $tree       = false,
              $parents    = [],
              $authors    = [],
              $committers = [],
              $message    = false;

    public function __construct($git) {
        parent::__construct($git);
    }


    // Getters
    public function type() {
        return GitObject::TYPE_COMMIT;
    }

    public function tree() {
        return $this->tree;
    }

    public function message() {
        return $this->message;
    }

    public function authors() {
        return $this->authors;
    }

    public function committers() {
        return $this->committers;
    }

    public function parents() {
        return $this->parents;
    }

    public function data() {
        $data = '';

        $data .= sprintf("tree %s\n", $this->tree());
        foreach ($this->parents() as $parent) {
            $data .= sprintf("parent %s\n", $parent);
        }

        foreach ($this->authors() as $author) {
            $data .= sprintf("author %s\n", $author);
        }

        foreach ($this->committers as $committer) {
            $data .= sprintf("committer %s\n", $committer);
        }

        $data .= sprintf("\n%s", $this->message());

        return $data;
    }



    // Setters
    public function setTree($sha) {
        $this->tree = $sha;

        return $this;
    }

    public function setMessage($data) {
        $this->message = $data;

        return $this;
    }

    public function addParent($data) {
        $this->parents[] = $data;

        return $this;
    }

    public function addAuthor($data) {
        $this->authors[] = $data;

        return $this;
    }

    public function addCommiter($data) {
        $this->committers[] = $data;

        return $this;
    }


    // TODO REMOVE??
    public function getAuthorTimestamp($asDate=false, $format='D M j G:i:s Y O') {
        if ($asDate === true) {
            return date($format, $this->aTimestamp);
        }

        return $this->aTimestamp;
    }


    // Loader function
    public function load($sha) {

        $this->tree       = false;
        $this->parents    = [];
        $this->authors    = [];
        $this->committers = [];
        $this->message    = false;

        $lines = explode("\n", $this->loadRawData($sha));

        $message = '';

        foreach($lines as $line) {
            $line = trim($line);
            $elements = explode(' ', $line, 2);

            if (count($elements) == 1) {
                $message .= $elements[0];
                continue;
            }

            switch($elements[0]) {
                case 'tree':
                    $this->setTree($elements[1]);
                    break;
                case 'parent':
                    $this->addParent($elements[1]);
                    break;
                case 'author':
                    preg_match('/^(.+?)\s+<(.+?)>\s+(\d+)\s+([+-]\d{4})$/', $elements[1], $m);
                    $user = new GitUser();
                    $off = intval($m[4]);
                    $user->setName($m[1])
                         ->setEmail($m[2])
                         ->setTimestamp(intval($m[3]))
                         ->setOffset($m[4]);
                    $this->addAuthor($user);
                    break;
                case 'committer':
                    preg_match('/^(.+?)\s+<(.+?)>\s+(\d+)\s+([+-]\d{4})$/', $elements[1], $m);
                    $user = new GitUser();
                    $off = intval($m[4]);
                    $user->setName($m[1])
                         ->setEmail($m[2])
                         ->setTimestamp(intval($m[3]))
                         ->setOffset($m[4]);
                    $this->addCommiter($user);
                    break;
                default:
                    $message .= $elements[0].' '.$elements[1];
                    break;
            }
        }

        $this->setMessage($message);

        return $this;
    }
}
