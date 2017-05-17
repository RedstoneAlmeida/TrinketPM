<?php
namespace Trinket;

use pocketmine\Thread;
use pocketmine\utils\TextFormat;
use Trinket\Network\DecodedPacket;
use Trinket\Network\Packet;
use Trinket\Network\Info;
use Trinket\Utils\TrinketLogger;
use Trinket\Utils\Queue;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class ServerThread extends Thread{

	private $logger, $socket, $password, $packetQueue, $messageQueue;

	private $isPluginEnabled = True;
	private $hasErrors = False;
	private $connected = False;

	public function __construct($array)
	{
		class_exists('Trinket\Network\DecodedPacket');

		$this->logger = new TrinketLogger();
		$this->logger->warning("Attempting connection to host server...");

		$this->packetQueue = new Queue();
		$this->messageQueue = new Queue();

		@set_time_limit(0);

		$this->workerId = mt_rand(5000, 10000);
		$this->password = $array["password"];
		$this->serverId = $array["id"];

		$host = isset($array["ip"]) ? $array["ip"] : "0.0.0.0";
		$host = str_replace(" ", "", $array["ip"]);
		$port = intval(isset($array["port"]) ? $array["port"] : 33657);

		$this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		@socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
		if(!$this->socket)
		{
			$this->hasErrors = true;
			$this->logger->error("Unable to create socket");
			return;
		}
		$connect = @socket_connect($this->socket, $host, $port);

		if(!$connect)
		{
			$this->hasErrors = true;
			$this->logger->error("Unable to connect to host server");
			return;
		}
		@socket_set_nonblock($this->socket);
		$pk = new Packet();
		$pk->identifier = Info::TYPE_PACKET_LOGIN;
		$pk->password = $this->password;
		@socket_write($this->socket, $pk->encode());

		sleep(2);
		$this->start();
	}

	public function __destruct()
	{
		$pk = new Packet();
		$pk->identifier = Info::TYPE_PACKET_DISCONNECT;
		$pk->reason = Info::TYPE_DISCONNECT_FORCED;
		@socket_write($this->socket, $pk->encode());
		@socket_close($this->socket);
	}

	public function run()
	{
		while($this->isPluginEnabled)
		{
			if($this->connected)
			{
				$pk = new Packet();
				@socket_write($this->socket, $pk->encode());
			}

			$pk = $this->getPacketQueue()->getNext();
			if($pk instanceof Packet)
			{
				@socket_write($this->socket, $pk->encode());
			}

			$input = @socket_read($this->socket, 1024);
			if(!is_string($input))
			{
				continue;
			}

			$pk = new DecodedPacket($input);

			switch($pk->getId())
			{
				case Info::TYPE_PACKET_UNKNOWN:
				case Info::TYPE_PACKET_DUMMY:
					continue;
				break;
				case Info::TYPE_PACKET_LOGIN:
					if($this->connected)
					{
						continue;
					}

					$data = $pk->getAll();
					if($data["error"] === Info::TYPE_ERROR_INVALID_PASSWORD)
					{
						$this->getLogger()->warning("Unable to connect to host server! Invalid Password.");
						continue;
					}
					elseif($data["error"] === Info::TYPE_ERROR_INVALID_PACKET)
					{
						$pk = new Packet();
						$pk->identifier = Info::TYPE_PACKET_LOGIN;
						$pk->password = $this->password;
						@socket_write($this->socket, $pk->encode());
						continue;
					}
					elseif($data["error"] === Info::TYPE_ERROR_EMPTY && $data["data"] === True)
					{
						$this->connected = True;
						$this->getLogger()->info("Connected to host server.");
						continue;
					}
				break;
				case Info::TYPE_PACKET_DISCONNECT:
					$this->connected = False;
					$this->getLogger()->warning("Disconnected from host server.");
					continue;
				break;
				case Info::TYPE_PACKET_COMMAND_REQUEST:
					continue;
				break;
				case Info::TYPE_PACKET_DATA_REQUEST:
					continue;
				break;
				case Info::TYPE_PACKET_DATA_SEND:
					if(!$this->connected)
					{
						continue;
					}

					$data = $pk->getAll();
					if($data["data"] === Info::TYPE_DATA_CHAT)
					{
						if($data["chat"] === "")
						{
							continue;
						}
						$this->messageQueue->addItem($data["chat"]);
						continue;
					}
					continue;
				break;
			}
		}
	}

	public function kill()
	{
		$this->isPluginEnabled = False;
		$this->__destruct();
	}

	public function getLogger()
	{
		return $this->logger;
	}

	public function hasErrors() : bool 
	{
		return $this->hasErrors;
	}

	public function getPacketQueue()
	{
		return $this->packetQueue;
	}

	public function getMessageQueue()
	{
		return $this->messageQueue;
	}
}