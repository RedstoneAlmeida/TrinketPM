<?php
namespace Trinket\Tasks;

use pocketmine\Thread;

use Trinket\Network\DecodedPacket;
use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;
use Trinket\Network\Client\TCPClientSocket;

use Trinket\Utils\TrinketLogger;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class PacketReadTask extends Thread{

	private $socket, $logger, $nullpacket;

	public function __construct(TrinketLogger $logger, TCPClientSocket $socket)
	{
		$this->logger = $logger;
		$this->socket = $socket;

		$this->start();
	}

	public function run()
	{
		while($this->socket->isConnected())
		{
			$pk = $this->socket->read();
			if(intval($pk->protocol) !== Info::PROTOCOL)
			{
				if(intval($pk->protocol) === 0)
				{
					continue;
				}
				$arg = ($pk->protocol > Info::PROTOCOL) ? "outdated" : "unknown";
				$this->logger->error("Recieved packet with " . $arg . " protocol.");
				continue;
			}
			switch($pk->getId())
			{
				case Info::TYPE_PACKET_LOGIN:
					continue;
				break;
				case Info::TYPE_PACKET_DISCONNECT:
					$this->socket->shutdown();
				break;
				case Info::TYPE_PACKET_DUMMY:
					$pk = new DataPacket();
					$pk->id = Info::TYPE_PACKET_DUMMY;
					$this->socket->direct($pk);
				break;
				case Info::TYPE_PACKET_COMMAND_EXECUTE:
					$cmd = $pk->data;
					//$this->
			}
		}
	}
}