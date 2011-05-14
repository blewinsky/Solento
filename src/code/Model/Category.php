<?php

class Danslo_Solr_Model_Category extends Mage_Catalog_Model_Category {

    protected $_categories = null;

    protected function _generateChildren($category) {
        if(isset($this->_categories[$category->getEntityId()])) {
            $category->setChildrenNodes($this->_categories[$category->getEntityId()]);
            foreach($this->_categories[$category->getEntityId()] as $childCategory) {
                $this->_generateChildren($childCategory);
            }
        }
    }

    public function getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true) {
        /*
         * TODO: Support recursionLevel / sorting / collection.
         */
        $filter = array(
            'key'   => 'fq_show_categories',
            'tag'   => array('showCategories'),
            'query' => 'include_in_menu:true');

        $this->_categories = $this->_getResource()->_getSolrReadAdapter()->getDocuments('catalog/category', array($filter), true);
        foreach($this->_categories[$parent] as $rootCategory) {
            $this->_generateChildren($rootCategory);
        }

        return $this->_categories[$parent];
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
