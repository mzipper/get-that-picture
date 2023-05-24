<?php

// put full path to Smarty.class.php
require('Smarty-4.3.0/libs/Smarty.class.php');
$smarty = new Smarty();
$smarty->debugging=1;
$smarty->setTemplateDir('smarty/templates');
$smarty->setCompileDir('smarty/templates_c');
$smarty->setCacheDir('smarty/cache');
$smarty->setConfigDir('smarty/configs');


$smarty->display('index.tpl');
