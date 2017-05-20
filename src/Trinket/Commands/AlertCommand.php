<?php
namespace Trinket\Commands;

use pocketmine\Server;
use Trinket\Trinket;
use Trinket\Network\Protocol\DataPacket;
use Trinket\Network\Protocol\Info;

use pocketmine\utils\TextFormat;

use pocketmine\command\CommandSender;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class AlertCommand extends BaseCommand{

    private $plugin;

    public function __construct(Trinket $plugin) {
        parent::__construct("alert", $plugin);
        $this->setDescription("Send a message to ALL users connected to the network");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if(!$sender->isOp()) {
            $sender->sendMessage("You dont have permission to use this command");
            return;
        }  
        if(!isset($args[0])) {
            $sender->sendMessage("Usage: /alert <msg>");
            return;
        }

        $msg = TextFormat::LIGHT_PURPLE . "[" .  $sender->getName() . "] " . implode(" ", $args);
        $pk = new DataPacket();
        $pk->id = Info::TYPE_PACKET_CHAT;
        $pk->data = $msg;
        $this->plugin->getPacketQueue()->push($pk);
    }
}
