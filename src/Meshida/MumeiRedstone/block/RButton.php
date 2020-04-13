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

	public function __construct(int $id, int $meta = 0) {
		parent::__construct($meta);
	}

	protected $id = $this->id;

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

	protected $delay = ($this->id === Block::STONE_BUTTON) ? 20 : 30;

//$player->getLevel()->scheduleDelayedBlockUpdate($this, $this->getTickDelay());

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool {

		$this->meta = $face;

		if(!$this->mayPlace($this->getLevel(), $this->x,$this->y,$this->z, $face)) return false;

		$this->getLevel()->setBlock($this, $this, true, true);

		$this->getLevel()->scheduleDelayedBlockUpdate(new Vector3($this->x, $this->y, $this->z), $this->delay);

		return true;
	}
	/*
	public function getVisualShape(int $data, AxisAlignedBB $AxisAlignedBB, bool $b): AxisAlignedBB
	{
	$f = 0.1875;
	$f1 = 0.125;
	$f2 = 0.125;
	$AxisAlignedBB->setBounds(0.5 - $f, 0.5 - $f1, 0.5 - $f2, 0.5 + $f, 0.5 + $f1, 0.5 + $f2);
	return $AxisAlignedBB;
	}
	
	public function getVisualShape2(TileSource $region, int $x, int $y, int $z, AxisAlignedBB $AxisAlignedBB, bool $b): AxisAlignedBB
	{
	$data = $region->getBlockDataAt($x, $y, $z);
	$rot = $data & 7;
	$powered = ($data & 8) > 0;
	$f = 0.375;
	$f1 = 0.625;
	$f2 = 0.1875;
	$f3 = 0.125;
	if ($powered) $f3 = 0.0625;
	if ($rot == 1)
	$AxisAlignedBB->setBounds(0.0, $f, 0.5 - $f2, $f3, $f1, 0.5 + $f2);
	else if ($rot == 2)
	$AxisAlignedBB->setBounds(1.0 - $f3, $f, 0.5 - $f2, 1.0, $f1, 0.5 + $f2);
	else if ($rot == 3)
	$AxisAlignedBB->setBounds(0.5 - $f2, $f, 0.0, 0.5 + $f2, $f1, $f3);
	else if ($rot == 4)
	$AxisAlignedBB->setBounds(0.5 - $f2, $f, 1.0 - $f3, 0.5 + $f2, $f1, 1.0);
	else if ($rot == 5)
	$AxisAlignedBB->setBounds($f, 0.0, 0.5 - $f2, $f1, $f3, 0.5 + $f2);
	else if ($rot == 6)
	$AxisAlignedBB->setBounds($f, 1.0 - $f3, 0.5 - $f2, $f1, 1.0, 0.5 + $f2);
	
	return $AxisAlignedBB;
	}
	*/

	public function onActivate(Item $item, Player $player = null): bool {
		$data = $this->getDamage();
		$rot = $data & 7;
		$power = 8 - ($data & 8);
		if ($power == 0) return true;
		$this->getLevel()->setBlock($this, BlockFactory::get($this->getId(), $rot + $power));
		$this->getLevel()->addSound(new GenericSound($this->add(0.5, 0.5, 0.5), LevelEventPacket::EVENT_REDSTONE_TRIGGER, 0.6));
		$this->getLevel()->scheduleNeighbourBlockUpdates($this);

		switch ($rot) {
			case 1:
			$player->getLevel()->scheduleNeighbourBlockUpdates($this->add(-1, 0));
			$this->sendRedstoneSignal($this, $this->getLevel(), $this->add(-1, 0), 15);
			break;
			case 2:
			$player->getLevel()->scheduleNeighbourBlockUpdates($this->add(1));
			$this->sendRedstoneSignal($this, $this->getLevel(), $this->add(1), 15);
			break;
			case 3:
			$player->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, 0, -1));
			$this->sendRedstoneSignal($this, $this->getLevel(), $this->add(0, 0, -1), 15);
			break;
			case 4:
			$player->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, 0, 1));
			$this->sendRedstoneSignal($this, $this->getLevel(), $this->add(0, 0, 1), 15);
			break;
			case 5:
			$player->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, -1));
			$this->sendRedstoneSignal($this, $this->getLevel(), $this->add(0, -1), 15);
			break;
			case 6:
			$player->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, 1));
			$this->sendRedstoneSignal($this, $this->getLevel(), $this->add(0, 1), 15);
			break;
		}

		$player->getLevel()->scheduleDelayedBlockUpdate($this, $this->delay);
		return true;
	}

	public function onBreak(Item $item, Player $player = null): bool {
		$data = $this->getDamage();
		if (($data & 8) > 0) {
			$this->getLevel()->scheduleNeighbourBlockUpdates($this);

			switch ($data & 7) {
				case 1:
				$this->getLevel()->scheduleNeighbourBlockUpdates($this->add(-1));
				break;
				case 2:
				$this->getLevel()->scheduleNeighbourBlockUpdates($this->add(1));
				break;
				case 3:
				$this->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, 0, -1));
				break;
				case 4:
				$this->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, 0, 1));
				break;
				case 5:
				$this->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, -1));
				break;
				case 6:
				$this->getLevel()->scheduleNeighbourBlockUpdates($this->add(0, 1));
				break;
			}
		}
		return parent::onBreak($item, $player);
	}

	public function getDirectSignal(TileSource $region, int $x, int $y, int $z, int $side): int {
		return (($region->getBlockDataAt($x, $y, $z) & 8) > 0) ? 15 : 0;
	}

	public function getSignalPower(TileSource $region, int $x, int $y, int $z, int $side): int {
		$data = $region->getBlockDataAt($x, $y, $z);
		if (($data & 8) == 0) return 0;
		$rot = $data & 7;

		if ($rot + $side == 6) {
			return 15;
		}
		return 0;
	}

	public function isSignalSource(): bool
	{
		return true;
	}

	public function sendRedstoneSignal(Block $source, Level $level, Vector3 $vec, $power)
	{
		$block = $level->getBlock($vec);
		if ($block->isSolid() || $block instanceof RedstoneComponent) {
			$block->onRefleshRedstoneSignal($source, $source, $power);
		}
	}

	public function mayPlace(Level $level, int $x, int $y, int $z, int $side = 0): bool {
		$block = $level->getBlockAt($x, $y, $z)->getSide(Vector3::getOppositeSide($side));
		$isRedstonePlacementException = ($block->getId() == 44 && ($block->getDamage() & 8) > 0) || ($block->getId() == 158 && ($block->getDamage() & 8) > 0) || $block->getId() == 89;

		return $block->isSolid() || $isRedstonePlacementException;
/*
		switch ($side) {
			case 2:
			return $level->getBlockAt($x, $y, $z + 1) instanceof Solid;
			case 3:
			return $level->getBlockAt($x, $y, $z - 1) instanceof Solid;
			case 4:
			return $level->getBlockAt($x + 1, $y, $z) instanceof Solid;
			case 5:
			return $level->getBlockAt($x - 1, $y, $z) instanceof Solid;
			case 1:
			return $level->getBlockAt($x, $y - 1, $z) instanceof Solid;
			case 0:
			return $level->getBlockAt($x, $y + 1, $z) instanceof Solid;
		}*/
		echo "WoodenButtonFalse in mayPlace\n";
		return false;
	}

	/*
	public function canSurvive(TileSource $region, int $x, int $y, int $z): bool
	{
	//return true;//TODO remove
	$rot = $region->getBlockDataAt($x, $y, $z) & 7;
	switch ($rot) {
	case 6:
	return $region->getLevel()->getBlockAt($x, $y + 1, $z) instanceof Solid;
	case 5:
	return $region->getLevel()->getBlockAt($x, $y - 1, $z) instanceof Solid;
	case 4:
	return $region->getLevel()->getBlockAt($x, $y, $z + 1) instanceof Solid;
	case 3:
	return $region->getLevel()->getBlockAt($x, $y, $z - 1) instanceof Solid;
	case 2:
	return $region->getLevel()->getBlockAt($x + 1, $y, $z) instanceof Solid;
	case 1:
	return $region->getLevel()->getBlockAt($x - 1, $y, $z) instanceof Solid;
	}
	return false;
	}
	*/

	public function onScheduledUpdate(): void {
		$this->tick($this->getLevel(), $this->x, $this->y, $this->z);
	}

	public function tick(Level $level, int $x, int $y, int $z): void {
		$data = $level->getBlockDataAt($x, $y, $z);

		if (($data & 8) == 0) {
			//$this->toggleIfArrowInside($region, $x, $y, $z);
			return;
		}

		$rot = $data & 7;

		$level->setBlockDataAt($x, $y, $z, $rot);

	//$level->setBlock(new Vector3($x, $y, $z), new self($this->getId(), $rot));

		$level->scheduleNeighbourBlockUpdates(new Vector3($x, $y, $z));
		//TODO: Vector3::SIDE_DOWN etc.
		switch ($rot) {
			case 1:
			$level->scheduleNeighbourBlockUpdates(new Vector3($x - 1, $y, $z));
			break;
			case 2:
			$level->scheduleNeighbourBlockUpdates(new Vector3($x + 1, $y, $z));
			break;
			case 3:
			$level->scheduleNeighbourBlockUpdates(new Vector3($x, $y, $z - 1));
			break;
			case 4:
			$level->scheduleNeighbourBlockUpdates(new Vector3($x, $y, $z + 1));
			break;
			case 5:
			$level->scheduleNeighbourBlockUpdates(new Vector3($x, $y - 1, $z));
			break;
			case 6:
			$level->scheduleNeighbourBlockUpdates(new Vector3($x, $y + 1, $z));
			break;
		}
		$level->addSound(new GenericSound($this->add(0.5, 0.5, 0.5), LevelEventPacket::EVENT_REDSTONE_TRIGGER, 0.5));
	}

	public function hasEntityCollision(): bool {
		return true;
	}

	public function getCollisionBoxes(): array {
		return parent::getCollisionBoxes();
	}
}