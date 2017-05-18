<?php
namespace Trinket\Network\Protocol;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class DataPacket{
  
  public $id, $error, $password, $data, $reason, $chat, $selection, $serverId, $to;

  public function __construct() {
    $this->id = Info::TYPE_PACKET_DUMMY;
    $this->error = Info::TYPE_ERROR_EMPTY;
    $this->password = Info::TYPE_STRING_EMPTY;
    $this->data = Info::TYPE_DATA_EMPTY;
    $this->reason = Info::TYPE_STRING_EMPTY;
    $this->chat = Info::TYPE_STRING_EMPTY;
    $this->selection = Info::TYPE_SELECTION_PLAYERS_ALL;
    $this->serverId = Info::TYPE_STRING_EMPTY;
    $this->to = Info::TYPE_STRING_EMPTY;
  }

  public function getId() {
    return $this->id;
  }
  
  public function encode() {
    return str_pad(json_encode(["id" => $this->id, "error" => $this->error, "password" => $this->password, "data" => $this->data, "protocol" => Info::PROTOCOL, "reason" => $this->reason, "chat" => $this->chat, "selection" => $this->selection, "serverId" => $this->serverId, "to" => $this->to]), 1024);
  }
}