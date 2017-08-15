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
        if(empty($args[0]) || empty($args[1]) || empty($args[2]) || empty($args[3]) || empty($args[4]) || empty($args[5]) || empty($args[6])) {
            $sender->sendMessage($this->getUsage());
        } else {
            $pos1 = array($args[0] , $args[1] , $args[2]);
            $pos2 = array($args[3] , $args[4] , $args[5]);
            $time = $args[6];
            $this->getPlugin()->queryServerLog($sender , $pos1 , $pos2 , $time);
        }
    }

}
