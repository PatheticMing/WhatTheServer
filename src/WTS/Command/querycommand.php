<?php

namespace WTS\commands;

use WTS\wts;

use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

class querycommand extends Command implements PluginIdentifiableCommand {
    
    public $command = "query";
    
    private $wts;
    
    public function __construct(wts $plugin) {
        parent::__construct($this->command, "Query what the server!");
        $this->setUsage("/$this->command <x> <y> <z> <x2> <y2> <z2>  <time>");
        $this->wts = $plugin;
    }
    
    public function getPlugin() {
        return $this->wts;
    }
    
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if(isset($args[0]) && isset($args[1]) && isset($args[2]) && isset($args[3]) && isset($args[4]) && isset($args[5]) && isset($args[6])) {
            if(is_numeric($args[0]) && is_numeric($args[1]) && is_numeric($args[2]) && is_numeric($args[3]) && is_numeric($args[4]) && is_numeric($args[5]) && is_numeric($args[6])) {
                $pos1 = array($args[0] , $args[1] , $args[2]);
                $pos2 = array($args[3] , $args[4] , $args[5]);
                $time = $args[6];
                $this->getPlugin()->queryServerLog($sender , $pos1 , $pos2 , $time);
            } else {
                $sender->sendMessage($this->getUsage());
                }
        } else {
            $sender->sendMessage($this->getUsage());
        }
    }

}
