<?php

namespace ITCDynamics365\Entities;

use ITCDynamics365\BaseEntity;

class Contact extends BaseEntity
{

    private $contactApiUrl;

    public function create(array $fields){

        $this->contactApiUrl = $this->getAPIUrl().'contacts';

        $response = $this->makeD365Request($this->contactApiUrl,[
            'body' => json_encode($fields),
            'method' => 'POST'
        ]);

        return $this->getContactIDFromResponse($response);

    }

    private function getContactIDFromResponse($response){
        $contactId = false;
        if(!empty($response['response']) && $response['response']['code'] == 204){
            $editContactUrl = wp_remote_retrieve_header($response, 'location');
            $pattern = '/contacts\(([\w-]+)\)/';
            preg_match($pattern, $editContactUrl, $matches);
            $contactId = isset($matches[1]) ? $matches[1] : '';
        }
        return $contactId;
    }


}