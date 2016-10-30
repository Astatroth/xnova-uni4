<?php

namespace Xnova\missions;

interface Mission
{
	public function TargetEvent();

	public function EndStayEvent();

	public function ReturnEvent();
}
 
?>