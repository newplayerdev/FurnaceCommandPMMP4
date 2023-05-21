<?php

namespace NewPlayerMC\command;

use NewPlayerMC\Main;
use pocketmine\command\CommandSender;
use pocketmine\crafting\FurnaceType;
use pocketmine\player\Player;
use pocketmine\Server;

class FurnaceCommand extends \pocketmine\command\defaults\PluginsCommand
{
    private $cooldowns = [];

    public function __construct()
    {
        parent::__construct("furnace");
        $this->setPermission("furnace.use");
        $this->setPermissionMessage(Main::getInstance()->getConfig()->get("permission_message"));
        $this->setDescription(Main::getInstance()->getConfig()->get("command_description"));
        $this->setUsage("furnace [all]");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return $sender->sendMessage("§cNo console allowed");
        if (!$this->testPermission($sender)) return $sender->sendMessage($this->getPermissionMessage());
        if (count($args) > 1) return $sender->sendMessage("§c/" . $this->getUsage());

        $cooldown = Main::getInstance()->getConfig()->get("cooldown");
        $furnacemanager = Server::getInstance()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE());

        if (isset($this->cooldowns[$sender->getName()]) and time() - $this->cooldowns[$sender->getName()] < $cooldown) {
            $time = time() - $this->cooldowns[$sender->getName()];
            $sender->sendMessage(str_replace("{cooldown}", ($cooldown - $time), Main::getInstance()->getConfig()->get("cooldown-message")));
        } else {
            $this->cooldowns[$sender->getName()] = time();
            if (isset($args[0])) {
                foreach ($sender->getInventory()->getContents() as $slot => $item) {
                    if ($furnacemanager->match($item) !== null) {
                        $sender->getInventory()->setItem($slot, $furnacemanager->match($item)->getResult()->setCount($item->getCount()));
                    }
                }
                $sender->sendMessage(Main::getInstance()->getConfig()->get("furnace_all_message"));
            } else {
                if ($furnacemanager->match($sender->getInventory()->getItemInHand()) === null) {
                    $sender->sendMessage(Main::getInstance()->getConfig()->get("item_not_furnacable"));
                } else {
                    $sender->getInventory()->setItemInHand($furnacemanager->match($sender->getInventory()->getItemInHand())->getResult()->setCount($sender->getInventory()->getItemInHand()->getCount()));
                    $sender->sendMessage(Main::getInstance()->getConfig()->get("furnace_message"));
                }
            }
        }
    }

}