<?php

/* 
 * We emulate flat indexer because it is very similar to how Solr data is stored.
 * All we need to do is override rebuild() method and communicate with Solr instead of 
 * internal mysql4 write adapter.
 * 
 * NOTE: At this point I have chosen not to completely override the writeAdapter because
 * we don't want to set up a whole Varien_Db object and we only want to rewrite a couple
 * of methods.
 */
class Danslo_Solr_Model_Resource_Category extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat {
    
    public function _getSolrWriteAdapter() {
        return Mage::getModel('solr/adapter_write');
    }
    
    public function _getSolrReadAdapter() {
        return Mage::getModel('solr/adapter_read');
    }
    
    public function checkId($id) {
        return $this->_getSolrReadAdapter()->entityExists($id);
    }
    
    public function rebuild($stores = null) {
        if ($stores === null) {
            $stores = Mage::app()->getStores();
        }

        if (!is_array($stores)) {
            $stores = array($stores);
        }

        $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $categories = array();
        $categoriesIds = array();
        /* @var $store Mage_Core_Model_Store */
        foreach ($stores as $store) {
            $this->_createTable($store->getId());

            if (!isset($categories[$store->getRootCategoryId()])) {
                $select = $this->_getWriteAdapter()->select()
                    ->from($this->getTable('catalog/category'))
                    ->where('path = ?', (string)$rootId)
                    ->orWhere('path = ?', "{$rootId}/{$store->getRootCategoryId()}")
                    ->orWhere('path LIKE ?', "{$rootId}/{$store->getRootCategoryId()}/%");
                $categories[$store->getRootCategoryId()] = $this->_getWriteAdapter()->fetchAll($select);
                $categoriesIds[$store->getRootCategoryId()] = array();
                foreach ($categories[$store->getRootCategoryId()] as $category) {
                    $categoriesIds[$store->getRootCategoryId()][] = $category['entity_id'];
                }
            }
            $categoriesIdsChunks = array_chunk($categoriesIds[$store->getRootCategoryId()], 500);
            foreach ($categoriesIdsChunks as $categoriesIdsChunk) {
                $attributesData = $this->_getAttributeValues($categoriesIdsChunk, $store->getId());
                $data = array();
                foreach ($categories[$store->getRootCategoryId()] as $category) {
                    if (!isset($attributesData[$category['entity_id']])) {
                        continue;
                    }
                    $category['store_id'] = $store->getId();
                    $data[] = $this->_prepareValuesToInsert(
                        array_merge($category, $attributesData[$category['entity_id']])
                    );
                }
                $this->_getSolrWriteAdapter()->insertMultiple($this->getMainStoreTable($store->getId()), $data);
            }
        }
        return $this;
    }
    
}

?>
