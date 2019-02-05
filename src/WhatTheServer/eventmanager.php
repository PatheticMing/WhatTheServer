<?php

namespace WhatTheServer;

use WhatTheServer\wts;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\item\Air;
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
        $name = strtolower($event->getPlayer()->getName());
        $time = $this->wts->getTime();
        $date = $this->wts->getDate();
        $query = $this->wts->getDatabase()->prepare("SELECT player FROM ServerLog WHERE player='$name' ");
        $result = $query->execute();
        $data = $this->wts->fetchall($result);
        if($data == NULL) {
            $this->wts->getDatabase()->exec("INSERT INTO ServerLog (date , time , player , join_date , last_join) VALUES ('$date' , '$time' , '$name' , '$date' , '$date')");
            $this->wts->getLogger()->notice("Player '$name' not found!Player is registered in datebase!");
        }
    }
    
    public function onQuit(PlayerQuitEvent $event) {
        $name = strtolower($event->getPlayer()->getName());
        $lastjoin = $this->wts->getDate();
        $lastonline = $this->wts->getTime();
        $this->wts->database->exec("UPDATE ServerLog SET last_join='$lastjoin' , last_online='$lastonline' WHERE player='$name' ");
    }
    
    public function onBreakBlock(BlockBreakEvent $event) {
        $name = strtolower($event->getPlayer()->getName());
        $date = $this->wts->getDate();
        $time = $this->wts->getTime();
        $block = $event->getBlock();
        $blockId = $block->getId();
        $blockname = $block->getName();
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();
        $level = $event->getPlayer()->getLevel()->getName();
        $this->wts->database->exec("INSERT INTO ServerLog (date , time , player , level , x , y , z , event , block , blockid) VALUES ('$date' , '$time' , '$name' , '$level' , '$x' , '$y' , '$z' , 'break block' , '$blockname' , '$blockId')");
    }
    
    public function onPlaceBlock(BlockPlaceEvent $event) {
        $name = strtolower($event->getPlayer()->getName());
        $date = $this->wts->getDate();
        $time = $this->wts->getTime();
        $block = $event->getBlock();
        $blockId = $block->getId();
        $blockname = $block->getName();
        $x = $block->getFloorX();
        $y = $block->getFloorY();
        $z = $block->getFloorZ();
        $level = $event->getPlayer()->getLevel()->getName();
        $this->wts->database->exec("INSERT INTO ServerLog (date , time , player , level , x , y , z , event , block , blockid) VALUES ('$date' , '$time' , '$name' , '$level' , '$x' , '$y' , '$z' , 'place block' , '$blockname' , '$blockId')");
    }

    /*public function onTransaction(InventoryTransactionEvent $event) {
        $player = $event->getTransaction()->getSource();
        $viewer = null;
        $playerinv = null;
        $blockinv = null;
        foreach($event->getTransaction()->getInventories() as $inventory) {
            if($inventory->getHolder() instanceof Player) {
                $playerinv = $inventory->getHolder();
            }
            if($inventory->getHolder() instanceof Chest) {
                $blockinv = $inventory->getHolder();
                //$viewer = $inventory->getViewers();
            }
        }
        if(isset($playerinv) && isset($blockinv)) {
            $name = strtolower($player->getName());
            $date = $this->wts->getDate();
            $time = $this->wts->getTime();
            $trActions = $event->getTransaction()->getActions();
            foreach($trActions as $action) {
                if($action->getSourceItem()->getId() == 0) {
                    continue;
                } else {
                    $item = $action->getSourceItem()->getName();
                    //$this->wts->database->exec("INSERT INTO ServerLog (date , time , player , level , x , y , z , event , block , blockid) VALUES ('$date' , '$time' , '$name' , '$level' , '$x' , '$y' , '$z' , 'place block' , '$blockname' , '$blockId')");
                }              
            }
        }
    }*/
    
}
