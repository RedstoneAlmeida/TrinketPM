<?php
namespace Trinket;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class EventListener implements Listener{

  private $plugin;

  public function __construct(Trinket $plugin) {
    $this->plugin = $plugin;
  }

  public function onChat(PlayerChatEvent $ev) {
    if($ev->isCancelled()) {
      return;
    }

    $pk = new DataPacket();
    $pk->id = Info::TYPE_PACKET_CHAT;
    $pk->data = strval(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $ev->getPlayer()->getDisplayName() . " : " . $ev->getMessage()));
    $this->plugin->getPacketQueue()->push($pk);
    return;
  }
}