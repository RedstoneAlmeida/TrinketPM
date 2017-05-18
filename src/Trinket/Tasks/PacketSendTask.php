<?php
namespace Trinket\Tasks;

use pocketmine\scheduler\PluginTask;

use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;

use Trinket\Network\Client\TCPClientSocket;

use Trinket\Utils\TrinketLogger;
use Trinket\Utils\Queue;

use Trinket\Trinket;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class PacketSendTask extends PluginTask{

	private $socket, $logger, $plugin;

	public function __construct(Trinket $plugin, TrinketLogger $logger, TCPClientSocket $socket)
	{
		$this->plugin = $plugin;
		$this->logger = $logger;
		$this->socket = $socket;

		parent::__construct($plugin);
	}

	public function onRun($currentTick)
	{
		$queue = $this->plugin->getSendQueue()->getQueue();

		if(empty($queue))
		{
			return;
		}

		$pk = $this->plugin->getSendQueue()->getNext();
		if(!$pk instanceof DataPacket)
		{
			$this->logger->debug("Instance of non-packet detected in send queue");
			return;
		}

		$this->logger->debug("Sent packet with identifier " . $pk->getId());
		$this->socket->direct($pk);
	}
}