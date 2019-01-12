<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="edit-material" class="container edit-form-page">
    <div class="form form-container">
        <div class="form-head">
            <span><i class="fa fa-gavel"></i>Edit Material</span>
            <?php $work_order_id =  (isset($customerData['work_order_id']) ? $customerData['work_order_id'] : '');
            ?>
            <a href="<?php echo site_url("/material/$work_order_id"); ?>" class="cancel-btn"><span class="btn-txt">Cancel Updating</span> <i class="fa fa-times"></i></a>
        </div>
        <?php
        $attributes_frm = array('class' => 'edit_material_form material_form', 'id' => 'edit_material_form');
        echo form_open('material/update', $attributes_frm);
        ?>

        <div id="form-page-1" class="form-fields-box">
            <div class="form-group">
                <label>Type</label>
                <input type="hidden" name="material_id" id="material_id" value="<?php echo (isset($customerData['id']) ? $customerData['id'] : ''); ?>"/>
                <input type="hidden" name="work_order_id" id="work_order_id" value="<?php echo (isset($customerData['work_order_id']) ? $customerData['work_order_id'] : ''); ?>"/>
                <select name="material_type">
                    <?php
                    if (isset($type)) {
                        foreach ($type as $value) {
                            $selected = '';
                            if (isset($customerData['type']) && !empty($customerData['type']) && $customerData['type'] == $value['id']) {

                                $selected = ' selected ';
                            } else {
                                $selected = '';
                            }
                            ?>
                            <option value="<?php echo $value['id']; ?>" <?php echo $selected ?>><?php echo $value['name']; ?></option>
                            <?php
                        }
                        ?>
                    <?php } ?>

                </select>
            </div>
            <div class="form-group">
                <label>Quantity/Length</label>
                <input type="text" name="quantity" value="<?php echo (isset($customerData['quantity'])) ? $customerData['quantity'] : '' ?>"/> 
            </div>
            <div class="form-group checkbox-field">
                <label>Ready</label>
                <input type="hidden" name="ready" value="0">
                <input type="checkbox" name="ready" value="1" <?php echo (isset($customerData['ready']) && $customerData['ready'] == "1" ? ' checked="" ' : '') ?>>
            </div>
            <div class="form-group">
                <label>Install Date</label>
                <input type="text" name="install_date" id="install_date" class="install_date" value="<?php echo (isset($customerData['install_date']) && $customerData['install_date'] > 0000-00-00 ? date('m/d/Y', strtotime($customerData['install_date'])) : '') ?>">
            </div>
            <div class="form-group clearfix">
                <label>Instructions</label>
                <textarea name="instructions" cols="24" rows="5"><?php echo (isset($customerData['instructions']) ? $customerData['instructions'] : '') ?></textarea>
            </div>
            <div class="form-group clearfix">
                <?php
                $submit = array(
                    'id' => 'submit',
                    'class' => 'submit'
                );
                ?>
                <span class="submit-holder">
                    <?php echo form_submit('material_form_submit', 'SUBMIT', $submit); ?>
                </span>
            </div> 

        </div> 
    </div>
</div>