<?php
class ITCDynamics365AdminMenuSettings
{
    public function init(){


        add_action('admin_menu', [$this,'itc_dynamics_365_options_page']);
        add_action('admin_post_itc_connect_dynamics_365', [$this,'itc_connect_dynamics_365']);
        add_action('admin_post_itc_update_dynamics_365_options', [$this,'itc_update_dynamics_365_options']);

    }
    function itc_dynamics_365_options_page() {
        add_menu_page(
            'ITC Dynamics 365', // Page title
            'ITC Dynamics 365', // Menu title
            'manage_options', // Capability required to access the page
            'itc_dynamics_365_options', // Unique slug for the page
            [$this,'itc_dynamics_365_render_options_page'], // Callback function to render the page,
            'dashicons-rest-api'
        );
    }
    function itc_dynamics_365_render_options_page() {

        require_once ITC_D365_DIR .'admin/classes/class-form-render.php';
        $formObj = new ITCDynamics365Configs\FormOptions();

        $tokenInfo  = get_network_option(null, 'itc_dynamics_365_token_info');
        $tokenInfo = !empty($tokenInfo) ? json_decode($tokenInfo, true) : [];



        // Add your HTML and form elements here for the options page
        echo '<div class="wrap">';
            echo '<h1>ITC -  Dynamics 365 Settings </h1>';

            if(!empty($tokenInfo['access_token'])){
                $formObj->renderConnectedD365Form($tokenInfo);
            }else{
                $formObj->renderBasicForm();
            }
        // Render your form here
        echo '</div>';
    }

    function itc_connect_dynamics_365() {

        check_admin_referer( 'itc_connect_dynamics_365' ); // Nonce security check
        if (isset($_POST['connectDynamics365'])) { // Check if the form is submitted

            $resource = isset($_POST['resource']) ? sanitize_text_field($_POST['resource']) : '';
            $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
            $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
            $status = 'login_failed';


            require_once ITC_D365_DIR .'Dynamics365/Authentication.php';

            $auth = new ITCDynamics365\Authentication();
            $loginParams = [
                'resource' => $resource,
                'username' => $username,
                'password' => $password,
            ];
            $response = $auth->loginToMicrosoft($loginParams);



            if (!empty($response['access_token'])) {
                $status = 'login_successful';
                update_network_option(null, 'itc_dynamics_365_credential', json_encode($loginParams));
                update_network_option(null, 'itc_dynamics_365_token_info', json_encode($response));
            }


            $redirectParams = [
                'page' => 'itc_dynamics_365_options',
                'status' => $status
            ];

            if(!empty($response["error"])){
                $redirectParams['error'] = $response["error"];
                $redirectParams['error_description'] = $response["error_description"];
            }
            $redirectUrl = add_query_arg($redirectParams,admin_url( 'admin.php' ));
            wp_safe_redirect($redirectUrl);
            exit;
        }
    }

    function itc_update_dynamics_365_options(){

        check_admin_referer( 'itc_update_dynamics_365_options' ); // Nonce security check

        if (isset($_POST['disconnectDynamics365'])) {
            update_network_option(null, 'itc_dynamics_365_token_info', null);

        }

        if (isset($_POST['saveDynamics365Options'])) {
            $selectedEntity = isset($_POST['selectedEntity']) ? sanitize_text_field($_POST['selectedEntity']) : null;
            $selectedSegmentListID = isset($_POST['selectedSegmentListID']) ?
                                    sanitize_text_field($_POST['selectedSegmentListID']) :
                                    null;
            $selectedMarketingListID = isset($_POST['selectedMarketingListID']) ?
                                        sanitize_text_field($_POST['selectedMarketingListID']) :
                                        null;

            $selectedListID = $selectedEntity == 'segment_list' ? $selectedSegmentListID : $selectedMarketingListID;

            update_option('itc_dynamics_365_selected_entity', $selectedEntity);
            update_option('itc_dynamics_365_list_id', $selectedListID);
        }

        $redirectUrl = add_query_arg(
            array(
                'page' => 'itc_dynamics_365_options',
            ),
            admin_url( 'admin.php' )
        );
        wp_safe_redirect($redirectUrl);
        exit;

    }
}

$dynamics365AdminMenu = new ITCDynamics365AdminMenuSettings();
$dynamics365AdminMenu->init();