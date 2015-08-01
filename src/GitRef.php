<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace Git4p;

use \Exception;

/**
 */
class GitRef {

    private $git  = false;
    private $refs = false;

    public function __construct($git) {
        $this->git = $git;
    }

    private function loadRefs() {
        $refs = [];

        // load packed refs if any
        $packfile = $this->git->dir().'/packed-refs';
        if (file_exists($packfile) === true) {
            $file = trim(Git::readFile($packfile));
            $line = strtok($file, "\n");
            while ($line !== false) {
                $line = trim($line);
                if ($line{0} != '#' && $line{0} != '^') {
                    if (strpos($line,' ') == 40)
                        $refs[substr($line,46)] = substr($line,0,40);
                }
                $line = strtok("\n");
            }

            // Clean up memory
            strtok('', '');

        }

        $this->refs = $refs;
    }

    private function refs() {
        // Make sure refs are loaded
        if ($this->refs === false) {
            $this->loadRefs();
        }

        return $this->refs;
    }

    // I'd prefer to call this list() but unfortunately that's a reserved word.
    public function listRefs() {
        return $this->refs();
    }

    public function exists($ref) {
        return array_key_exists($ref, $this->refs());
    }

    public function ref($ref) {
        if ($this->exists($ref)) {
            return $this->refs()[$ref];
        }

        throw new Exception('Ref does not exist.');
    }

}
