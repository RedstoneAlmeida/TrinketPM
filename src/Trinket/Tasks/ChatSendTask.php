<?php
namespace Trinket\Tasks;

use pocketmine\scheduler\PluginTask;

use Trinket\Trinket;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class ChatSendTask extends PluginTask{

	private $socket, $logger;

    private $plugin;

	public function __construct(Trinket $plugin) {
		$this->plugin = $plugin;

		parent::__construct($plugin);
	}

	public function onRun($currentTick) {
		$queue = $this->plugin->getChatQueue();
		if(empty($queue)) {
			return;
		}

		for($i = 0; $i < 5; $i++)
		{
			$msg = $queue->getNext();
			if(is_null($msg) or $msg === "")
			{
				continue;
			}
			$this->plugin->getServer()->broadcastMessage($msg);
		}
	}
}