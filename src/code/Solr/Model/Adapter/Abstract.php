<?php

/*
 * TODO: At some point we will want to use this as our end point to map model type
 * to a specific solr core.
 */

class Danslo_Solr_Model_Adapter_Abstract {

    protected $_core   = null;
    protected $_client = null;

    protected function getClient() {
        if($this->_client == null) {
            $config = array();
            if($this->_core) {
                $config['core'] = $this->_core;
            }
            $this->_client = new Solarium_Client($config);
        }
        return $this->_client;
    }

}

?>
