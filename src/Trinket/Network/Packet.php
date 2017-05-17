<?php
namespace Trinket\Network;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class Packet{
  
  public $identifier, $error, $password, $data, $protocol, $reason, $chat, $selection, $serverid;

  public function __construct()
  {
    $this->identifier = Info::TYPE_PACKET_DUMMY;
    $this->error = Info::TYPE_ERROR_EMPTY;
    $this->password = Info::TYPE_STRING_EMPTY;
    $this->data = [];
    $this->protocol = Info::PROTOCOL;
    $this->reason = Info::TYPE_STRING_EMPTY;
    $this->chat = Info::TYPE_STRING_EMPTY;
    $this->selection = Info::TYPE_SELECTION_PLAYERS_ALL;
    $this->serverId = 0;
  }

  public function encode()
  {
    return json_encode(["id" => $this->identifier, "error" => $this->error, "password" => $this->password, "data" => $this->data, "protocol" => $this->protocol, "reason" => $this->reason, "chat" => $this->chat, "selection" => $this->selection, "serverId" => $this->serverId]);
  }
}