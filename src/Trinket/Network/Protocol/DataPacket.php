<?php
namespace Trinket\Network\Protocol;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class DataPacket{
  
  public $id, $error, $password, $data, $reason, $chat, $selection, $serverId;

  public function __construct()
  {
    $this->id = Info::TYPE_PACKET_DUMMY;
    $this->error = '';
    $this->password = '';
    $this->data = '';
    $this->reason = '';
    $this->chat = '';
    $this->selection = '';
    $this->serverId = '';
  }

  public function getId()
  {
    return $this->identifier;
  }
  
  public function encode()
  {
    return str_pad(json_encode(["id" => $this->id, "error" => $this->error, "password" => $this->password, "data" => $this->data, "protocol" => Info::PROTOCOL, "reason" => $this->reason, "chat" => $this->chat, "selection" => $this->selection, "serverId" => $this->serverId]), 1024);
  }
}