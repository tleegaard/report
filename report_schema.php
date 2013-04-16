<?php

$schema['report'] = array(
  'userid' => array('type' => 'int(11)', 'Key'=>'PRI'),
  'settings' => array('type' => 'text')
);

$schema['applist'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'userid' => array('type' => 'int(11)'),
  'name' => array('type' => 'text'),
  'list' => array('type' => 'text')
);

?>
