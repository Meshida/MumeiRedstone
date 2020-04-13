<?php
namespace Meshida\MumeiRedstone\block;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use Meshida\MumeiRedstone\block\RWoodenButton;

class RBlockFactory extends BlockFactory{

	public static function init() : void{
		self::registerBlock(new RWoodenButton(Block::WOODEN_BUTTON), true);
	}

}