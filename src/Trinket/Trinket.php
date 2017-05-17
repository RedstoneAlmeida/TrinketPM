<?php
namespace Trinket;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use Trinket\Tasks\BroadcastTask;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class Trinket extends PluginBase{

  protected $serverThread;
  protected $data;

  public function onEnable()
  {
    @mkdir($this->getDataFolder());
    $this->saveDefaultConfig();

    $data = (new Config($this->getDataFolder() . "/config.yml", Config::YAML))->getAll();
    if(!isset($data["id"]))
    {
      $data["id"] = uniqid();
      @unlink($this->getDataFolder() . "/config.yml");
      (new Config($this->getDataFolder() . "/config.yml", Config::YAML, $data))->save();
    }
    if(!isset($data["ip"]) or $data["ip"] === "")
    {
      $data["ip"] = getHostByName(getHostName());
      @unlink($this->getDataFolder() . "/config.yml");
      (new Config($this->getDataFolder() . "/config.yml", Config::YAML, $data))->save();
    }

    if(!isset($data["password"]) or $data["password"] === "")
    {
      $this->getLogger()->warning("Unable to locate 'password' in " . $this->getDataFolder() . "config.yml");
      return;
    }

    $this->serverThread = new ServerThread($data);
    if($this->serverThread->hasErrors())
    {
      $this->getLogger()->error("Unknown error occured within ServerThread");
      $this->getServer()->getPluginManager()->disablePlugin($this);
      return;
    }

    if($data["chat-link"])
    {
      $this->getServer()->getScheduler()->scheduleRepeatingTask(new BroadcastTask($this), 100);
      $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }
  }

  public function onDisable()
  {
    $this->getServerThread()->kill();
  }

  public function getServerThread()
  {
    return $this->serverThread;
  }
}