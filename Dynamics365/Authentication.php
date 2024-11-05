<?php

namespace ITCDynamics365;

use ITCDynamics365\BaseEntity;

class Authentication extends BaseEntity
{

    const GRANT_TYPES = [
        'password' => 'password',
        'refresh_token' => 'refresh_token'
    ];

    private $clientId;
    private $loginUrl;


    public function __construct() {
        parent::__construct();

        // Default client id from Microsoft Dynamics 365
        $this->clientId = "51f81489-12ee-4a9e-aaae-a2591f45987d";
        $this->loginUrl = 'https://login.microsoftonline.com/common/oauth2/token';
    }

    public function getAccessToken()
    {
        $tokenInfo = get_network_option(null, 'itc_dynamics_365_token_info');

        if (empty($tokenInfo)) {
            return false;
        }
        $tokenInfo = json_decode($tokenInfo, true);

        if ($this->isValidAccessToken($tokenInfo)) {
            return $tokenInfo['access_token'];
        }
        return $this->refreshToken($tokenInfo);

    }

    public function loginToMicrosoft($params = []){
        $defaultParams = [
            'resource' => '',
            'username' => '',
            'password' => '',
        ];
        $params = array_merge($defaultParams, $params);

        $response = [
            'error' => 'invalid_parameters',
            'error_description' => 'Require resource, username and password'
        ];

        if (false === $this->isValidLoginParams($params)) {
            return $response;
        }

        $response = $this->makeLoginRequest([
            'body' => [
                'client_id' => $this->clientId,
                'username' => $params['username'],
                'password' => $params['password'],
                'resource' => $params['resource'],
                'grant_type' => self::GRANT_TYPES['password']
            ]
        ]);

        return $response;

    }


    private function refreshToken(array $tokenInfo)
    {

        $response = false;

        if(!empty($tokenInfo)){
            $response = $this->makeLoginRequest([
                'body' => [
                    'grant_type' => self::GRANT_TYPES['refresh_token'],
                    'client_id' => $this->clientId,
                    'refresh_token' => $tokenInfo['refresh_token'],
                    'scope' => 'user_impersonation'
                ]
            ]);
        }

        //If token was revoked, need to login again by username & password
        $response = $this->maybeLoginAgainByCredential($response);

        if (!empty($response) && !empty($response['access_token'])) {
            update_network_option(null, 'itc_dynamics_365_token_info', json_encode($response));
            return $response['access_token'];
        }
        return false;
    }

    private function maybeLoginAgainByCredential($response){
        $d365Credential = [];
        if (!empty($response) && !empty($response['error']) && $response['error'] == 'invalid_grant') {
            $d365Credential = get_option('itc_dynamics_365_credential');
            $d365Credential = !empty($d365Credential) ? json_decode($d365Credential, true) : [];
        }

        if(!empty($d365Credential)){
            $response = $this->loginToMicrosoft([
                'resource' => $d365Credential['resource'],
                'username' => $d365Credential['username'],
                'password' => $d365Credential['password'],
            ]);
        }


        if (!empty($response["error"])) { //Maybe password was changed, destroy all credentials
            update_network_option(null, 'itc_dynamics_365_token_info', null);
            update_network_option(null, 'itc_dynamics_365_credential', null);
        }



        return $response;
    }

    private function makeLoginRequest(array $bodyParams){
        try{
            $tokenResult = wp_remote_post($this->loginUrl, $bodyParams);
            $tokenResult = json_decode(wp_remote_retrieve_body($tokenResult), true);
        }catch(\Exception $e) {
            $tokenResult = [
                'error' => 'refresh_token_error',
                'error_description' => $e->getMessage()
            ];
        }
        return $tokenResult;

    }

    private function isValidLoginParams(array $params ){
        return !empty($params['resource']) && !empty($params['username']) && !empty($params['password']);
    }

    private function isValidAccessToken(array $tokenInfo)
    {
        $currentTime = time();
        //Should check expires_on to be ealier by 30 seconds in case of delay of process
        return !empty($tokenInfo['access_token']) &&
                !empty($tokenInfo['expires_on']) &&
                ($currentTime <= $tokenInfo['expires_on'] - 30);
    }

}