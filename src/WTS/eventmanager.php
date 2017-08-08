<?php

namespace WTS;

use WTS\wts;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\TextFormat as C;

class eventmanager extends PluginBase implements Listener {
    
    private $wts;

    public function __construct(wts $plugin) {
        $this->wts = $plugin;
    }
    
}
