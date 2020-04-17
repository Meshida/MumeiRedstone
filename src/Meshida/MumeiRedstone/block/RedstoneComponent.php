<?php
namespace Meshida\MumeiRedstone\block;

interface RedstoneComponent
{
	
    public function canBeConnectedFrom(Block $block) : bool;

    public function canBeConnectedTo(Block $block) : bool;

}