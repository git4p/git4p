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
    const OBJ_OFS_DELTA = 6;
    const OBJ_REF_DELTA = 7;

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
        return $this->readObjectData($this->findObject($sha));
    }

    public function findObject($sha) {
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
        return $idx->getInt();
    }

    public static function memcpy($src, $srcPos, &$dst, $dstPos, $length)
    {
        for ($i = 0; $i < $length; $i++)
            $dst[$dstPos + $i] = $src[$srcPos + $i];
    }

    // very direct port from jgit
    public static function applyDelta($base, $delta)
    {
        list($baseHeader, $baseData, ) = explode("\0", $base);
        list($baseType, $baseDataLen, ) = explode(' ', $baseHeader);

        $resultPtr = 0;
        $deltaPtr = 0;

        $baseLen = 0;
        $shift = 0;
        do {
            $c = ord($delta[$deltaPtr++]);
            $baseLen |= ($c & 0x7f) << $shift;
            $shift += 7;
        } while ($c & 0x80);

        if ($baseLen != $baseDataLen)
            throw new \Exception('base length incorrect');

        $resLen = 0;
        $shift = 0;
        do {
            $c = ord($delta[$deltaPtr++]);
            $resLen |= ($c & 0x7f) << $shift;
            $shift += 7;
        } while ($c & 0x80);

        $result = str_repeat("\0", $resLen);

        while ($deltaPtr < strlen($delta)) {
            $cmd = ord($delta[$deltaPtr++]);

            if (($cmd & 0x80) != 0) {
                $copyOffset = 0;
                if ($cmd & 0x01)
                    $copyOffset = ord($delta[$deltaPtr++]);
                if ($cmd & 0x02)
                    $copyOffset |= ord($delta[$deltaPtr++]) << 8;
                if ($cmd & 0x04)
                    $copyOffset |= ord($delta[$deltaPtr++]) << 16;
                if ($cmd & 0x08)
                    $copyOffset |= ord($delta[$deltaPtr++]) << 24;

                $copySize = 0;
                if ($cmd & 0x10)
                    $copySize = ord($delta[$deltaPtr++]);
                if ($cmd & 0x20)
                    $copySize |= ord($delta[$deltaPtr++]) << 8;
                if ($cmd & 0x40)
                    $copySize |= ord($delta[$deltaPtr++]) << 16;

                if ($copySize == 0)
                    $copySize = 0x10000;

                self::memcpy($baseData, $copyOffset, $result, $resultPtr, $copySize);
                $resultPtr += $copySize;
            } elseif ($cmd != 0) {
                self::memcpy($delta, $deltaPtr, $result, $resultPtr, $cmd);
                $deltaPtr += $cmd;
                $resultPtr += $cmd;
            } else {
                throw new \Exception('Zero delta command reserved.');
            }
        }

        return static::typeString($baseType) . " $resLen\0" . $result;
    }

    protected function readObjectData($pack_offset) {
        if ($pack_offset === false)
            return false;

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

        if ($type == self::OBJ_OFS_DELTA || $type == self::OBJ_REF_DELTA) {

            $delta = false;
            $baseObject = false;

            if ($type == self::OBJ_OFS_DELTA) {
                $c = $pack->getByte();
                $off = $c & 127;
                while ($c & 128) {
                    $off += 1;
                    $c = $pack->getByte();
                    $off = ($off << 7) + ($c & 127);
                }

                $delta = $pack->inflate($size);
                $base = $this->readObjectData($pack_offset - $off);
            } else {
                // completely untested
                $baseSha = $pack->getSha();
                $delta = $pack->inflate($size);
                $base = $this->getObject($baseSha);
            }

            return static::applyDelta($base, $delta);
        }

        return static::typeString($type) . " $size\0" . $pack->inflate($size);
    }
}
