<?php

class Danslo_Solr_Model_Adapter_Write extends Danslo_Solr_Model_Adapter_Abstract {

    public function insertMultiple($table, array $data) {
        /*
         * Generate the solr documents.
         */
        $solr_documents = array();
        foreach($data as $document) {
            $solr_document = new Solarium_Document_ReadWrite();
            foreach($document as $field => $value) {
                if(Mage::helper('solr/category')->isDefaultField($field)) {
                    $solr_document->{$field} = $value;
                } else {
                    $solr_document->{$field.'_s'} = $value; /* Custom data. */ 
                }
            }
            $solr_documents[] = $solr_document;
            unset($solr_document);
        }
        
        /*
         * Create the query.
         */
        $query = new Solarium_Query_Update;
        $query->addDocuments($solr_documents);
        $query->addCommit();
        $query->addOptimize();
        
        /*
         * Update into Solr.
         */
        $this->getClient()->update($query);
    }

}

?>
