<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style type="text/css">
.update_at{display: none;}
</style>
<div id="materials_list" class="listing-page container-fluid">
    <div class="row">

        <div id="page-header">
            <div class="tab_row container">

                <div id="tab_row-header" class="inline-block">
                    <br>
                    <p id="wrk-mtrl-for">
                        <b>Work material for: </b>
                        <span> <?php echo stripslashes($workOrderName[0]['name']); ?> </span>
                    </p>
                </div>

                <?php $workOrderId = isset($workOrderId) ? $workOrderId : 0 ?>
                     <?php 
                      $customer_id=isset($workOrderName[0]["customer_id"]) && !empty($workOrderName[0]["customer_id"]) ? $workOrderName[0]["customer_id"] : '';
                                        
                         $location_id=isset($workOrderName[0]["location_id"]) && !empty($workOrderName[0]["location_id"]) ? $workOrderName[0]["location_id"] : '' ;?>        
                
                    <?php
                       if(!empty($user_access['material']) && !empty($user_access['material']['access_insert']) && $user_access['material']['access_insert'] == 1){
                        ?>
                <a href="<?php echo base_url('material/add/') . $workOrderId; ?>" class="add_new_btn"><i class="fa fa-plus"></i> <span class="btn-txt"> New Work Material </span> </a>
                <?php 
                    }
                ?>      
        <a href='<?php echo base_url("work-order/customer/$customer_id/$location_id");?>' class="add_new_btn"><i class="fa fa-arrow-left" aria-hidden="true"></i><span class="btn-txt"> Back to WO</span> </a>

            </div> <!-- /.tab_row -->
        </div> <!-- /#page-header -->


        <div id="message-popup" class="container">
            <div class="flash-message col-md-12">
                <?php
                if (($this->session->flashdata('success'))) {
                    ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (($this->session->flashdata('error'))) {
                    ?>
                    <div class="alert alert-warning alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div> <!-- /#message-popup -->


        <div id="main-content" class="container">
          <div class="row">
            <div class="table-container">

                <div class="table-responsive col-xs-12">
                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                        <table id="materials_table" class="table table-bordered table-striped material-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="min-width:130px;">Type</th>
                                    <th>Ready ?</th>
                                    <th>Installed Date</th>
                                    <th>Instructions</th>
                                    <th class="update_at">Update At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="Materials_body">                              
                            </tbody>
                        </table>
                    </div>
                </div> <!-- /.table-responsive -->

            </div> <!-- /.table-container -->
          </div>
        </div> <!-- /#main-content -->


    </div>
    <input type="hidden" name="workOrderId" id="workOrderId" value="<?php echo isset($workOrderId) ? $workOrderId : 0 ?>"/>
</div>


<div id="deleteModal" class="modal fade form-modal" role="dialog">
    <div class="modal-dialog delete-modal">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Material</h4>
            </div>
            <div class="modal-body">
                <p>You are about to delete a Material ?</p>
                <p>Please be aware that this will delete all data associated with this material.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm-btn" data-dismiss="modal" id="Confirm_material_del">Yes, delete this material</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>

            </div>
        </div>

    </div>

</div>

<script>

    $(document).ready(function () {
        var base_url_str = $('.base_url').val();
        var workOrderId = $('#workOrderId').val();

        // Get Category Name
        var cat = $("#materials_table").DataTable({
             "order": [[ 4, "desc" ]],
            "oLanguage": {
                "sProcessing": "<div align='center'><img src='<?php echo base_url(); ?>assets/image/ajax-loader.gif'></div>"},
            //"ordering": false,
            "sAjaxSource": base_url_str + "material/show/" + workOrderId,
            "bProcessing": true,
            "bServerSide": true,
            "aLengthMenu": [[10, 20, -1], [10, 20, "All"]],
            "iDisplayLength": 10,
            "responsive": true,
//        "bSortCellsTop": true,
            "searching": false,
            "bDestroy": true, //!!!--- for remove data table warning.
            "aoColumnDefs": [
                {"sClass": "eamil_conform aligncenter", "aTargets": [0]},
                {"sClass": "eamil_conform aligncenter", "aTargets": [1]},
                {"sClass": "eamil_conform aligncenter", "aTargets": [2]},
                {"sClass": "eamil_conform aligncenter", "aTargets": [3]},
                {"sClass": "eamil_conform aligncenter update_at", "aTargets": [4]},
                {"sClass": "eamil_conform aligncenter", "aTargets": [5], orderable: false}
            ]}
        );
        $(document).on("click", ".deleteMaterial", function () {
            var base_url_str = $(".base_url").val();
            var materialId = $(this).closest('tr').attr("id");
            $('#deleteModal').modal('show');
            $("#Confirm_material_del").one("click", function () {
                $.ajax({
                    url: base_url_str + 'MaterialsController/deleteMaterial/' + materialId,
                    error: function () {
                        $('#info').html('<p>An error has occurred</p>');
                    },
                    dataType: 'JSON',
                    success: function (data) {
                        if (typeof data != 'undefined' && typeof data.success != 'undefined' && data.success != 'undefined' && data.message != 'undefined') {
                            var html = '';
                            var html = '<div class="alert alert-success alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'
                                    + data.message + '</div>';
                            $('.flash-message').html(html);
                            $('#materials_table').DataTable().ajax.reload();
                           
                        }
                    },
                    type: 'GET'
                });
            });
        });
    });

</script>