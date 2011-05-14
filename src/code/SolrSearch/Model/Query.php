<?php 

class Danslo_SolrSearch_Model_Query extends Mage_CatalogSearch_Model_Query
{
	/**
     * Load Query object by query string
     *
     * @param string $text
     * @return Danslo_SolrSearch_Model_Query
     */
    public function loadByQuery($text)
    {
    	$results = $this->_getResource()->loadByQuery($text);
    	$this->setQueryText($text);
    	$this->setData($results);
    	
    	return $this;
    }
    
    public function _getResource() {
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
        	
        	// Temporary to test
        	return Mage::getResourceSingleton('catalogsearch/query');
            //return Mage::getResourceSingleton('solr/product');
        } else {
        	/** If we get here, we get nesting errors. please fix */
            return parent::getResource();
        }
    }
}