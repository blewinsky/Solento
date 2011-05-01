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

    public function getChildren($category, $recursive = true, $isActive = true) {
        die($category->getName());
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainStoreTable($category->getStoreId()), 'entity_id')
            ->where('path LIKE ?', "{$category->getPath()}/%");
        if (!$recursive) {
            $select->where('level <= ?', $category->getLevel() + 1);
        }
        if ($isActive) {
            $select->where('is_active = ?', '1');
        }
        $_categories = $this->_getReadAdapter()->fetchAll($select);
        $categoriesIds = array();
        foreach ($_categories as $_category) {
            $categoriesIds[] = $_category['entity_id'];
        }
        return $categoriesIds;
    }
    
    protected function _getSolrUpdateAdapter() {
        return Mage::getModel('solr/adapter_update');
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
                //$this->_getWriteAdapter()->insertMultiple($this->getMainStoreTable($store->getId()), $data);
                $this->_getSolrUpdateAdapter()->insertMultiple($this->getMainStoreTable($store->getId()), $data);
            }
        }
        return $this;
    }
    
}

?>
