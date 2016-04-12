<?php

namespace Blubberboy333\PlayerMute;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBAse implements Listener{
  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->players = [];
    $this->getLogger()->info(TextFormat::GREEN."Done!");
  }
  
  public function checkBlock(Player $player){
    if($player->hasPermission("mute") || $player->hasPermission("mute.block")){
      return true;
    }else{
      return false;
    }
  }
  
  public function checkMute(Player $player){
    if(in_array($player->getName(), $this->players)){
      return true;
    }else{
      return false;
    }
  }
  
  public function onCommand(CommandSender $sender, Command $command, $label, array $args){
    if(strtolower($command->getName()) == "mute"){
      if($sender->hasPermission("mute") || $sender->hasPermission("mute.cmd")){
        if(isset($args[0])){
          $player = $this->getServer()->getPlayer($args[0]);
          if($player instanceof Player){
            $name = $player->getName();
            if($this->checkMute($player) == true){
              unset($this->players[$name]);
              $sender->sendMessage(TextFormat::YELLOW.$name." is no longer muted!");
              $player->sendMessage(TextFormat::YELLOW."You can now chat again!");
              return true;
            }else{
              if($this->checkBlock($player) == false){
                array_push($this->players, $name);
                $sender->sendMessage(TextFormat::YELLOW.$name." is not muted");
                $player->sendMessage(TextFormat::YELLOW."You have been muted!");
                return true;
              }else{
                if($sender instanceof Player){
                  $sender->sendMessage(TextFormat::YELLOW."That player can't be muted!");
                  return true;
                }else{
                  array_push($this->players, $name);
                  $sender->sendMessage(TextFormat::YELLOW.$name." is not muted");
                  $player->sendMessage(TextFormat::YELLOW."You have been muted!");
                  return true;
                }
              }
            }
          }else{
            $sender->sendMessage(TextFormat::RED."Error: ".$args[0]." isn't online!");
            return true;
          }
        }else{
          return false;
        }
      }else{
        $sender->sendMessage(TextFormat::RED."You don't have permission to use that command!");
      }
    }
  }
  
  public function onChat(PlayerChatEvent $event){
    $player = $event->getPlayer();
    if($this->checkMute($player) == true){
      $event->setCancelled();
      $sender->sendMessage(TextFormat::YELLOW."You can't chat while muted!");
    }
  }
}
