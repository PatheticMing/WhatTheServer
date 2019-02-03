<?php

namespace WhatTheServer;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\WrittenBook;
use pocketmine\utils\TextFormat as C;

class wts extends PluginBase {
    
    public $database;
    
    private $data;
    private $player;

    const WTS = "[WhatTheServer] ";

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new eventmanager($this), $this);
        if(!is_dir($this->getDataFolder())) {
                @mkdir($this->getDataFolder());
            }
        $this->datebase = new SQLiteDataProvider($this);
        
        $querycommand = new \WhatTheServer\commands\querycommand($this);
        $this->getServer()->getCommandMap()->register($querycommand->command, $querycommand);
        
        $this->getLogger()->notice("This plugin is in BETA! Using SQLite3 as the data provider!");
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
    
    public function queryServerLog($sender , array $pos1 , array $pos2 /*, $time*/) {
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
			if($sender instanceof Player) {
				$book = Item::get(Item::WRITTEN_BOOK, 0, 1);
				$book->setTitle(C::YELLOW . C::UNDERLINE . 
								"[pos 1: " . C::GREEN . $pos1[0] . C::YELLOW . ", " . C::GREEN . $pos1[1] . C::YELLOW . ", " . C::GREEN . $pos1[2] . C::YELLOW . "; " .
								"pos 2: " . C::GREEN . $pos2[0] . C::YELLOW . ", " . C::GREEN . $pos2[1] . C::YELLOW . ", " . C::GREEN . $pos2[2] . C::YELLOW . "]");
				$book->setAuthor(C::LIGHT_PURPLE . wts::WTS);
				$temp = $text = [];
				$j = $page = 0;
				$totalcount = count($data);
				$mod = fmod($totalcount, 2);
				foreach($data as $i => $value) {
					if(($page > 50) && $totalcount > 0) {
						$sender->getInventory()->addItem($book);
						$page = 0;
					} else {
						if(($j == 1)) {
							$text[$page] = $temp[$i-1] . 
											"(" . $i . ")\n" . C::BLUE . "[" . $value["date"] . "] " . $value["time"] . C::DARK_GREEN . " '" . $value["player"] . "' " . 
											C::RED . $value["event"] . " " . C::DARK_GRAY . $value["block"] . "(" . $value["blockid"] . ")" . " at\n" . 
											C::DARK_GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"] . "\n" . C::RESET;
							$totalcount--;
							$j = -1;
							
							$book->setPageText($page, $text[$page]);										
							$page++;
						} elseif(($totalcount < 2) && ($mod != 0)) {
							$text[$page] = "(" . $i . ")\n" . C::BLUE . "[" . $value["date"] . "] " . $value["time"] . C::DARK_GREEN . " '" . $value["player"] . "' " . 
											C::RED . $value["event"] . " " . C::DARK_GRAY . $value["block"] . "(" . $value["blockid"] . ")" . " at\n" . 
											C::DARK_GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"] . "\n" . C::RESET;
							$book->setPageText($page, $text[$page]);
						} else {
							$temp[$i] = "(" . $i . ")\n" .  C::BLUE . "[" . $value["date"] . "] " . $value["time"] . C::DARK_GREEN . " '" . $value["player"] . "' " . 
										C::RED . $value["event"] . " " . C::DARK_GRAY . $value["block"] . "(" . $value["blockid"] . ")" . " at\n" . 
										C::DARK_GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"] . "\n" . C::RESET;
										$totalcount--;
						}
					}
					$j++;
				}
				
				$sender->getInventory()->addItem($book);
				$sender->sendMessage(C::YELLOW . wts::WTS . C::AQUA . "Query has been done. " . C::RED . count($data) . C::AQUA . " records found!");
				
			} else {
				$sender->sendMessage(C::RED . "------------------------------------------------------------------------------------------------------------");
				foreach($data as $value) {
					$sender->sendMessage(C::YELLOW . wts::WTS . C::AQUA . "[" . $value["date"] . "] " . $value["time"] . C::GOLD . " '" . $value["player"] . "' " . 
										C::RESET . $value["event"] . " " . $value["block"] . "(" . $value["blockid"] . ")" . " at" . 
										C::GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"]);
				}
			}
		} else {
			$sender->sendMessage(C::YELLOW . wts::WTS . C::RED . "Cannot find any data!");
		}
    }

    public function queryPlayer($sender , $name) {
        $players = $this->getServer()->getOnlinePlayers();
		$this->player = null;
        foreach($players as $player) {
            if(strtolower($player->getName()) == strtolower($name)) {
                $this->player = $player->getName();
                break;
            }
            $this->player = null;
        }
        if($this->player != null) {
            $name = strtolower($name);
            $query = $this->getDatabase()->prepare("SELECT join_date,last_join,last_online FROM ServerLog WHERE player='$name' ");
            $result = $query->execute();
            $data = $this->fetchall($result);
            if($data != null) {
                    $sender->sendMessage(C::YELLOW . wts::WTS . "---------------\n" . C::GREEN . "Player: '$name' (Online) \n" . C::AQUA . 
                    "Joined: " . $data[0]["join_date"] . "\n" . 
                    "Last seen: " . $data[0]["last_join"] . " " . $data[0]["last_online"]);          
            }
        } elseif($this->getServer()->getOfflinePlayer($name) != null) {
            $name = strtolower($name);
            $query = $this->getDatabase()->prepare("SELECT join_date,last_join,last_online FROM ServerLog WHERE player='$name' ");
            $result = $query->execute();
            $data = $this->fetchall($result);
            if($data != null) {				
                    $sender->sendMessage(C::YELLOW . wts::WTS . "---------------\n" . C::RED . "Player: '$name' (Offline) \n" . C::AQUA . 
                    "Joined: " . $data[0]["join_date"] . "\n" . 
                    "Last seen: " . $data[0]["last_join"] . " " . $data[0]["last_online"]);            
            } else {
                $sender->sendMessage(C::YELLOW . wts::WTS . C::RED . "Cannot find any data!");
            }
        }
    }
    
}
