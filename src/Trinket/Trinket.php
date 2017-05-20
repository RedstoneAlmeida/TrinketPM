<?php
namespace Trinket;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use Trinket\Network\Client\TCPClientSocket;

use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;

use Trinket\Utils\TrinketLogger;
use Trinket\Utils\Queue;
use Trinket\Utils\ThreadedQueue;
use Trinket\Utils\ThreadedStorage;

use Trinket\Commands\TrinketCommand;
use Trinket\Commands\AlertCommand;

use Trinket\Tasks\PacketSendTask;
use Trinket\Tasks\CommandExecuteTask;
use Trinket\Tasks\ChatSendTask;
use Trinket\Tasks\PacketReadTask;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class Trinket extends PluginBase{

    private $socket, $tlogger, $packetqueue, $readtask, $threadedqueue, $threadedstorage;

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

        $data = (new Config($this->getDataFolder() . "/config.yml", Config::YAML))->getAll();
        if(!isset($data["id"])) {
            $data["id"] = uniqid();
            @unlink($this->getDataFolder() . "/config.yml");
            (new Config($this->getDataFolder() . "/config.yml", Config::YAML, $data))->save();
        }

        if(!isset($data["password"]) or $data["password"] === "") {
            $this->getLogger()->warning("Unable to locate 'password' in " . $this->getDataFolder() . "config.yml");
            return;
        }
        if($this->getServer()->getName() !== "PocketMine-MP") {
            $this->getLogger()->warning("TrinketPM is built for PMMP, some features may not work correctly when using " . $this->getServer()->getName());
        }
        $this->threadedstorage = new ThreadedStorage();
        $this->packetqueue = new Queue();
        $this->commandqueue = new Queue();
        $this->socket = new TCPClientSocket(($this->tlogger = new TrinketLogger()), $data["password"], $data["host"], $data["name"]);

        $this->getServer()->getCommandMap()->register("trinket", new TrinketCommand($this));
        $this->getServer()->getCommandMap()->register("alert", new AlertCommand($this));

        $this->threadedqueue = new ThreadedQueue();
        $this->messagequeue = new ThreadedQueue();
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new PacketSendTask($this, $this->tlogger, $this->socket), 5);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new CommandExecuteTask($this), 20);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ChatSendTask($this), 10);
        $this->readtask = new PacketReadTask($this->tlogger, $this->socket, $this->threadedqueue, $this->threadedstorage, $this->messagequeue);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $data), $this);
    }

    public function onDisable() {
        $pk = new DataPacket();
        $pk->id = Info::TYPE_PACKET_DISCONNECT;
        $this->socket->direct($pk);
    }

    public function getPacketQueue() : Queue {
        return ($this->packetqueue instanceof Queue) ? $this->packetqueue : $this->setPacketQueue(new Queue());
    }

    public function setPacketQueue(Queue $packetqueue) {
        $this->packetqueue = $packetqueue;
        return $packetqueue;
    }

    public function getCommandQueue() {
        return $this->readtask->getCommandQueue();
    }

    public function getThreadedStorage() {
        return $this->readtask->getThreadedStorage();
    }

    public function getChatQueue() {
        return $this->readtask->getChatQueue();
    }

    public function getTCPSocket() : TCPClientSocket {
        return $this->socket;
    }
}