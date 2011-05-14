<?php

/* 
 * TODO: CREATE DOC
 */
class Danslo_SolrSearch_Resource_Query extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat {
    
	protected $_results;
    
    public function _getSolrWriteAdapter() {
        return Mage::getModel('solr/adapter_write');
    }
    
    public function _getSolrReadAdapter() {
        return Mage::getModel('solr/adapter_read');
    }
    
    public function loadByQuery($text)
    {
    	$oQuery = new Solarium_Query_Select_FilterQuery();
    	$oQuery->setKey('fq_show_categories')
    		   ->addTag('showCategories')
    		   ->setQuery($text);
    		   
        $this->_results = $this->_getSolrReadAdapter()->getDocuments('catalog/category', array($oQuery), true);

        return $this->_results;
    }
    
  	public function checkId($id) {
        return $this->_getSolrReadAdapter()->entityExists($id);
    }
}