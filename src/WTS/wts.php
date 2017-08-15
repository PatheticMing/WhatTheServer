<?php

namespace WTS;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class wts extends PluginBase {
    
    public $database;
    
    private $data;

    const WTS = "[WhatTheServer] ";

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new eventmanager($this), $this);
        $this->datebase = new SQLiteDataProvider($this);
        
        $querycommand = new \WTS\commands\querycommand($this);
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
    
    public function queryServerLog($player , array $pos1 , array $pos2 , $time) {
        $maxX = max($pos1[0] , $pos2[0]);
        $minX = min($pos1[0] , $pos2[0]);
        $maxY = max($pos1[1] , $pos2[1]);
        $minY = min($pos1[1] , $pos2[1]);
        $maxZ = max($pos1[2] , $pos2[2]);
        $minZ = min($pos1[3] , $pos2[3]);
        $query = $this->getDatabase()->prepare("SELECT id,date,time,player,x,y,z,event,block,blockid FROM ServerLog WHERE x BETWEEN '$minX' AND '$maxX' AND y BETWEEN '$minY' AND '$maxY' AND z BETWEEN '$minZ' AND '$maxZ' ");
        $result = $query->execute();
        $data = $this->fetchall($result);
        if($data != null) {
            foreach($data as $i => $value) {
                $player->sendMessage(C::YELLOW . wts::WTS . C::AQUA . "[" . $value["date"] . "] " . $value["time"] . C::GOLD . " '" . $value["player"] . "' " . C::RESET . $value["event"] . " " . $value["block"] . "(" . $value["blockid"] . ")" . " at" . C::GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"]);
            }
        } else {
            $player->sendMessage(C::YELLOW . wts::WTS . C::RED . "Cannot find any data!");
        }
    }
    
}
