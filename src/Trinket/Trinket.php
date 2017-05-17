<?php
namespace Trinket;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use Trinket\Network\Client\TCPClientSocket;

use Trinket\Utils\TrinketLogger;
use Trinket\Utils\Queue;

use Trinket\Commands\PacketCommand;

use Trinket\Tasks\PacketSendTask;
use Trinket\Tasks\PacketReadTask;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class Trinket extends PluginBase{

  private $socket, $tlogger, $queue;

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

    if(!isset($data["password"]) or $data["password"] === "")
    {
      $this->getLogger()->warning("Unable to locate 'password' in " . $this->getDataFolder() . "config.yml");
      return;
    }

    $this->queue = new Queue();
    $this->socket = new TCPClientSocket(($this->tlogger = new TrinketLogger()), $data["password"]);
    $this->getServer()->getCommandMap()->register("packet", new PacketCommand($this));

    $this->getServer()->getScheduler()->scheduleRepeatingTask(new PacketSendTask($this, $this->tlogger, $this->socket), 25);//send packets from queue every 1.25 sec
    $readTask = new PacketReadTask($this->tlogger, $this->socket);
  }

  public function getSendQueue() : Queue
  {
    return ($this->queue instanceof Queue) ? $this->queue : $this->setSendQueue(new Queue());
  }

  public function setSendQueue(Queue $queue)
  {
    $this->queue = $queue;
    return $queue;
  }

  public function getTCPSocket() : TCPClientSocket
  {
    return $this->socket;
  }
}