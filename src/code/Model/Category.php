<?php

class Danslo_Solr_Model_Category extends Mage_Catalog_Model_Category {

    public function getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true) {
        /*
         * TODO: Support recursionLevel / sorting / collection.
         */
        $filter = array(
            'key'   => 'fq_show_categories',
            'tag'   => array('showCategories'),
            'query' => sprintf('include_in_menu:true AND parent_id:%d', $parent));

        return $this->_getResource()->getDocuments('catalog/category', array($filter));
    }
    
    public function _getResource() {
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            return Mage::getResourceSingleton('solr/category');
        } else {
            return parent::getResource();
        }
    }
    
}

?>
