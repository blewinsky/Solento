<?php

class Danslo_Solr_Model_Category extends Mage_Catalog_Model_Category {

    public function getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true) {
        /*
         * TODO: Support recursionLevel / sorting / collection.
         */
        $query = new Solarium_Query_Select();
        $query->addFilterQuery(array(
            'key'   => 'fq_show_categories',
            'tag'   => array('showCategories'),
            'query' => sprintf('include_in_menu:true AND parent_id:%d', $parent)
        ));

        $client = new Solarium_Client();
        $result = $client->select($query);
        
        $categories = array();
        foreach($result as $document) {
            $category = Mage::getModel('catalog/category');
            foreach($document->getFields() as $field => $value) {
                $category->setData($field, $value);
            }
            $categories[] = $category;
        }

        return $categories;
    }
    
    public function getResource() {
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            return Mage::getResourceSingleton('solr/category');
        } else {
            return parent::getResource();
        }
    }
    
}

?>
