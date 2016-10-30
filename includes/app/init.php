<?php

use Xcms\eventManager;

$eventManager = eventManager::getInstance();
$eventManager->addEventHandler('onBeforeUserParse', Array('\Xnova\User', 'onBeforeUserParse'), 1);
$eventManager->addEventHandler('onAfterUserParse', Array('\Xnova\User', 'onAfterUserParse'), 1);
$eventManager->addEventHandler('onBeforeUserDelete', Array('\Xnova\User', 'onBeforeUserDelete'), 1);

?>