<?php
namespace Trinket\Network\Protocol;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */
class Info{

  const TYPE_PACKET_UNKNOWN = 00;
  const TYPE_PACKET_DUMMY = 1;
  const TYPE_PACKET_LOGIN = 68;
  const TYPE_PACKET_DISCONNECT = 82;
  const TYPE_PACKET_COMMAND = 93;
  const TYPE_PACKET_DATA_REQUEST = 37;
  const TYPE_PACKET_DATA_SEND = 45;
  const TYPE_PACKET_COMMAND_EXECUTE = 89;
  const TYPE_DISCONNECT_FORCED = 11000001;
  const TYPE_ERROR_INVALID_PASSWORD = 10101010;
  const TYPE_ERROR_INVALID_PACKET = 10111000;
  const TYPE_ERROR_EMPTY = 11100111; const TYPE_STRING_EMPTY = 11100111;
  const TYPE_SELECTION_PLAYERS_ALL = 10001101;
  const TYPE_SELECTION_PLAYERS_OP = 10111001;
  const TYPE_DATA_CLIENTLIST = 10010000;
  const TYPE_DATA_CHAT = 11001000;
  const PROTOCOL = '1.0.0';
}