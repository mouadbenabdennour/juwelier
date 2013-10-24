<?php

/*
 * Das Script wird von der in der config.xml angegebenen Setup Klasse ausgeführt
 *
 * @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup
 */
$installer = $this;

/*
 * startSetup() schaltet einige automatische Prüfungen in MySQL ab, um Fehler
 * während Datenbank-Änderungen zu vermeiden.
 */
$installer->startSetup();

/*
 * Hinzufügen des neuen Attributes
 */
$this->addAttribute('catalog_category', 'color', array(
	'label' => 'Hintergrundfarbe',
	'type' => 'varchar',
	'required' => 0
));

/*
 * endSetup() schaltet die Prüfungen wieder ein
 */
$installer->endSetup();