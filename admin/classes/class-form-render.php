<?php
namespace ITCDynamics365Configs;
use ITCDynamics365\Entities\SegmentList;
use ITCDynamics365\Entities\MarketingList;

class FormOptions
{
    public function __construct(){

    }

    public function init(){

    }

    public function renderBasicForm(){

        if (isset($_GET['status']) && $_GET['status'] == 'login_failed') {
            $this->renderAdminNotice($_GET['status']);
        }

        itc_render_d365_template_part('admin/partials/dynamics-365-form-options');
    }

    public function renderConnectedD365Form($tokenInfo){
        $segmentList = new SegmentList();
        $marketingList = new MarketingList();

        $selectedEntity = get_option('itc_dynamics_365_selected_entity');
        $selectedListID = get_option('itc_dynamics_365_list_id');

        itc_render_d365_template_part('admin/partials/dynamics-365-form-connected', [
            'segments' => $segmentList->getList(),
            'marketingList' => $marketingList->getList(),
            'tokenInfo' => $tokenInfo,
            'selectedEntity' => $selectedEntity,
            'selectedListID' => $selectedListID
        ]);
    }


    public function renderAdminNotice($type = 'login_failed'){
        echo '<div class="notice notice-error">';
            echo '<p>'.esc_html_e( 'The given information seems incorrect, please try again?', 'itc-dynamics365').'</p>';
            if(isset($_GET['error'])){
                echo '<p>Error code: '.$_GET['error'].' </p>';
            }
            if(isset($_GET['error_description'])){
                echo '<p>Error message: '.$_GET['error_description'].' </p>';
            }
        echo '</div>';
    }
}