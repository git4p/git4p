<?php

/*
 * This file is part of the Git4P library.
 *
 * Copyright (c) 2015 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Copyright (c) 2015 Toni Spets <toni.spets@iki.fi>
 * Licensed under the MIT license <http://opensource.org/licenses/MIT>
 */

namespace Git4p;

use \Exception;

/**
 * Class to search through git pack files.
 *
 * @todo Add support for delta compression
 * @todo Possibly needs a rewrite
 */
class GitPack {

    protected $idx_path;
    protected $idx_reader;
    protected $idx_fanout = [];
    protected $idx_size;

    protected $pack_path;
    protected $pack_reader;

    const OBJ_COMMIT = 1;
    const OBJ_TREE = 2;
    const OBJ_BLOB = 3;
    const OBJ_TAG = 4;
    const OBJ_OFS_DELTA = 5;
    const OBJ_REF_DELTA = 6;

    public static function typeString($type) {
        switch ($type) {
            case self::OBJ_COMMIT: return 'commit';
            case self::OBJ_TREE: return 'tree';
            case self::OBJ_BLOB: return 'blob';
            default: return '?';
        }
    }

    public static function readObject($git, $sha) {
        $packs = sprintf('%s/objects/pack/', $git);
        if (!is_dir($packs))
            throw new Exception($packs . ' is not a directory.');

        foreach (glob($packs . '*.idx') as $idx) {
            $path = pathinfo($idx);
            $pack = new self($git, $path['filename']);
            $ref = $pack->getObject($sha);
            if ($ref !== false)
                return $ref;
        }

        return false;
    }

    public function __construct($git, $base) {
        $this->idx_path = sprintf('%s/objects/pack/%s.idx', $git, $base);
        $this->pack_path = sprintf('%s/objects/pack/%s.pack', $git, $base);
    }

    protected function getIndex() {
        if (!$this->idx_reader) {
            $this->idx_reader = new FileBinaryReader($this->idx_path);

            if ($this->idx_reader->read(4) != "\377tOc")
                throw new \Exception('Index file magic is invalid.');

            if ($this->idx_reader->getInt() != 2)
                throw new \Exception('Index file version not 2.');

            $prev = 0;
            for ($i = 0; $i < 256; $i++) {
                $v = $this->idx_reader->getInt();
                $this->idx_fanout[$i] = [ $v - $prev, $v ];
                $prev = $v;
            }

            $this->idx_size = $prev;
        }

        return $this->idx_reader;
    }

    protected function getPack() {
        if (!$this->pack_reader) {
            $this->pack_reader = new FileBinaryReader($this->pack_path);

            if ($this->pack_reader->read(4) != 'PACK')
                throw new \Exception('Pack file magic is invalid.');

            if ($this->pack_reader->getInt() > 2)
                throw new \Exception('Pack file version not 2.');

            $pack_size = $this->pack_reader->getInt();
        }

        return $this->pack_reader;
    }

    public function getObject($sha) {
        $idx = $this->getIndex();

        $first = hexdec(substr($sha, 0, 2));
        $offset = false;
        $num = 0;

        foreach ($this->idx_fanout as $k => $d) {
            if ($k == $first) {
                $offset = $d[1];
                $num = $d[0];
                break;
            }
        }

        if ($offset === false) return false;

        $idx->setPos(2 * 4 + 256 * 4 + ($offset - $num) * 20);

        $exact_offset = false;
        for ($i = 0; $i < $num; $i++) {
            if ($idx->getSha() == $sha) {
                $exact_offset = $i;
                break;
            }
        }

        if ($exact_offset === false) return false;

        $exact_offset += $offset - $num;

        $idx->setPos(2 * 4 + 256 * 4 + $this->idx_size * 20 + $exact_offset * 4);
        $crc32 = $idx->getInt();

        $idx->setPos(2 * 4 + 256 * 4 + $this->idx_size * 20 + $this->idx_size * 4 + $exact_offset * 4);
        $pack_offset = $idx->getInt();

        $pack = $this->getPack();
        $pack->setPos($pack_offset);

        $b = $pack->getByte();

        $type = ($b &~ (1 << 7)) >> 4;
        $size = $b & 0xF;

        $sh = 4;
        while ($b & (1 << 7)) {
            $b = $pack->getByte();
            $size |= (($b &~ (1 << 7)) << $sh);
            $sh += 7;
        }

        if ($type > self::OBJ_TAG)
            throw new \Exception('Delta packed objects not implemented (yet)');

        return static::typeString($type) . " $size\0" . $pack->inflate($size);
    }
}
