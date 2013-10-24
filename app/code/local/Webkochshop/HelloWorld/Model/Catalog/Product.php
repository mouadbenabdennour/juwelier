<?php

/*
 * Die neue Klasse wird vom Core-Model abgeleitet
 */
class Webkochshop_HelloWorld_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
	public function getUppercaseName()
	{
		/*
		 * Dank der Vererbung steht die Methode getName() des Core-Models zur Verfügung.
		 */
		$name = $this->getName();
		return strtoupper($name);
	}

	/*
	 * Die Core-Methode getPrice() wird überschrieben
	 */
	
}