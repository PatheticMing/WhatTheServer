<?php

namespace WTS\Command;

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
    
    public function execute(CommandSender $sender, string $commandLabel, string $args) {
        if(empty($args)) {
            return false;
        }
        $time = $args[6];
        $pos1 = new Vector3($args[0] , $args[1] , $args[2]);
        $pos2 = new Vector3($args[3] , $args[4] , $args[5]);
        $this->getPlugin()->queryServerLog($sender , $pos1 , $pos2 , $time);
    }

}
