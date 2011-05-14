<?php

class Danslo_Solr_Helper_Category extends Mage_Core_Helper_Abstract {
    
    /* 
     * NOTE: If you modify these values, you need to change schema.xml to reflect
     * those changes.
     */
    private $_default_fields = array(
        'entity_id',
        'parent_id',
        'created_at',
        'updated_at',
        'path',
        'position',
        'level',
        'children_count',
        'store_id',
        'all_children',
        'available_sort_by',
        'children',
        'custom_apply_to_products',
        'custom_design',             
        'custom_design_from',        
        'custom_design_to',          
        'custom_layout_update',      
        'custom_use_parent_settings',
        'default_sort_by',           
        'description',
        'display_mode',              
        'filter_price_range',        
        'image',
        'include_in_menu',
        'is_active',
        'is_anchor',
        'landing_page',              
        'meta_description',
        'meta_keywords',
        'meta_title',
        'name',
        'page_layout',
        'path_in_store',
        'thumbnail',
        'url_key',
        'url_path');
    
    public function isDefaultField($field) {
        return in_array($field, $this->_default_fields);
    }
    
}

?>