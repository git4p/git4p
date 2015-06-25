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

    public function loadRefs() throw Exception {
        $refs = [];

        // load packed refs if any
        $packfile = $this->git->dir().'/packed-refs';
        if (file_exists($packfile) === true) {
            $file = trim(Git::readFile($packfile));
            $line = strtok($file, "\n");
            while ($line !== false) {
                $line = trim($line);
                if ($line{0} == '#' || $line{0} == '^') continue;
                if (strpos($line,' ') != 40) continue;
                $refs[substr($line,41)] = substr($line,0,40);
                $line = strtok("\n");
            }

            // Clean up memory
            strtok('', '');

        }

        if (count($refs) == 0) {
            throw new Exception("Unable to load refs.");
        }
        else {
            $this->refs = $refs;
        }
    }

    public function listRefs() {
        // Make sure refs are loaded
        if ($this->refs === false) {
            $this->loadRefs();
        }

        return $this->refs;

    }

}
