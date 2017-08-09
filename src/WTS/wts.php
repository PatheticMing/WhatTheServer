<?php

namespace WTS;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class wts extends PluginBase {
    
    public $database;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new eventmanager($this), $this);
        $this->datebase = new SQLiteDataProvider($this);
        
        $this->getLogger()->notice("This plugin is on BETA!Using SQLite3 data provider!");
        $this->getLogger()->info(C::GOLD . "Loaded!");
    }
    
    public function fetchall($result){
        $row = array();
        $object = 0;
        while($r = $result->fetchArray(SQLITE3_ASSOC)){
            $row[$object] = $r;
            $object++;
        }
        return $row;
    }

    
    public function getDatabase() {
        return $this->database;
    }

    public function getTime() {
        return date("H:i:s");
    }
    
    public function getDate() {
        return date("Y-m-d");
    }
    
}
