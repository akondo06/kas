<?php

class Session {
    public function __construct() {
        session_start();
    }

    public function __destruct() {
        unset($this);
    }

    public function register($time = 60) {
        $_SESSION['session_id'] = session_id();
        $_SESSION['session_time'] = intval($time);
        $_SESSION['session_start'] = $this->new_time();
    }

    public function is_registered() {
        if(!empty($_SESSION['session_id'])) {
            return true;
        } else {
            return false;
        }
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }



    public function get($key) {
        if(array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return null;
    }
    
    public function session() {
        return $_SESSION;
    }

    public function session_id() {
        return $_SESSION['session_id'];
    }

    public function is_expired() {
        if($_SESSION['session_start'] < $this->time_now()) {
            return true;
        } else {
            return false;
        }
    }

    public function renew() {
        $_SESSION['session_start'] = $this->new_time();
    }

    private function time_now() {
        $currentHour = date('H');
        $currentMin = date('i');
        $currentSec = date('s');
        $currentMon = date('m');
        $currentDay = date('d');
        $currentYear = date('y');
        return mktime($currentHour, $currentMin, $currentSec, $currentMon, $currentDay, $currentYear);
    }

    private function new_time() {
        $currentHour = date('H');
        $currentMin = date('i');
        $currentSec = date('s');
        $currentMon = date('m');
        $currentDay = date('d');
        $currentYear = date('y');
        return mktime($currentHour, ($currentMin + $_SESSION['session_time']), $currentSec, $currentMon, $currentDay, $currentYear);
    }

    public function end() {
        session_destroy();
        $_SESSION = array();
    }
}