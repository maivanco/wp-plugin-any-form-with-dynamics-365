<?php

namespace ITCDynamics365\Entities;

use ITCDynamics365\BaseEntity;

class MarketingList extends BaseEntity
{

    private $apiUrls;
    public function __construct()
    {
        parent::__construct();
        $this->apiUrls = [
            'list' => $this->getAPIUrl().'lists',
            'addNew' => $this->getAPIUrl().'AddListMembersList'
        ];
    }

    public function getList(){
        $response = $this->makeD365Request($this->apiUrls['list']);
        return $this->formatmarketingListOptions($response);
    }
    public function assignContactIDToMarketingList(string $contactID,string $listID){
        $response = $this->makeD365Request($this->apiUrls['addNew'],[
            'body' => $this->buildBodyParams($contactID, $listID),
            'method' => 'POST'
        ]);
    }

    private function formatmarketingListOptions($response){
        $marketinglistItems = [];
        $formattedOptions = [];
        if (!empty($response['response']) &&
            !empty($response['response']['code']) &&
            $response['response']['code'] == 200 &&
            !empty($response['body']))
        {
            $marketinglistItems = json_decode($response['body']);
            $marketinglistItems = $marketinglistItems->value;

        }

        if (!empty($marketinglistItems)) {
            foreach($marketinglistItems as $row){
                $formattedOptions[$row->listid] = $row->listname;
            }
        }
        return $formattedOptions;
    }

    private function buildBodyParams(string $contactID, string $listID){
        return wp_json_encode([
            'List' => [
                '@odata.type' => 'Microsoft.Dynamics.CRM.list',
                'listid' => $listID,
            ],
            'Members' => [
                [
                    '@odata.type' => 'Microsoft.Dynamics.CRM.contact',
                    'contactid' => $contactID
                ]
            ]
        ]);
    }



}