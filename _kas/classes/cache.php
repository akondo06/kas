<?php
/*
	Name: Cache Class
	Version: 1.0
	Source Author: http://www.jongales.com/blog/2009/02/18/simple-file-based-php-cache-class/
*/	
class Cache {
	private $dir = null;
	
    public function __construct($dir) {
        $this->dir = $dir;
    }

    private function _name($key) {
        return sprintf("%s/%s", $this->dir, sha1($key));
    }

    public function get($key, $expiration = 3600) {
        if(!is_dir($this->dir) || !is_writable($this->dir)) {
            return false;
        }

        $cache_path = $this->_name($key);
        if(!@file_exists($cache_path)) {
            return false;
        }

        if(filemtime($cache_path) < (time() - $expiration)) {
            $this->clear($key);
            return false;
        }

        if(!$fp = @fopen($cache_path, 'rb')) {
            return false;
        }

        flock($fp, LOCK_SH);
        $cache = '';

        if(filesize($cache_path) > 0) {
            $cache = unserialize(fread($fp, filesize($cache_path)));
        } else {
            $cache = null;
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $cache;
    }

    public function set($key, $data) {
        if(!is_dir($this->dir) || !is_writable($this->dir)) {
            return false;
        }

        $cache_path = $this->_name($key);
        if(!$fp = fopen($cache_path, 'wb')) {
            return false;
        }

        if(flock($fp, LOCK_EX))  {
            fwrite($fp, serialize($data));
            flock($fp, LOCK_UN);
        } else {
            return false;
        }
        fclose($fp);
        @chmod($cache_path, 0777);
        return true;
    }

    public function clear($key) {
        $cache_path = $this->_name($key);

        if(file_exists($cache_path)) {
            unlink($cache_path);
            return true;
        }

        return false;
    }
    
    public function clear_all() {
		$return = false;
		$dir = $this->dir;
		$d = dir($dir);
		$x = 0;
		while($entry = $d->read()) {
			if($entry != "." && $entry != ".." && (preg_match("/([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $entry) == false) && is_dir($dir.$entry) === false) {
				unlink($dir.$entry);
				$x++;
			}
		}
		$d->close();
		
		if($x > 0) {
			$return = true;
		}
		
		return $return;
    }
}
