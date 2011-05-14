<?php
class Danslo_Solr_Model_Resource_Category extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat {
	    
    public function _getSolrWriteAdapter() {
        return Mage::getModel('solr/adapter_write_product');
    }
    
    public function _getSolrReadAdapter() {
        return Mage::getModel('solr/adapter_read_product');
    }
    
}
?>
