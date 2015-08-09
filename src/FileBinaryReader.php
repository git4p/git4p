<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Copyright (c) 2015 Toni Spets <toni.spets@iki.fi>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace Git4p;

/**
 * Reads binary data from files efficiently.
 */
class FileBinaryReader {
    private $path;
    private $fh;
    private $pos;
    private $len;

    public function __construct($path) {
        $this->path = $path;
        $this->fh = fopen($path, 'rb');
        $this->pos = 0;
    }

    public function __destruct() {
        fclose($this->fh);
    }

    public function read($num) {
        $ret = fread($this->fh, $num);

        if ($ret === false)
            throw new \Exception('Read failed');

        $this->pos += $num;

        return $ret;
    }

    public function getPos() {
        return $this->pos;
    }

    public function setPos($pos) {
        fseek($this->fh, $pos);
        $this->pos = $pos;
    }

    public function getByte() {
        return unpack('C', $this->read(1))[1];
    }

    public function getInt() {
        return unpack('N', $this->read(4))[1];
    }

    public function getSha() {
        return bin2hex($this->read(20));
    }

    public function inflate($num) {
        fseek($this->fh, $this->pos); // avoid bug in PHP stream buffering when applying a filter
        $filter = stream_filter_append($this->fh, 'zlib.inflate', STREAM_FILTER_READ, ['window' => 15]);
        $ret = fread($this->fh, $num);
        stream_filter_remove($filter);
        $this->pos = ftell($this->fh);

        if ($ret === false)
            throw new \Exception('Inflate read failed');

        return $ret;
    }
}
