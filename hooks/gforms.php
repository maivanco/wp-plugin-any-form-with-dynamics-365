<?php

namespace ITCDynamics365;

use ITCDynamics365\Entities\Contact as D365Contact;
use ITCDynamics365\Entities\SegmentList as D365SegmentList;
use ITCDynamics365\Entities\MarketingList as D365MarketingList;

class GravityFormHooks
{
    public function init()
    {
        add_action( 'gform_after_submission', [$this,'addNewSubscriberToSegmentList'], 10, 2 );
    }
    /**
     * There are 2 steps to add new subscriber into Dynamics 365 Segment
     * Step 1: Add new subscriber to Contact Entity => return contact ID
     * Step 2: Assign that contact ID to Segment List (Segment ID is required)
     */
    public function addNewSubscriberToSegmentList($entry = [], $form = [])
    {

        $dynamics365Fields = [];
        $contactID = null;
        $isUserConsented = true; //Default for subscribe form

        foreach ( $form['fields'] as $field ) {
            $currentD365Field = $this->getD365field($field['cssClass']);
            if (!empty($currentD365Field)) {
                $dynamics365Fields[$currentD365Field] = $entry[$field['id']];
            }
            if ($field['type'] == 'consent' && empty($entry[$field['id'].'.1']) ){
                $isUserConsented = false;
            }
        }

        if($isUserConsented === false){
            return false;
        }

        try{
            $contactID = null;
            if( !empty($dynamics365Fields) ){
                $contactObj = new D365Contact();
                $contactID = $contactObj->create($dynamics365Fields);
            }

            if (!$contactID) {
                return false;
            }

            $selectedEntity = get_option('itc_dynamics_365_selected_entity');
            $selectedListID = get_option('itc_dynamics_365_list_id');

            switch($selectedEntity) {
                case 'segment_list':
                    $segmentObj = new D365SegmentList();
                    $segmentObj->assignContactIDToSegment($contactID, $selectedListID);
                    break;
                case 'marketing_list':
                    $marketingObj = new D365MarketingList();
                    $marketingObj->assignContactIDToMarketingList($contactID, $selectedListID);
                    break;
                default:

            }

        }catch(\Exception $e) {
            error_log('D365 API error: '.$e->getMessage());
        }

    }

    private function getD365field(string $cssClasses = ''){
        $pattern = '/map-d365-field-(\w+)/';
        preg_match($pattern, $cssClasses, $matches);
        if(!empty($matches) && !empty($matches[1])){
            return $matches[1];
        }else{
            return '';
        }
    }

}

$gformHooks = new GravityFormHooks();
$gformHooks->init();

