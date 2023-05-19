<?php

namespace NewPlayerMC;

use NewPlayerMC\command\FurnaceCommand;

class Main extends \pocketmine\plugin\PluginBase
{
    /** @var Main  */
    private static Main $instance;

    protected function onEnable(): void
    {
        self::$instance = $this;
        $this->saveDefaultConfig();

        $this->getServer()->getCommandMap()->register("furnace", new FurnaceCommand());
    }

    /**
     * @return Main
     */
    public static function getInstance(): Main
    {
        return self::$instance;
    }

}