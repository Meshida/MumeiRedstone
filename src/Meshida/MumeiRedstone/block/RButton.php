<?php
namespace Meshida\MumeiRedstone\block;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Button;
use pocketmine\block\Solid;
use pocketmine\block\WoodenButton;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\GenericSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\utils\Random;

use Meshida\MumeiRedstone\TileSource;

class RButton extends Button implements RedstonePowerSource {

	/**
	 * Damage & 8
	 * 0: UP
	 * 1: DOWN
	 * 2: SOUTH
	 * 3: NORTH
	 * 4: EAST
	 * 5: WEST
	 */

	public function __construct(int $id, int $meta = 0) {
		parent::__construct($meta);
	}

	protected $id = $this->id;

	protected $delay = ($this->id == Block::WOODEN_BUTTON) ? 30 : 20;

	public function getName() : string{
		switch ($this->id) {
			case Block::WOODEN_BUTTON:
			return 'Wooden Button';
			break;
			
			case Block::STONE_BUTTON:
			return 'Stone Button';
			break;
		}
		return 'Button';
	}

	public function getHardness() : float{
		return 0.5;
	}

	public function getToolType() : int{
		switch ($this->id) {
			case Block::WOODEN_BUTTON:
			return BlockToolType::TYPE_AXE;
			break;
			
			case Block::STONE_BUTTON:
			return BlockToolType::TYPE_PICKAXE;
			break;
		}
		return BlockToolType::TYPE_AXE;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool {

		//$this->meta = $face;

		if(!$this->mayPlace($face)) return false;

		$this->getLevel()->setBlock($this, $this, true, true);

		$this->getLevel()->scheduleDelayedBlockUpdate($this, $this->delay);

		return true;
	}

	public function onActivate(Item $item, Player $player = null): bool {
		$data = $this->getDamage();
		$rot = $data & 7;
		if (($data & 8) > 0) return true;
		$this->setDamage($rot | 8);
		$this->getLevel()->addSound(new GenericSound($this->add(0.5, 0.5, 0.5), LevelEventPacket::EVENT_REDSTONE_TRIGGER, 0.6));
		$this->getLevel()->scheduleNeighbourBlockUpdates($this);

		foreach ($this->getAllSides() as $side) {
			if ($this->canBeConnectedTo($side)) {
				$side->onRefleshRedstoneSignal($this, $this, 15);
			}
		}

		$this->getLevel()->scheduleDelayedBlockUpdate($this, $this->delay);
		return true;
	}

	public function onBreak(Item $item, Player $player = null): bool {
		$data = $this->getDamage();
		if (($data & 8) > 0) {
			foreach ($this->getAllSides() as $side) {
				if ($this->canBeConnectedTo($side)) {
					$side->onRefleshRedstoneSignal($this, $this, 0);
				}
			}
		}
		return parent::onBreak($item, $player);
	}

	public function mayPlace(int $face = 0): bool {
		$block = $this->getSide($this->getOppositeSide($face));
		$exception = ($block->getId() == 44 and ($block->getDamage() & 8) > 0) || ($block->getId() == 158 and ($block->getDamage() & 8) > 0) || $block->getId() == 89;
		return $block->isSolid() || $exception;
	}

	public function onScheduledUpdate(): void {
		$this->tick();
	}

	public function tick(): void {
		$data = $this->getDamage();

		if (($data & 8) == 0) {
			//$this->checkArrow($this);
			return;
		}

		$rot = $data & 7;

		$this->setDamage($rot);

		foreach ($this->getAllSides() as $side) {
			if ($this->canBeConnectedTo($side)) {
				$side->onRefleshRedstoneSignal($this, $this, 0);
			}
		}

		$level->addSound(new GenericSound($this->add(0.5, 0.5, 0.5), LevelEventPacket::EVENT_REDSTONE_TRIGGER, 0.5));
	}

	public function hasEntityCollision(): bool {
		return true;
	}

	/*
	public function getCollisionBoxes(): array {
		return parent::getCollisionBoxes();
	}
	*/

	public function canBeConnectedTo(Block $block)
	{
		if ($block->isSolid() and !$block instanceof RedstoneComponent) {
			for ($i = 0; $i <= 5; $i++) {
				if ($this->getSide($i) === $block) {
					if ($this->getDamage() ^ 0x1 == $i) {
						return true;
					}
					return false;
				}
			}
		}

		for ($i = 0; $i <= 5; $i++) {
			if ($this->getSide($i) === $block) {
				if ($this->getDamage() & 7 == $i) {
					return false;
				}
				return true;
			}
		}
		return false;
	}

	public function canBeConnectedFrom(Block $block)
	{
		return false;
	}

	public function getSignalPower(): int {
		$data = $this->getDamage();
		if (($data & 8) == 1) return 15;
		return 0;
	}
}