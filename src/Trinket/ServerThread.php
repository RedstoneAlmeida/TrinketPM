<?php
namespace Trinket;

use pocketmine\Thread;

use pocketmine\utils\TextFormat;

use Trinket\Network\DecodedPacket;
use Trinket\Network\Packet;
use Trinket\Network\Info;

use Trinket\Utils\TrinketLogger;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class ServerThread extends Thread{

	private $workerId;
	private $logger;
	private $host;
	private $port;
	private $isPluginEnabled = True;
	private $hasErrors = False;
	private $password;
	private $connected = False;
	private $messages = [];
	private $socket;
	private $chatEnabled;

	public function __construct($array)
	{
		class_exists('Trinket\Network\DecodedPacket');
		$this->logger = new TrinketLogger();
		$this->logger->warning("Attempting connection to host server...");

		@set_time_limit(0);

		$this->workerId = mt_rand(5000, 10000);
		$this->password = $array["password"];
		$this->serverId = $array["id"];
		$this->chatEnabled = $array["chat-link"];

		$host = isset($array["ip"]) ? $array["ip"] : "0.0.0.0";
		$host = str_replace(" ", "", $array["ip"]);
		$port = intval(isset($array["port"]) ? $array["port"] : 33657);

		$this->host = $host;
		$this->port = $port;

		$this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		@socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
		if(!$this->socket)
		{
			$this->hasErrors = true;
			$this->logger->error("Unable to create socket");
			return;
		}

		$connect = socket_connect($this->socket, $host, $port);
		if(!$connect)
		{
			$this->hasErrors = true;
			$this->logger->error("Unable to connect to host server");
			return;
		}

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
		while($this->isPluginEnabled === True)
		{
			if($this->connected)
			{
				$pk = new Packet();
				$pk->identifier = Info::TYPE_PACKET_DUMMY;
				@socket_write($this->socket, $pk->encode());
			}

			$read = @socket_read($this->socket, 1024);
			if(!is_string($read))
			{
				continue;
			}

			$pk = new DecodedPacket($read);

			switch($pk->getID())
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

					if($pk->get("data"))
					{
						$this->connected = true;
						$this->getLogger()->info("Connected to host server!");
						continue;
					}

					$error = $pk->get("error");
					if($error === Info::ERROR_INVALID_PASSWORD)
					{
						$this->getLogger()->error("Unable to connect to host server error: Invalid Password");
						$this->isPluginEnabled = False;
					}
					elseif($error === Info::ERROR_INVALID_DATA)
					{
						$pk = new Packet();
						$pk->identifier = Info::TYPE_PACKET_LOGIN;
						$pk->password = $password;
						@socket_write($this->socket, $pk->encode());
						$this->start();
						continue;
					}
					else
					{
						continue;
					}
				break;
				case Info::TYPE_PACKET_DISCONNECT:
					if(!$this->connected)
					{
						continue;
					}

					$this->isPluginEnabled = False;
					$this->getLogger()->warning("Disconnected from host server!");
					continue;
				break;
				case Info::TYPE_PACKET_SEND:
					if(!$this->connected)
					{
						continue;
					}

					if(!$this->chatEnabled)
					{
						continue;
					}

					$msg = trim($pk->get("chat"));
					if(empty($msg) or $msg === "")
					{
						continue;
					}
					$this->broascastMessage($msg, $pk->get("select"));
				break;
			}
		}
	}

	public function kill()
	{
		$this->isPluginEnabled = False;
	}

	public function getLogger()
	{
		return $this->logger;
	}

	public function hasErrors() : bool 
	{
		return $this->hasErrors;
	}

	public function broascastMessage(String $msg, $select)
	{
		array_push($this->messages, [$msg, $select]);
	}

	public function addMessage($msg)
	{
		if(!$this->chatEnabled)
		{
			return;
		}
		$pk = new Packet();
		$pk->identifier = Info::TYPE_PACKET_DATA_SEND;
		$pk->chat = (string) $msg;
		$pk->data = Info::TYPE_DATA_CHAT;
		@socket_write($this->socket, $pk->encode());
	}

	public function getMessages()
	{
		return $this->messages;
	}

	public function unset($msg)
	{
		unset($this->messages[$msg]);
	}
}