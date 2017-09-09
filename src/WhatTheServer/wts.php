<?php

namespace WhatTheServer;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class wts extends PluginBase {
    
    public $database;
    
    private $data;
    private $player;

    const WTS = "[WhatTheServer] ";

    public function onEnable() {
        if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}
        $this->getServer()->getPluginManager()->registerEvents(new eventmanager($this), $this);
        if(!is_dir($this->getDataFolder())) {
                @mkdir($this->getDataFolder());
            }
        $this->datebase = new SQLiteDataProvider($this);
        
        $querycommand = new \WhatTheServer\commands\querycommand($this);
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
        $minZ = min($pos1[2] , $pos2[2]);
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

    public function queryPlayer($sender , $name) {
        $players = $this->getServer()->getOnlinePlayers();
        foreach($players as $player) {
            if($player->getName() == $name) {
                $this->player = $player->getName();
                break;
            }
            $this->player = null;
        }
        var_dump($this->player);
        var_dump($this->getServer()->getOfflinePlayer($name));
        if($this->player != null) {
            $name = strtolower($name);
            $query = $this->getDatabase()->prepare("SELECT join_date,last_join,last_online FROM ServerLog WHERE player='$name' ");
            $result = $query->execute();
            $data = $this->fetchall($result);
            if($data != null) {
                foreach($data as $i => $value) {
                    $sender->sendMessage(C::YELLOW . wts::WTS . "---------------" . C::GREEN . "Player : '$name' (Online) \n" . C::AQUA . 
                    "Joined : " . $value["join_date"] . "\n" . 
                    "Last seem : " . $value["last_join"] . " " . $value["last_online"]);
                }
            }
        } elseif($this->getServer()->getOfflinePlayer($name) != null) {
            $name = strtolower($name);
            $query = $this->getDatabase()->prepare("SELECT join_date,last_join,last_online FROM ServerLog WHERE player='$name' ");
            $result = $query->execute();
            $data = $this->fetchall($result);
            if($data != null) {
                foreach($data as $i => $value) {
                    $sender->sendMessage(C::YELLOW . wts::WTS . "---------------" . C::RED . "Player : '$name' (Offline) \n" . C::AQUA . 
                    "Joined : " . $value["join_date"] . "\n" . 
                    "Last seem : " . $value["last_join"] . " " . $value["last_online"]);
                }
            } else {
                $sender->sendMessage(C::YELLOW . wts::WTS . C::RED . "Cannot find any data!");
            }
        }
    }
    
}
