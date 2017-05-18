<?php
namespace Trinket\Tasks;

use pocketmine\scheduler\PluginTask;

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

	private $socket, $logger, $plugin;

	public function __construct(Trinket $plugin, TrinketLogger $logger)
	{
		$this->plugin = $plugin;
		$this->logger = $logger;

		parent::__construct($plugin);
	}

	public function onRun($currentTick)
	{
		$queue = $this->plugin->getCommandQueue()->getQueue();

		if(empty($queue))
		{
			return;
		}

		$cmd = $this->plugin->getCommandQueue()->getNext();
		echo($cmd);
	}
}