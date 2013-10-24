<?php

class MageModule_Customerprice_Block_Wishlist extends Mage_Wishlist_Block_Customer_Wishlist
{
    // only change: displayMinimalPrice = false --> displayMinimalPrice = true
    public function getPriceHtml($product, $displayMinimalPrice = true, $idSuffix = '')
    {
        return parent::getPriceHtml($product, $displayMinimalPrice, $idSuffix);
    }
}
