<?php

namespace WhatTheServer;

use WhatTheServer\wts;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\utils\TextFormat as C;

class eventmanager extends PluginBase implements Listener {
    
    private $wts , $datebase;

    public function __construct(wts $plugin) {
        $this->wts = $plugin;
        
    }
    
    public function onJoin(PlayerJoinEvent $event) {
        $name = $event->getPlayer()->getName();
        $time = $this->wts->getTime();
        $date = $this->wts->getDate();
        $query = $this->wts->getDatabase()->prepare("SELECT player FROM ServerLog WHERE player='$name' ");
        $result = $query->execute();
        var_dump($result);
        $data = $this->wts->fetchall($result);
        if($data == NULL) {
        $this->wts->getDatabase()->exec("INSERT INTO ServerLog (date , time , player , join_date , last_join) VALUES ('$date' , '$time' , '$name' , '$date' , '$date')");
        $this->wts->getLogger()->notice("Player '$name' not found!Data registered in datebase!");
        } else{
            $this->wts->database->exec("UPDATE ServerLog SET last_join='$time' WHERE player='$name' ");
        }
    }
    
    public function onQuit(PlayerQuitEvent $event) {
        $name = $event->getPlayer()->getName();
        $lastonline = date("Y-m-d H:i:s");
        $this->wts->database->exec("UPDATE ServerLog SET last_online='$lastonline' WHERE player='$name' ");
    }
    
    public function onBreakBlock(BlockBreakEvent $event) {
        $name = $event->getPlayer()->getName();
        $date = $this->wts->getDate();
        $time = $this->wts->getTime();
        $block = $event->getBlock();
        $blockId = $block->getId();
        $blockname = $block->getName();
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();
        $level = $event->getPlayer()->getLevel()->getName();
        $this->wts->database->exec("INSERT INTO ServerLog (date , time , player , level , x , y , z , event , block) VALUES ('$date' , '$time' , '$name' , '$level' , '$x' , '$y' , '$z' , 'break block' , '$blockname')");
    }
    
    public function onPlaceBlock(BlockPlaceEvent $event) {
        $name = $event->getPlayer()->getName();
        $date = $this->wts->getDate();
        $time = $this->wts->getTime();
        $block = $event->getBlock();
        $blockId = $block->getId();
        $blockname = $block->getName();
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();
        $level = $event->getPlayer()->getLevel()->getName();
        $this->wts->database->exec("INSERT INTO ServerLog (date , time , player , level , x , y , z , event , block) VALUES ('$date' , '$time' , '$name' , '$level' , '$x' , '$y' , '$z' , 'place block' , '$blockname')");
    }
    
}
