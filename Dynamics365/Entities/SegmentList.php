<?php

namespace ITCDynamics365\Entities;

use ITCDynamics365\BaseEntity;

class SegmentList extends BaseEntity
{

    const SEGMENT_TYPE_STATIC = '192350001';

    private $apiUrls;
    public function __construct()
    {
        parent::__construct();
        $this->apiUrls = [
            'list' => $this->getAPIUrl().'msdyncrm_segments',
            'addNew' => $this->getAPIUrl().'msdyncrm_SegmentMembersUpdate'
        ];
    }
    /**
     * Because D365 only allows to add data in static segments so we need to make a query string to get only static segments
    */
    public function getList(){
        $apiLinkWithQueryString = add_query_arg( array(
            '$filter' => 'msdyncrm_segmenttype eq '.self::SEGMENT_TYPE_STATIC,
        ), $this->apiUrls['list'] );

        $response = $this->makeD365Request($apiLinkWithQueryString);
        return $this->formatSegmentList($response);
    }
    public function assignContactIDToSegment(string $contactID,string $segmentID){
        $response = $this->makeD365Request($this->apiUrls['addNew'],[
            'body' => $this->buildBodyParams($contactID, $segmentID),
            'method' => 'POST'
        ]);
        return $response['response'];
    }

    private function formatSegmentList($response){
        $responseData = [];
        $formattedOptions = [];
        if (!empty($response['response']) &&
            !empty($response['response']['code']) &&
            $response['response']['code'] == 200 &&
            !empty($response['body']))
        {
            $responseData = json_decode($response['body']);
            $responseData = $responseData->value;

        }

        if (!empty($responseData)) {
            foreach($responseData as $row){
                $formattedOptions[$row->msdyncrm_segmentid] = $row->msdyncrm_segmentname;
            }
        }
        return $formattedOptions;
    }

    private function buildBodyParams(string $contactID, string $segmentID){
        return wp_json_encode([
            'msdyncrm_memberids' => $contactID,
            'msdyncrm_operation' => 'addByIds',
            'msdyncrm_segmentid' => $segmentID,
        ]);
    }



}