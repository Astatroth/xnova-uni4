<?php

define('OPBEPATH', dirname(__DIR__).'/');

require (OPBEPATH.'utils/GeometricDistribution.php');
require (OPBEPATH.'utils/Gauss.php');
require (OPBEPATH.'utils/Iterable.php');
require (OPBEPATH.'utils/Math.php');
require (OPBEPATH.'utils/Number.php');
require (OPBEPATH.'models/Type.php');
require (OPBEPATH.'models/ShipType.php');
require (OPBEPATH.'models/Fleet.php');
require (OPBEPATH.'models/HomeFleet.php');
require (OPBEPATH.'models/Defense.php');
require (OPBEPATH.'models/Ship.php');
require (OPBEPATH.'models/Player.php');
require (OPBEPATH.'models/PlayerGroup.php');
require (OPBEPATH.'combatObject/Fire.php');
require (OPBEPATH.'combatObject/PhysicShot.php');
require (OPBEPATH.'combatObject/ShipsCleaner.php');
require (OPBEPATH.'combatObject/FireManager.php');
require (OPBEPATH.'core/Battle.php');
require (OPBEPATH.'core/BattleReport.php');
require (OPBEPATH.'core/Round.php');
require (OPBEPATH.'constants/battle_constants.php');

?>
