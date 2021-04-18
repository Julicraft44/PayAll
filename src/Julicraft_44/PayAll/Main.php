<?php
namespace Julicraft_44\PayAll;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {
    
    public function onEnable() {
        $this->saveDefaultConfig();
        $this->getLogger()->info("PayAll loaded");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        switch($cmd->getName()) {
            case "payall":  
                if($sender instanceof Player) {
                    if($sender->hasPermission("payall.use")) {
                        
                        if(!isset($args[0])) {
                            $sender->sendMessage("§cNutze:§8 /payall §8<Anzahl>");
                            return false;
                        }
                        
                        if($args[0]) {
                            if(is_numeric($args[0])) {
                                $op = count($this->getServer()->getOnlinePlayers()); 
                                $tp = ($args[0] * $op - 1);
                                $name = $sender->getName();
                                if($tp <= EconomyAPI::getInstance()->myMoney($sender)) {
                                    foreach ($this->getServer()->getOnlinePlayers() as $p) {
                                        $pn = $p->getName();
                                        EconomyAPI::getInstance()->addMoney($pn, $args[0]);
                                        EconomyAPI::getInstance()->reduceMoney($sender, $args[0]);
                                    }
                                    $msg = $this->getConfig()->get("you-got-money");
                                    $msg = str_replace("%sender", "$name", $msg);
                                    $msg = str_replace("%amount", "$args[0]", $msg);
                                    $this->getServer()->broadcastMessage($msg);
                                    
                                    $msg = $this->getConfig()->get("you-send-money");
                                    $msg = str_replace("%amount", "$args[0]", $msg);
                                    $sender->sendMessage($msg);
                                } else {
                                    $sender->sendMessage($this->getConfig()->get("no-money"));
                                }
                            } else {
                                $sender->sendMessage($this->getConfig()->get("numberic"));
                            }
                        }
                    } else {
                        $sender->sendMessage($this->getConfig()->get("no-perm"));
                    }
                } else {
                   $sender->sendMessage("You need to run this command in-game");
                }
                
        }
        return true;
        
    }

}
