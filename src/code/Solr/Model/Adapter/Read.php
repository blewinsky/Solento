<?php

class Danslo_Solr_Model_Adapter_Read extends Danslo_Solr_Model_Adapter_Abstract {
    
    public function entityExists($id) {
        /*
         * Filter by entity_id.
         */
        $query = new Solarium_Query_Select();
        $query->addFilterQuery(array(
            'key'   => 'fq_entity_id',
            'tag'   => array('entityId'),
            'query' => sprintf('entity_id:%d', $id)
        ));

        return $this->getClient()->select($query)->getNumFound() != 0;
    }

    public function getDocuments($type, $filters = false, $groupByParent = false) {
        /*
         * Apply filters.
         */
        $query = new Solarium_Query_Select();
        $query->setRows(100);
        if($filters && count($filters)) {
            foreach($filters as $filter) {
                $query->addFilterQuery($filter);
            }
        }

        /*
         * Construct Mage objects.
         */
        $documents = array();
        foreach($this->getClient()->select($query) as $document) {
            $mobj = Mage::getModel($type);
            foreach($document->getFields() as $field => $value) {
                $mobj->setData($field, $value);
            }
            if(!$groupByParent) {
                $documents[$document->entity_id] = $mobj;
            } else {
                $documents[$document->parent_id][] = $mobj;
            }
        }
        return $documents;
    }
    
}

?>
