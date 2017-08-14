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
    
    public function queryServerLog($player , $pos1 , $pos2 , $time) {
        $X1 = floor($pos1->x);
        $Y1 = floor($pos1->y);
        $Z1 = floor($pos1->z);
        $X2 = floor($pos2->x);
        $Y2 = floor($pos2->y);
        $Z2 = floor($pos2->z);
        if($X1 < $X2 && $Y1 < $Y2 && $Z1 < $Z2) { //95 6 138 96 7
            $query = $this->getDatabase()->prepare("SELECT id,date,time,player,x,y,z,event,block FROM ServerLog WHERE x>='$X1' AND x<='$X2' AND y>='$Y1' AND y<='$X2' AND z>='$Z1' AND z<='$Z2' ");
            $result = $query->execute();
            $data = $this->fetchall($result);
            var_dump($data);
            foreach($data as $i => $value) {
                $player->sendMessage(C::YELLOW . wts::WTS . C::AQUA . "[" . $value["date"] . "] " . $value["time"] . C::GOLD . " '" . $value["player"] . "' " . C::RED . $value["event"] . " at" . C::GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"]);
            }
        }
    }
    
}
