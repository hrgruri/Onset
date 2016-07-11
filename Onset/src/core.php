<?php
require_once(dirname(__FILE__).'/config.php');


class Onset{
    static function isValidAccess($randKey) {
        if($randKey != $_SESSION['onset_rand']) {
            return false;
        }
        return true;
    }
    
    static function getRoomlist() {
        global $config;
        $dir = $config['roomSavepath'];
        return unserialize(file_get_contents($dir.'roomlist'));
    }
    
    static function setRoomlist($roomlist){
        global $config;
        $dir = $config['roomSavepath'];
        $ret = file_put_contents($dir.'roomlist', serialize($roomlist), LOCK_EX);
        return $ret !== FALSE;
    }
    
    static function okJson($data){
        $json = [
            "status" => 1,
            "data" => $data
        ];
        return json_encode($json);
    }
    
    
    static function errorJson($message){
        $json = [
            "status" => -1,
            "message" => $message
        ];
        return json_encode($json);
    }
    
    static function diceroll($text, $sys){
        global $config;
        $url = $config['bcdiceURL'];
        
        $encordedText = urlencode($text);
        $encordedSys  = urlencode($sys);
        
        $s = "";
        if($config["enableSSL"]){$s = 's';}
        $ret = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");
        if(trim($ret) == '1' || trim($ret) == 'error'){
            $ret = "";
        }
        return str_replace('onset: ', '', $ret);
    }
    
    static function checkBcdice(){
        global $config;
        $url = $config['bcdiceURL'];
        $s = $config['enableSSL'] ? 's' : '';
        file_get_contents("http{$s}://{$url}?list=1");
        return strpos($http_response_header[0], '200') !== FALSE;
    }

    static function getSystemList(){
        global $config;
        $url = $config['bcdiceURL'];
        $s = '';
        if($config['enableSSL']){$s = 's';}
        return split("\n", file_get_contents("http{$s}://{$url}?list=1"));
    }

    static function checkPermition(){
        global $config;
        $dir = $config['roomSavepath'];
        return is_writable($dir) && is_readable($dir);
    }
    
}
