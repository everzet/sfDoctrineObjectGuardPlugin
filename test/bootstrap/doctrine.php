<?php

// guess current application
$app = 'frontend';
include dirname(__FILE__) . '/unit.php';
include dirname(__FILE__) . '/functional.php';

new sfDatabaseManager($configuration);
Doctrine::loadData(dirname(__FILE__) . '/../fixtures');
$conn = Doctrine_Manager::getInstance()->getCurrentConnection();
