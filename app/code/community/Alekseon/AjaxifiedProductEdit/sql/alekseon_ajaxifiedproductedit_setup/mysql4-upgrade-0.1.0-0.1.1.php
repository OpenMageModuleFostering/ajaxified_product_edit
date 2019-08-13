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
    ALTER TABLE `{$this->getTable('alekseon_ajaxifiedProductEdit/gridview')}` CHANGE `enable` `enable` tinyint(1) UNSIGNED DEFAULT 0;
    ALTER TABLE `{$this->getTable('alekseon_ajaxifiedProductEdit/gridview_attributeColumns')}` CHANGE `editable` `editable` tinyint(1) UNSIGNED DEFAULT 0;
");
$installer->endSetup();
