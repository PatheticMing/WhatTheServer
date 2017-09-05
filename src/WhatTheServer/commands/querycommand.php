<?php

namespace WhatTheServer\commands;

use WhatTheServer\wts;

use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

class querycommand extends Command implements PluginIdentifiableCommand {
    
    public $command = "wts";
    
    private $wts;
    
    public function __construct(wts $plugin) {
        parent::__construct($this->command, "Query what the server!");
        $this->setUsage("Usage: /wts query <x> <y> <z> <x2> <y2> <z2> <time> | player <username>");
        $this->wts = $plugin;
    }
    
    public function getPlugin(): \pocketmine\plugin\Plugin {
        return $this->wts;
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if(isset($args[0])) {
            switch ($args[0]) {
                case "query" :
                    if($sender->hasPermission("yolo.command.query")) {
                        if(isset($args[1]) && isset($args[2]) && isset($args[3]) && isset($args[4]) && isset($args[5]) && isset($args[6]) && isset($args[7])) {
                            if(is_numeric($args[1]) && is_numeric($args[2]) && is_numeric($args[3]) && is_numeric($args[4]) && is_numeric($args[5]) && is_numeric($args[6]) && is_numeric($args[7])) {
                                $pos1 = array($args[1] , $args[2] , $args[3]);
                                $pos2 = array($args[4] , $args[5] , $args[6]);
                                $time = $args[7];
                                $this->getPlugin()->queryServerLog($sender , $pos1 , $pos2 , $time);
                            } else {
                                $sender->sendMessage($this->getUsage());
                                }
                        } else {
                            $sender->sendMessage($this->getUsage());
                        }
                    } else {
                        $sender->sendMessage(C::RED . "Sorry but you don't have permission to do that!");
                    }
                    break;
                case "player" :
                    if(isset($args[1])) {
                        if($sender->hasPermission("yolo.command.query")) {
                            if(isset($args[1])) {
                                $this->getPlugin()->queryPlayer($sender , $args[1]);
                            }
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                    break;
                default:
                    if($sender->hasPermission("yolo.command.query")) {
                        $sender->sendMessage($this->getUsage());
                    } else {
                        $sender->sendMessage(C::RED . "Sorry but you don't have permission!");
                    }
                    break;
            }
        } else {
            $sender->sendMessage($this->getUsage());
        }
    }

}
