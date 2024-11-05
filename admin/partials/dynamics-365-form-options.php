<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
    <input type="hidden" name="action" value="itc_connect_dynamics_365">
    <?php wp_nonce_field('itc_connect_dynamics_365'); ?>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="username">Resource (CRM Url)</label></th>
                <td>
                    <input name="resource" type="text" id="resource" class="regular-text" value="">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="username">Username</label></th>
                <td>
                    <input name="username" type="text" id="username" class="regular-text" value="">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="admin_email">Password</label></th>
                <td>
                    <input name="password" type="password" id="password" class="regular-text" value="">
                </td>
            </tr>
        </tbody>
    </table>
    <?php submit_button('Connect to Dynamics 365', 'primary', 'connectDynamics365');?>
</form>