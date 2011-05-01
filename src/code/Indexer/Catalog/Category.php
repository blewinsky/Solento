<?php

class Danslo_Solr_Indexer_Catalog_Category extends Mage_Catalog_Model_Category_Indexer_Flat {
	
    public function getName() {
        return Mage::helper('solr')->__('Solr Category Data');
    }

    public function getDescription() {
        return Mage::helper('solr')->__('Category Data stored in Solr');
    }
    
    protected function _getIndexer() {
        return Mage::getResourceSingleton('solr/category');
    }
    
}

?>