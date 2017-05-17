<?php
namespace Trinket;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

use Trinket\Network\Packet;
use Trinket\Network\Info;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class EventListener implements Listener{

  private $plugin;

  public function __construct(Trinket $plugin)
  {
    $this->plugin = $plugin;
  }

  public function onChat(PlayerChatEvent $ev)
  {
    if($ev->isCancelled())
    {
      return;
    }
    $format = $ev->getMessage(); //NEEDS TO BE UPDATED TO SUPPORT PURECHAT ETC.
    $pk = new Packet();
    $pk->identifier = Info::TYPE_PACKET_DATA_SEND;
    $pk->chat = $format;
    $this->plugin->getServerThread()->getPacketQueue()->addItem($pk);
  }
}