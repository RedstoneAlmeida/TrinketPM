<?php
namespace Trinket\Tasks;

use pocketmine\scheduler\PluginTask;

use Trinket\Trinket;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class BroadcastTask extends PluginTask{

  public function __construct(Trinket $plugin)
  {
    $this->plugin = $plugin;
    parent::__construct($plugin);
  }

  public function onRun($currentTick)
  {
    $queue = $this->plugin->getServerThread()->getMessageQueue();
    if(empty($queue->getQueue()))
    {
      return;
    }

    $next = rtrim($queue->getNext());
    $this->plugin->getServer()->broadcastMessage($next);
  }
}