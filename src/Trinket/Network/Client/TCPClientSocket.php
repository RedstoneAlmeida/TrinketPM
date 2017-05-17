<?php
namespace Trinket\Network\Client;

use Trinket\Network\DecodedPacket;
use Trinket\Network\Protocol\Packet;
use Trinket\Network\Protocol\Info;
use Trinket\Utils\TrinketLogger;

use Trinket\Utils\Exceptions\SocketError;
use Trinket\Utils\Exceptions\AuthenticationError;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class TCPClientSocket{

	private $logger, $socket;
	private $connected = False;

	public function __construct(TrinketLogger $logger, $password)
	{
		$this->logger = $logger;

		$host = getHostByName(getHostName());
		$port = 33657;

		$option = @set_time_limit(0);
		$sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$option = @socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
		$connect = @socket_connect($sock, $host, $port);
		$option = @set_time_limit(0);
		$option = @socket_set_nonblock($sock);

		if(!$connect or !$sock)
		{
			throw new SocketError("Unknown Error Occured: Unable to connect to host server");
		}

		$this->socket = $sock;
		$this->connect($password);
	}

	public function direct(Packet $pk)
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
		$pk = new Packet();
		$pk->identifier = Info::TYPE_PACKET_LOGIN;
		$pk->password = $password;

		$this->direct($pk);
		$pk = $this->read();
		if($pk->getId() === Info::TYPE_PACKET_LOGIN)
		{
			if($pk->getAll()["data"])
			{
				$this->setConnected(True);
				$this->logger->info("Connected to host server!");
				return;
			}
			else
			{
				switch($pk->getAll()["error"])
				{
					case Info::TYPE_ERROR_INVALID_PASSWORD:
						throw new AuthenticationError("Unable to connect to host server: Invalid Password.");
					break;
				}
				return;
			}
		}
	}

	public function isEnabled()
	{
		return false;
	}
}
