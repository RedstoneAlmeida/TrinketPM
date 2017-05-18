<?php
namespace Trinket\Commands;

use Trinket\Trinket;
use Trinket\Network\Protocol\Packet;
use Trinket\Network\Protocol\Info;

use pocketmine\command\CommandSender;

/* Copyright (C) ImagicalGamer - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jake C <imagicalgamer@outlook.com>, May 2017
 */

class TrinketCommand extends BaseCommand{

    private $plugin;

    public function __construct(Trinket $plugin){
        parent::__construct("trinket", $plugin);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args) 
    {
        if($sender instanceof Player)
        {
            $sender->sendMessage("Unable to run command as player.");
            return false;
        }

        if(!isset($args[0]))
        {
            $sender->sendMessage("Usage: /trinket <test:info>");
            return;
        }
        switch($args[0])
        {
            case "test":
                $sender->sendMessage("Sending dummy packet to host.");
                $pk = new Packet();
                $this->plugin->getSendQueue()->push($pk);
            break;
            case "info":
                $sender->sendMessage("Trinket v" . $this->plugin->getDescription()->getVersion() . " Protocol: " . Info::PROTOCOL);
                $conn = ($this->plugin->getTCPSocket()->isConnected() === True) ? "True" : "False";
                $sender->sendMessage("Connected: " . $conn);
            break;
            default:
                $sender->sendMessage("Usage: /trinket <test:info>");
            break;
        }
    }
}