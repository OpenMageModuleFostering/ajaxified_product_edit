<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */

$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('alekseon_ajaxifiedProductEdit/gridview')};
CREATE TABLE {$this->getTable('alekseon_ajaxifiedProductEdit/gridview')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  `description` text default NULL,
  `user_roles` text default NULL,
  `enable` bit default 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('alekseon_ajaxifiedProductEdit/gridview_attributeColumns')};
CREATE TABLE {$this->getTable('alekseon_ajaxifiedProductEdit/gridview_attributeColumns')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `grid_view_id` int(10) unsigned,
  `attribute_id` smallint unsigned,
  `editable` bit default 0,
  `position` smallint default 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY(`grid_view_id`) REFERENCES `{$installer->getTable('alekseon_ajaxifiedProductEdit/gridview')}`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY(`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}`(`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
