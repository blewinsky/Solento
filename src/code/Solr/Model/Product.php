<?php
class Danslo_Solr_Model_Product extends Mage_Catalog_Model_Product {
	
	public function _getResource() {
        if (Mage::helper('catalog/product_flat')->isEnabled()) {
            return Mage::getResourceSingleton('solr/product');
        } else {
        	/** If we get here, we get nesting errors. please fix */
            return parent::getResource();
        }
    }	
		
}
?>
