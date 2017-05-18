<?php
namespace Trinket\Network\Client;

use Trinket\Network\DecodedPacket;
use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;
use Trinket\Utils\TrinketLogger;

use Trinket\Utils\Exceptions\SocketError;
use Trinket\Utils\Exceptions\AuthenticationError;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class TCPClientSocket extends \Threaded{

	private $logger, $socket, $name;
	private $connected = False;
	private $attempts = 0;

	public function __construct(TrinketLogger $logger, $password, $host, $name)
	{
		$this->logger = $logger;
		$this->name = $name;

		$host = ($host !== "") ? $host : getHostByName(getHostName());
		$port = 33657;

		$sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$option = @socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
		$connect = @socket_connect($sock, $host, $port);
		$option = @set_time_limit(0);
		$option = @socket_set_nonblock($sock);
		$this->logger->warning("Attempting to connect to host on " . $host . ":" . $port);
		if(!$sock)
		{
			$errorcode = socket_last_error();
			$errormsg = str_replace([PHP_EOL, "\n"], "", socket_strerror($errorcode));
			$this->logger->warning("Couldn't create socket: [" . $errorcode . "] " . $errormsg);
			return;
		}
		if(!$connect)
		{
			$errorcode = socket_last_error();
			$errormsg = str_replace([PHP_EOL, "\n"], "", socket_strerror($errorcode));
			$this->logger->warning("Couldn't connect to host: [" . $errorcode . "] " . $errormsg);
			return;
		}

		$this->socket = $sock;
		$this->connect($password);
	}

	public function direct(DataPacket $pk)
	{
		@socket_write($this->socket, $pk->encode());
	}

	public function read(int $buffer = 1024)
	{
		return new DecodedPacket(@socket_read($this->socket, $buffer));
	}

	public function isConnected()
	{
		return $this->connected;
	}

	public function setConnected($bool = True)
	{
		$this->connected = $bool;
	}

	public function getSocket()
	{
		return $this->socket;
	}

	public function connect($password)
	{
		$this->attempts++;
		$pk = new DataPacket();
		$pk->id = Info::TYPE_PACKET_LOGIN;
		$pk->password = $password;
		$pk->serverId = $this->name;

		$this->direct($pk);
		$pk = $this->read();
		if($pk->id === 0)
		{
			$this->attempts--;
		}
		if($pk->getId() === Info::TYPE_PACKET_LOGIN)
		{
			if($pk->data)
			{
				$this->setConnected(True);
				$this->logger->info("Connected to host server!");
				return;
			}
			else
			{
				switch($pk->error)
				{
					case Info::TYPE_ERROR_INVALID_PASSWORD:
						$this->logger->error("Unable to connect to host server: Invalid Password.");
						return;
					break;
					case Info::TYPE_ERROR_SERVER_ID:
						$this->logger->error("Unable to connect to host server: Invalid ServerID.");
						return;
					break;
				}
			}
		}

		if(!$this->isConnected() && $this->attempts < 10)
		{
			$this->connect($password);
		}
	}

	public function shutdown($forced = false)
	{
		if($forced)
		{
			$this->logger->warning("Socket force closing.");
		}
		$this->logger->warning("Lost connection to host server.");
		@socket_close($this->socket);
		$this->setConnected(False);
	}
}
