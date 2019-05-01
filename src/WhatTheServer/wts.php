<?php

namespace WhatTheServer;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\WrittenBook;
use pocketmine\utils\TextFormat as C;

class wts extends PluginBase {
    
    public $database, $taptoquery;
    
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
		
		$this->taptoquery = false;
        
        $this->getLogger()->notice("This plugin is in BETA! Using SQLite3 as the data provider!");
        $this->getLogger()->info(C::GOLD . "Loaded!");
    }
    
	public function resetDatabase() {
		$query = $this->database->prepare("DELETE FROM ServerLog");
		$result = $query->execute();
		
		$query = $this->database->prepare("DELETE FROM sqlite_sequence WHERE name='ServerLog'");
		$result = $query->execute();
		
		$query = $this->database->prepare("SELECT * FROM ServerLog");
		$result = $query->execute();
		$data = $this->fetchall($result);
		if($data == NULL) {
			$this->getServer()->getLogger()->notice("Database has been clear!");
		} else {
			$$this->getServer()->getLogger()->notice("Failed to clear the database!");
		}
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
	
	public function generatePMessage(array $value, $i) {
		if(isset($value["item_transfered"])) {
			return "(" . $i . ")\n" .  C::BLUE . "[" . $value["date"] . "] " . $value["time"] . C::DARK_GREEN . " '" . $value["player"] . "' " . 
						C::RED . $value["event"] . " " . C::DARK_GRAY . $value["item_transfered"] . "(" . $value["objectid"] . ")" . " at\n" . 
						C::DARK_GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"] . "\n" . C::RESET;
		} else {
			return "(" . $i . ")\n" .  
						C::BLUE . "[" . $value["date"] . "] " . $value["time"] . C::DARK_GREEN . " '" . $value["player"] . "' " . 
						C::RED . $value["event"] . " " . C::DARK_GRAY . $value["block"] . "(" . $value["objectid"] . ")" . " at\n" . 
						C::DARK_GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"] . "\n" . C::RESET;
		}
	}
    
	public function generateCMessage(array $value) {
		if(isset($value["item_transfered"])) {
			return C::YELLOW . wts::WTS . C::AQUA . "[" . $value["date"] . "] " . $value["time"] . C::GOLD . " '" . $value["player"] . "' " . 
					C::RESET . $value["event"] . " " . $value["item_transfered"] . "(" . $value["objectid"] . ")" . " at" . 
					C::GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"];
		} else {
			return C::YELLOW . wts::WTS . C::AQUA . "[" . $value["date"] . "] " . $value["time"] . C::GOLD . " '" . $value["player"] . "' " . 
					C::RESET . $value["event"] . " " . $value["block"] . "(" . $value["objectid"] . ")" . " at" . 
					C::GREEN . " x= " . $value["x"] . " y= " . $value["y"] . " z= " . $value["z"];
		}
	}
	
    public function queryServerLog($sender , array $pos1 , array $pos2, bool $invonly) {
        $maxX = max($pos1[0] , $pos2[0]);
        $minX = min($pos1[0] , $pos2[0]);
        $maxY = max($pos1[1] , $pos2[1]);
        $minY = min($pos1[1] , $pos2[1]);
        $maxZ = max($pos1[2] , $pos2[2]);
        $minZ = min($pos1[2] , $pos2[2]);
		if($invonly) {
			$query = $this->getDatabase()->prepare("SELECT id,date,time,player,x,y,z,event,objectid,item_transfered FROM ServerLog WHERE item_transfered <> 'NULL' AND x BETWEEN '$minX' AND '$maxX' AND y BETWEEN '$minY' AND '$maxY' AND z BETWEEN '$minZ' AND '$maxZ' ORDER BY id DESC");	
		} else {
			$query = $this->getDatabase()->prepare("SELECT date,time,player,x,y,z,event,block,objectid,item_transfered FROM ServerLog WHERE x BETWEEN '$minX' AND '$maxX' AND y BETWEEN '$minY' AND '$maxY' AND z BETWEEN '$minZ' AND '$maxZ' ");
			}
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
					if($totalcount != count($data) - 100) {
						if(($j == 1)) {
							$text[$page] = $temp[$i-1] . $this->generatePMessage($value, $i);
							
							$totalcount--;
							$j = -1;
							$book->setPageText($page, $text[$page]);										
							$page++;
						} elseif(($totalcount < 2) && ($mod != 0)) {
							$text[$page] = $this->generatePMessage($value, $i);
							$book->setPageText($page, $text[$page]);
						} else {
							$temp[$i] = $this->generatePMessage($value, $i);
							$totalcount--;
						}
						$j++;
					} else {
						break;
						}
				}
				
				$sender->getInventory()->addItem($book);
				$sender->sendMessage(C::YELLOW . wts::WTS . C::AQUA . "Query has been done. " . C::RED . count($data) . C::AQUA . " records found!");
				
			} else {
				$sender->sendMessage(C::RED . "------------------------------------------------------------------------------------------------------------");
				foreach($data as $value) {
					$sender->sendMessage($this->generateCMessage($value));
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
            $query = $this->getDatabase()->prepare("SELECT * FROM PlayerLog WHERE player='$name' ");
            $result = $query->execute();
            $data = $this->fetchall($result);
            if($data != null) {
                    $sender->sendMessage(C::YELLOW . wts::WTS . "---------------\n" . C::GREEN . "Player: '$name' (Online) " . 
					C::LIGHT_PURPLE . "(" . $data[0]["identity"] . ") \n" . 
					C::AQUA . "Joined: " . $data[0]["join_date"] . "\n" . 
                    C::GOLD . "Last seen: ". $data[0]["last_join"] . " (" . $data[0]["last_online"] . ")");
            } else {
                $sender->sendMessage(C::YELLOW . wts::WTS . C::RED . "Cannot find any data!");
            }
        } elseif($this->getServer()->getOfflinePlayer($name) != null) {
            $name = strtolower($name);
            $query = $this->getDatabase()->prepare("SELECT * FROM PlayerLog WHERE player='$name' ");
            $result = $query->execute();
            $data = $this->fetchall($result);
            if($data != null) {
                    $sender->sendMessage(C::YELLOW . wts::WTS . "---------------\n" . C::RED . "Player: '$name' (Offline) " . 
					C::LIGHT_PURPLE . "(" . $data[0]["identity"] . ") \n" . 
					C::AQUA . "Joined: ". $data[0]["join_date"] . "\n" . 
                    C::GOLD . "Last seen: " . $data[0]["last_join"] . " (" . $data[0]["last_online"] . ")");
            } else {
                $sender->sendMessage(C::YELLOW . wts::WTS . C::RED . "Cannot find any data!");
            }
        } else {
                $sender->sendMessage(C::YELLOW . wts::WTS . C::RED . "Cannot find any data!");
            }
    }
    
}
