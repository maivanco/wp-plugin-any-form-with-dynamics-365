<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
    <input type="hidden" name="action" value="itc_update_dynamics_365_options">
    <?php wp_nonce_field('itc_update_dynamics_365_options'); ?>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="username">Resource (CRM Url)</label></th>
                <td>
                    <?= !empty($tokenInfo['resource']) ? $tokenInfo['resource'] : ''?> <strong> (connected) </strong>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="SelectAList">Select a List</label>
                </th>
                <td>
                    <select name="selectedEntity">
                        <option><?= _e('Select an entity', 'itc-dynamics-365') ?></option>
                        <option value="segment_list" <?= $selectedEntity == 'segment_list' ? 'selected="selected"' : null; ?>>Segment List</option>
                        <option value="marketing_list" <?= $selectedEntity == 'marketing_list' ? 'selected="selected"' : null; ?>>Marketing List</option>
                    </select>
                    <br><br>
                    <select id="segment_list" name="selectedSegmentListID" class="entity_list" <?= $selectedEntity == 'segment_list' ? null : 'style="display:none"'?>>
                        <option><?= __('Select a static segment');?></option>
                        <?php
                        if (!empty($segments)) {
                            foreach ($segments as $segmentID => $segmentName) :
                                $selected = $selectedListID == $segmentID ? 'selected="selected"' : '';
                                echo '<option value="'.$segmentID.'" '.$selected.'>'.$segmentName.'</option>';
                            endforeach;
                        }
                        ?>
                    </select>
                    <select id="marketing_list" name="selectedMarketingListID" class="entity_list"  <?= $selectedEntity == 'marketing_list' ? null : 'style="display:none"'?>>
                        <option><?= __('Select a marketing list');?></option>
                        <?php
                        if (!empty($marketingList)) {
                            foreach ($marketingList as $listID => $listName) :
                                $selected = $selectedListID == $listID ? 'selected="selected"' : '';
                                echo '<option value="'.$listID.'" '.$selected.'>'.$listName.'</option>';
                            endforeach;
                        }
                        ?>
                    </select>
                    <p><i>A new subscriber will be added into this list</i></p>
                </td>
            </tr>

        </tbody>
    </table>
    <p class="submit">
        <?php submit_button('Save', 'primary', 'saveDynamics365Options', false);?>
        &nbsp
        <?php submit_button('Disconnect from Dynamics 365', 'button-link-delete', 'disconnectDynamics365', false);?>
    </p>
</form>