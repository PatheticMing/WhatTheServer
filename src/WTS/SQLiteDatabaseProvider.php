<?php

namespace WTS;

use WTS\wts;
use WTS\eventmanager;

class SQLiteDataProvider {
    
    private $wts , $database;
            
    public function __construct(wts $plugin) {
        $this->wts = $plugin;
        if(!file_exists($this->wts->getDataFolder() . "ServerLog.db")) {
            $this->database = new \SQLite3($this->wts->getDataFolder() . "ServerLog.db");
            $this->database->exec("CREATE TABLE IF NOT EXISTS ServerLog
                                            (id INTEGER PRIMARY KEY AUTOINCREMENT , date INTEGER , time INTEGER , player TEXT , 
                                            level TEXT , x INTEGER , y INTEGER , z INTEGER , event TEXT , block TEXT , item_transfer TEXT , 
                                            join_date INTEGER , last_join INETEGER , last_online INTEGER);");
            $this->wts->database = $this->database;
        } else{
            $this->database = new \SQLite3($this->wts->getDataFolder() . "ServerLog.db");
            $this->wts->database = $this->database;
        }
    }
    
}
