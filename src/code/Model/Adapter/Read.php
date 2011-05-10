<?php

class Danslo_Solr_Model_Adapter_Read {
    
    public function entityExists($id) {
        $query = new Solarium_Query_Select();
        $query->addFilterQuery(array(
            'key'   => 'fq_entity_id',
            'tag'   => array('entityId'),
            'query' => sprintf('entity_id:%d', $id)
        ));
        $client = new Solarium_Client();

        return $client->select($query)->getNumFound() != 0;
    }
    
}

?>
