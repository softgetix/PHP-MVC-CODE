<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div id="add-new-material" class="container add-new-form-page">
    <div class="form form-container">
        <div class="form-head">
            <?php $workOrderId =  isset($workOrderId)?$workOrderId:0?>
            
            <span><i class="fa fa-gavel"></i>Add New Material</span>
            <a href="<?php echo base_url('/material/').$workOrderId; ?>" class="cancel-btn"> <span class="btn-txt">Cancel Adding</span> <i class="fa fa-times"></i></a>
        </div>
        <?php
        $attributes_frm = array('class' => 'add_new_material_form material_form', 'id' => 'add_new_material_form');
        echo form_open('material/add', $attributes_frm);
        ?>
         <input type="hidden" name="work_order_id" id="work_order_id" value="<?php echo isset($workOrderId)?$workOrderId:0?>"/>
        <div id="form-page-1" class="form-fields-box">
            <div class="form-group">
                <label>Type</label>
                <select name="material_type">
                   
                    <?php
                    if (isset($type)) {
                        foreach ($type as $value) {
                            ?>
                            <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                            <?php
                        }
                        ?>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Quantity/Length</label>
                <input type="text" name="quantity"> 
            </div>
            <div class="form-group checkbox-field">
                <label>Ready</label>
                <input type="hidden" name="ready" value="0">
                <input type="checkbox" name="ready" value="1">
            </div>
            <div class="form-group">
                <label>Install Date</label>
                <input type="text" name="install_date" id="install_date" class="install_date">
            </div>
            <div class="form-group clearfix">
                <label>Instructions</label>
                <textarea name="instructions" cols="24" rows="5"></textarea>
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
<script type="text/javascript">
    $('#install_date,#edit_material_form .install_date').datepicker();    
</script>
