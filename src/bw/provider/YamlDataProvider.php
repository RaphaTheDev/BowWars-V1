<?php

namespace bw\provider;

use pocketmine\level\Level;
use pocketmine\utils\Config;
use bw\arena\Arena;
use bw\BowWars;

/**
 * Class YamlDataProvider
 * @package bw\provider
 */
class YamlDataProvider {

    /** @var BowWars $plugin */
    private $plugin;

    /**
     * YamlDataProvider constructor.
     * @param BowWars $plugin
     */
    public function __construct(BowWars $plugin) {
        $this->plugin = $plugin;
        $this->init();
        $this->loadArenas();
    }

    public function init() {
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder() . "arenas")) {
            @mkdir($this->getDataFolder() . "arenas");
        }
        if(!is_dir($this->getDataFolder() . "saves")) {
            @mkdir($this->getDataFolder() . "saves");
        }
    }

    public function loadArenas() {
        foreach (glob($this->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR . "*.yml") as $arenaFile) {
            $config = new Config($arenaFile, Config::YAML);
            $this->plugin->arenas[basename($arenaFile, ".yml")] = new Arena($this->plugin, $config->getAll(\false));
        }
    }

    public function saveArenas() {
        foreach ($this->plugin->arenas as $fileName => $arena) {
            if($arena->level instanceof Level) {
                foreach ($arena->players as $player) {
                    $player->teleport($player->getServer()->getDefaultLevel()->getSpawnLocation());
                }
                // must be reseted
                $arena->mapReset->loadMap($arena->level->getFolderName());
            }
            $config = new Config($this->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR . $fileName . ".yml", Config::YAML);
            $config->setAll($arena->data);
            $config->save(\false);
        }
    }

    /**
     * @return string $dataFolder
     */
    private function getDataFolder(): string {
        return $this->plugin->getDataFolder();
    }
}
