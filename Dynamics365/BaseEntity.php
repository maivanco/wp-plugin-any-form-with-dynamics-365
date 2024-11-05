<?php

namespace ITCDynamics365;
use ITCDynamics365\Authentication as Dynamics365Auth;

class BaseEntity {
    const API_VERSION = '9.2';

    private $crmUrl;
    public function __construct()
    {
        $this->crmUrl = $this->getTokenInfo('resource');
    }

    public function getBaseInfo(){
        return [
            'api_url' => $this->getAPIUrl(),
            'api_version' => $this->getAPIVersion()
        ];
    }

    public function getAPIVersion(){
        return self::API_VERSION;
    }
    public function getAPIUrl(){
        return $this->crmUrl.'/api/data/v'.self::API_VERSION.'/';
    }

    protected function makeD365Request(string $url, array $args = []){
        try{
            $args = array_merge($this->getDefaultParams(), $args);
            $args['headers'] = !empty($args['header']) ? $args['headers'] : $this->buildHeaderParams();
            $response =  wp_remote_request($url, $args);
        }catch(\Exception $e) {
            $response = [
                'error' => 'dynamics_365_request_error',
                'error_description' => $e->getMessage()
            ];
        }
        return $response;
    }

    protected function buildHeaderParams(){
        $auth = new Dynamics365Auth();

        $accessToken = $auth->getAccessToken();

        return array(
            'Authorization' =>'Bearer '. $accessToken,
            'Content-Type' => 'application/json; charset=utf-8',
            'OData-MaxVersion' => '4.0',
            'OData-Version' => '4.0',
            'Accept' => 'application/json',
        );
    }

    private function getDefaultParams(){
        return [
            'data_format' => 'body',
            'timeout' => 60,
        ];
    }

    private function getTokenInfo(string $fieldName = ''){
        $tokenInfo = get_network_option(null, 'itc_dynamics_365_token_info');
        if(empty($tokenInfo)){
            return false;
        }

        $tokenInfo = json_decode($tokenInfo, true);
        return !empty($fieldName) ? $tokenInfo[$fieldName] : $tokenInfo;
    }


}