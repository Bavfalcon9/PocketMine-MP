<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\block\utils\Fallable;
use pocketmine\block\utils\FallableTrait;
use pocketmine\math\Facing;

class ConcretePowder extends Opaque implements Fallable{
	use FallableTrait {
		onNearbyBlockChange as protected startFalling;
	}

	public function __construct(BlockIdentifier $idInfo, string $name, ?BlockBreakInfo $breakInfo = null){
		parent::__construct($idInfo, $name, $breakInfo ?? new BlockBreakInfo(0.5, BlockToolType::SHOVEL));
	}

	public function onNearbyBlockChange() : void{
		if(($block = $this->checkAdjacentWater()) !== null){
			$this->pos->getWorld()->setBlock($this->pos, $block);
		}else{
			$this->startFalling();
		}
	}

	public function tickFalling() : ?Block{
		return $this->checkAdjacentWater();
	}

	private function checkAdjacentWater() : ?Block{
		foreach(Facing::ALL as $i){
			if($i === Facing::DOWN){
				continue;
			}
			if($this->getSide($i) instanceof Water){
				return BlockFactory::getInstance()->get(BlockLegacyIds::CONCRETE, $this->idInfo->getVariant());
			}
		}

		return null;
	}
}
