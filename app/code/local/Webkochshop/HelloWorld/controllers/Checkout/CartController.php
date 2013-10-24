<?php

/*
 * Da der Autoloader den Klassennamen nicht auf den Dateinamen mappen kann muss
 * die Core Klasse manuell eingebunden werden:
 */
require_once 'Mage/Checkout/controllers/CartController.php';

class Webkochshop_HelloWorld_Checkout_CartController extends Mage_Checkout_CartController
{
	public function indexAction()
	{
		parent::indexAction();
	}
}