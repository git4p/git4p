<?php

class Git {
    const   BASEDIR     = '.git/';
    const   HEAD        = '.git/HEAD';
    
    public static function getHeadSha($repodir) {
        $headref = false;
        $filename = $repodir.self::HEAD;
        
        if (file_exists($filename) && is_readable ($filename)) {
            $headref = trim(file_get_contents($filename));
            $headref = explode(' ', $headref);
        }
        
        $filename = $repodir.self::BASEDIR.$headref[1];
        if (file_exists($filename) && is_readable ($filename)) {
            $rootsha = trim(file_get_contents($filename));
            return $rootsha;
        }
        
        return false;
    }
}