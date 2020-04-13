<?php

declare(strict_types=1);

namespace Meshida\MumeiRedstone;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Random;
use Meshida\MumeiRedstone\block\RWoodenButton;
use Meshida\MumeiRedstone\block\RBlockFactory;
use Meshida\MumeiRedstone\block\DoorTile;
use Meshida\MumeiRedstone\block\FenceGateTile;
use Meshida\MumeiRedstone\block\HeavyPressurePlateTile;
use Meshida\MumeiRedstone\block\LampTile;
use Meshida\MumeiRedstone\block\LeverTile;
use Meshida\MumeiRedstone\block\LightPressurePlateTile;
use Meshida\MumeiRedstone\block\NotGateTile;
use Meshida\MumeiRedstone\block\PistonArmTile;
use Meshida\MumeiRedstone\block\PistonBaseTile;
use Meshida\MumeiRedstone\block\PistonMovingTile;
use Meshida\MumeiRedstone\block\PressurePlateTile;
use Meshida\MumeiRedstone\block\RedstoneBlockTile;
use Meshida\MumeiRedstone\block\RedstoneWireTile;
use Meshida\MumeiRedstone\block\RepeaterTile;
use Meshida\MumeiRedstone\block\TNT;
use Meshida\MumeiRedstone\item\Repeater;

class Loader extends PluginBase implements Listener
{
    /** @var Loader */
    private static $instance = null;
    public static $tileSources = [];
    public static $random = [];
    /*
    const CAULDRON = "Cauldron";
    const HOPPER = "Hopper";
    const DISPENSER = "Dispenser";
    const DROPPER = "Dropper";
    const DAY_LIGHT_DETECTOR = "DLDetector";
    const NOTEBLOCK = "Music";
    const PISTON = "Piston";
    */

    /**
     * Returns an instance of the plugin
     * @return Loader
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function onLoad()
    {
        self::$instance = $this;
        RBlockFactory::init();
        $this->initBlocks();
        $this->initItems();
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->initCreativeItems();
    }

    private function initBlocks(): void
    {
        BlockFactory::registerBlock(new RedstoneWireTile(55), true);
        BlockFactory::registerBlock(new NotGateTile(75), true);
        BlockFactory::registerBlock(new NotGateTile(76), true);
        BlockFactory::registerBlock(new LightPressurePlateTile(70, LightPressurePlateTile::MOBS), true);//stone plate
        BlockFactory::registerBlock(new LightPressurePlateTile(Block::LIGHT_WEIGHTED_PRESSURE_PLATE, LightPressurePlateTile::EVERYTHING), true);//gold plate
        BlockFactory::registerBlock(new LeverTile(), true);
        BlockFactory::registerBlock(new LampTile(Block::REDSTONE_LAMP), true);
        BlockFactory::registerBlock(new LampTile(Block::LIT_REDSTONE_LAMP), true);
        //BlockFactory::registerBlock(new RWoodenButton(), true);
        BlockFactory::registerBlock(new RWoodenButton(Block::WOODEN_BUTTON), true);
        BlockFactory::registerBlock(new FenceGateTile(Block::FENCE_GATE), true);
        BlockFactory::registerBlock(new FenceGateTile(Block::SPRUCE_FENCE_GATE), true);
        BlockFactory::registerBlock(new FenceGateTile(Block::BIRCH_FENCE_GATE), true);
        BlockFactory::registerBlock(new FenceGateTile(Block::JUNGLE_FENCE_GATE), true);
        BlockFactory::registerBlock(new FenceGateTile(Block::DARK_OAK_FENCE_GATE), true);
        BlockFactory::registerBlock(new FenceGateTile(Block::ACACIA_FENCE_GATE), true);
        BlockFactory::registerBlock(new DoorTile(Block::IRON_DOOR_BLOCK, 0, "Iron Door", Item::IRON_DOOR), true);
        BlockFactory::registerBlock(new DoorTile(Block::OAK_DOOR_BLOCK, 0, "OAK Door", Item::OAK_DOOR), true);
        BlockFactory::registerBlock(new DoorTile(Block::SPRUCE_DOOR_BLOCK, 0, "Spruce Door", Item::SPRUCE_DOOR), true);
        BlockFactory::registerBlock(new DoorTile(Block::BIRCH_DOOR_BLOCK, 0, "Birch Door", Item::BIRCH_DOOR), true);
        BlockFactory::registerBlock(new DoorTile(Block::JUNGLE_DOOR_BLOCK, 0, "Jungle Door", Item::JUNGLE_DOOR), true);
        BlockFactory::registerBlock(new DoorTile(Block::ACACIA_DOOR_BLOCK, 0, "Acacia Door", Item::ACACIA_DOOR), true);
        BlockFactory::registerBlock(new DoorTile(Block::DARK_OAK_DOOR_BLOCK, 0, "Dark Oak Door", Item::DARK_OAK_DOOR), true);
        BlockFactory::registerBlock(new HeavyPressurePlateTile(147, 15), true);
        BlockFactory::registerBlock(new HeavyPressurePlateTile(148, 150), true);
        BlockFactory::registerBlock(new PressurePlateTile(Block::WOODEN_PRESSURE_PLATE, LightPressurePlateTile::EVERYTHING), true);
        BlockFactory::registerBlock(new RepeaterTile(Block::UNPOWERED_REPEATER), true);
        BlockFactory::registerBlock(new RepeaterTile(Block::POWERED_REPEATER), true);
        #BlockFactory::registerBlock(new PistonBaseTile(33, false), true);
        #BlockFactory::registerBlock(new PistonBaseTile(29, true), true);
        #BlockFactory::registerBlock(new PistonArmTile(34), true);
        #BlockFactory::registerBlock(new PistonMovingTile(36), true);
        BlockFactory::registerBlock(new RedstoneBlockTile(Block::REDSTONE_BLOCK), true);
        BlockFactory::registerBlock(new TNT(), true);
    }

    private function initItems(): void
    {
        ItemFactory::registerItem(new Repeater(100), true);
    }

    private function initCreativeItems(): void
    {
        $ids = [
            324, 
            Item::IRON_DOOR, 
            Item::REPEATER, 
            29, 33, 69, 70, 72, 76, 77, 107, 123, 143, 147, 148, 183, 184, 185, 186, 187, 427, 428, 429, 430, 431
        ];
        $this->addCreativeItems($ids);
        //Item::initCreativeItems();
    }

    public function addCreativeItems(array $ids): void
    {
        foreach ($ids as $id) {
            $item = Item::get($id);
            if (!Item::isCreativeItem($item)) {
                Item::addCreativeItem($item);
            }
        }
    }

    public function onLevelLoad(LevelLoadEvent $event)
    {
        if (!array_key_exists($event->getLevel()->getId(), self::$tileSources)) self::$tileSources[$event->getLevel()->getId()] = new TileSource($event->getLevel());
        if (!array_key_exists($event->getLevel()->getId(), self::$random)) self::$random[$event->getLevel()->getId()] = new Random($event->getLevel()->getSeed());
    }

    /**
     * @param Level $level
     * @return TileSource|null
     */
    public static function getTileSource(Level $level): ?TileSource
    {
        if (!array_key_exists($level->getId(), self::$tileSources)) self::$tileSources[$level->getId()] = new TileSource($level);
        return self::$tileSources[$level->getId()] ?? null;
    }

    public static function getRandom(Level $level): Random
    {
        if (!array_key_exists($level->getId(), self::$random)) self::$random[$level->getId()] = new Random($level->getSeed());
        return self::$random[$level->getId()] ?? null;
    }

}
/*
Button.php
Door.php
FenceGate.php
HeavyPressurePlate.php
Lamp.php
Lever.php
LightPressurePlate.php
NotGate.php
PistonArm.php
PistonBase.php
PistonMoving.php
PistonPushInfo.h
PressurePlate.php
RedstoneBlock.php
RedstoneColors.h
RedstoneWire.php
Repeater.php
Tile.php
TrapDoor.php
*/