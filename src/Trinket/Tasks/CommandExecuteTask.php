<?php
namespace Trinket\Tasks;

use pocketmine\scheduler\PluginTask;

use pocketmine\command\ConsoleCommandSender;

use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;

use Trinket\Network\Client\TCPClientSocket;

use Trinket\Utils\TrinketLogger;
use Trinket\Utils\ThreadedQueue;

use Trinket\Trinket;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class CommandExecuteTask extends PluginTask{

	private $socket, $logger;

    private $plugin;

	public function __construct(Trinket $plugin, TrinketLogger $logger) {
		$this->plugin = $plugin;
		$this->logger = $logger;

		parent::__construct($plugin);
	}

	public function onRun($currentTick) {
		$queue = $this->plugin->getCommandQueue();
		if(empty($queue)) {
			return;
		}


		$cmd = $this->plugin->getCommandQueue()->getNext();
		if(is_null($cmd) or !is_string($cmd)) {
			return;
		}
		$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
	}
}