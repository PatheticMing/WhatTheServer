<?php

namespace xdming\WhatTheServer\commands;

use xdming\WhatTheServer\wts;

use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

class querycommand extends Command implements PluginIdentifiableCommand {

    public $command = "wts";
	public $pos1 = [];
	public $pos2 = [];
	public $flag;

    private $wts, $confirm;

    public function __construct(wts $plugin) {
        parent::__construct($this->command, "Query what the server!");
        $this->setUsage("Usage: /wts query <x> <y> <z> <x2> <y2> <z2> | player <username> | reset");
        $this->wts = $plugin;
		$this->confirm = $this->flag = false;
    }

    public function getPlugin(): \pocketmine\plugin\Plugin {
        return $this->wts;
    }

    //TODO <time>
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if($sender->hasPermission("yolo.command.query")) {
    			if(isset($args[0])) {
    				switch ($args[0]) {
    					case "query" :
    							if(isset($args[1]) && isset($args[2]) && isset($args[3]) && isset($args[4]) && isset($args[5]) && isset($args[6]) /*&& isset($args[7])*/) {
    								if(is_numeric($args[1]) && is_numeric($args[2]) && is_numeric($args[3]) && is_numeric($args[4]) && is_numeric($args[5]) && is_numeric($args[6]) /*&& is_numeric($args[7])*/) {
    									 $this->pos1 = array($args[1] , $args[2] , $args[3]);
    									 $this->pos2 = array($args[4] , $args[5] , $args[6]);
    									 //$time = $args[7];
    									 $this->getPlugin()->queryServerLog($sender, $this->pos1, $this->pos2, false);
    								} else {
    									 $sender->sendMessage($this->getUsage());
    									 }
    							} elseif(!isset($args[1])) {
    								if($sender instanceof Player) {
    									if($this->getPlugin()->taptoquery) {
                          $this->getPlugin()->taptoquery = false;
                          $sender->sendMessage(wts::WTS. "Turned off transaction query mode");
                      } else {
                         $this->getPlugin()->taptoquery = true;
                         $sender->sendMessage(wts::WTS. "Transaction query mode on! Please tap on an inventory block to query :D");
                         }
    								} else {
    										$sender->sendMessage("Console doesn't tap on a block");
    									}
    							} else {
    								$sender->sendMessage($this->getUsage());
    							}
    						break;
    					case "pos1" :
    						if($sender instanceof Player) {
    							$this->pos1 = [intval($sender->getX()), intval($sender->getY()), intval($sender->getZ())];
    							$this->flag = true;
    							$sender->sendMessage(wts::WTS. "Position one set");
    						} else {
    							 $sender->sendMessage("The console should not run this command.");
    						   }
    						break;
    					case "pos2" :
    						if($sender instanceof Player) {
    							if($this->flag) {
    								$this->flag = false;
    								$this->pos2 = [intval($sender->getX()), intval($sender->getY()), intval($sender->getZ())];
    								$sender->sendMessage(wts::WTS. "Position two set");
    								$this->getPlugin()->queryServerLog($sender, $this->pos1, $this->pos2, false);
    							} else {
						         $sender->sendMessage(wts::WTS. "Please go set the position one first");
				            }
    						} else {
    							$sender->sendMessage("The console should not run this command.");
    						}
    						break;
    					case "player" :
    						if(isset($args[1])) {
    							$this->getPlugin()->queryPlayer($sender , $args[1]);
    						} else {
    							$sender->sendMessage($this->getUsage());;
    						}
    						break;
    					case "reset":
    						if(!$sender instanceof Player) {
    							if($this->confirm){
    								$this->getPlugin()->resetDatabase();
    								$this->confirm = false;
    							} else {
    								$sender->sendMessage(C::AQUA . wts::WTS . C::YELLOW . "Are you sure to do that? Please enter the command again to confirm.");
    								$this->confirm = true;
    							}
    						} else {
    							$sender->sendMessage(C::AQUA . wts::WTS . C::RED . "Sorry but you can only reset the database from the console. ");
    						}
    						break;
    					default:
    							$this->confirm = false;
    							$sender->sendMessage($this->getUsage());
    							break;
    				}
    			} else {
    				$sender->sendMessage($this->getUsage());
    			}
  		} else {
  			$sender->sendMessage(C::RED . "Sorry but you don't have the permission to do that!");
  		}
    }

}
