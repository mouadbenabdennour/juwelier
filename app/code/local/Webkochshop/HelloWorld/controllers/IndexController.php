<?php

/*
 *  Neuen Controller vom standard Frontend-Controller ableiten:
 */
class Webkochshop_HelloWorld_IndexController extends Mage_Core_Controller_Front_Action
{
	function indexAction()
	{
		$currentTime = date("H:i", time());

		/*
		 * Laden des Layout XML
		 */
		$this->loadLayout();

		/*
		 *  Layout-Block des Layouts laden und Variable $currentTime übergeben:
		 */
		$this->getLayout()->getBlock('helloworld')->assign('currentTime', $currentTime);

		$this->renderLayout();
	}
}