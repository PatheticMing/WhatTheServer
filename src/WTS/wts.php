<?php

namespace WTS;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;

class wts extends PluginBase {
    
    
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new eventmanager($this), $this);
        $this->getServer()->getLogger()->notice("This plugin is on BETA!Using YAML data provider!");
        $this->getServer()->getLogger()->info(C::GOLD . "Loaded!");
    }
    
}
