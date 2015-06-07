<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace Git4p;

class GitUser {

    protected $name      = false;
    protected $email     = false;
    protected $timestamp = false;
    protected $offset    = false;

    public function __construct() {
    }

    public function __toString() {
        return sprintf('%s <%s> %s %s', $this->name, $this->email, $this->timestamp, $this->offset);
    }


    // Getters
    public function name() {
        return $this->name;
    }

    public function email() {
        return $this->email;
    }

    public function timestamp() {
        return $this->timestamp;
    }

    public function offset() {
        return $this->offset;
    }

    // Setters
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function setOffset($offset) {
        $this->offset = $offset;

        return $this;
    }

}
