<?php

namespace WTS;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;
use wts\Command\querycommand;

class wts extends PluginBase {
    
    public $database;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new eventmanager($this), $this);
        $this->datebase = new SQLite3DatabaseProvider($this);
        
        $querycommand = new querycommand($this);
        $this->getServer()->getCommandMap()->register($querycommand->command, $querycommand);
        
        $this->getLogger()->notice("This plugin is on BETA!Using SQLite3 data provider!");
        $this->getLogger()->info(C::GOLD . "Loaded!");
    }
    
    public function fetchall($result){
        $row = array();
        $i = 0;
        while($res = $result->fetchArray(SQLITE3_ASSOC)){
            $row[$i] = $res;
            $i++;
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
    
    public function queryServerLog($player , $pos1 , $pos2 , $date , $time) {
        
    }
    
}
