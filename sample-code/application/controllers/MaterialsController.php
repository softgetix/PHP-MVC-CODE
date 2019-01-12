<?php defined('BASEPATH') or exit('No direct script access allowed');

class MaterialsController extends MY_Controller {
    /* Action : index
     * Purpose : List the work order  	  
     * Created By : RG
     */

    public function index($workOrderId = 0) {
          if ($this->require_role('admin,employee,installer')){
        if (isset($workOrderId) && $workOrderId > 0) {
            $this->is_logged_in();
          $data['user_access'] = $this->getUserAccess();
         $this->load->view('common/page_header', $data);
            $data['workOrderId'] = $workOrderId;
            $data['workOrderName'] = $this->App_model->getData('work_orders', array('name','customer_id','location_id'), array('id' => $workOrderId));
          $this->load->view('templates/materials/index', $data);
          $this->load->view('common/page_footer');
        }else {
            redirect('work-order/search');
        }
    }
  }  

    /* Action : showAjax
     * Purpose : List the material work order  	  
     * Created By : RG
     */

    public function showAjax($workOrderId = 0) {
        $sLimit = "";
        $lenght = 10;
        $str_point = 0;

        $col_sort = array("material_types.name", "ready", "install_date", "instructions","materials.updated_at");

        $select = array("materials.id", "material_types.name", "ready", "install_date", "instructions","materials.updated_at");

        $order_by = "materials.updated_at";

        $temp = 'desc';
        if (isset($_GET['iSortCol_0'])) {
            $index = $_GET['iSortCol_0'];
            //echo $index;
            $temp = $_GET['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
            //echo $order_by;
        }
        $this->db->select($select);
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $words = $_GET['sSearch'];
            for ($i = 0; $i < count($col_sort); $i++) {
                if ($col_sort[$i] != 'usercount')
                    $this->db->or_like($col_sort[$i], $words, "both");
            }
        }
        $this->db->join('material_types', 'material_types.id = materials.type', 'left');
        $this->db->where('work_order_id', $workOrderId);
        $this->db->order_by($order_by, $temp);
        $this->db->group_by('materials.id');
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $str_point = intval($_GET['iDisplayStart']);
            $lenght = intval($_GET['iDisplayLength']);
            $records = $this->db->from('materials')
                    ->limit($lenght, $str_point)
                    ->get();
        } else {
            $records = $this->db->from('materials')
                    ->get();
        }

        $total_record = $this->db->select('*')
                ->from('materials')
                ->where('work_order_id', $workOrderId)
                ->count_all_results();
        $output = array(
            "sEcho" => 0,
            "iTotalRecords" => $total_record,
            "iTotalDisplayRecords" => $total_record,
            "aaData" => array()
        );

        $result = $records->result_array();
        $i = 0;
        $final = array();
        $delete = '';
        $user_access = $this->getUserAccess();
        foreach ($result as $val) {
        if ($this->require_role('admin,employee,installer'))
            $delete = '<a href="javascript:void(0)" class="deleteLocation"  data-toggle="modal" data-target="#deleteModel" title="Delete Location"><img  src="' . base_url('/assets/image/delete.png') . '" class="location-img" /></a>';

     $edit=!empty($user_access["material"]) && !empty($user_access["material"]["access_update"]) && $user_access["material"]["access_update"] == 1 ? '<a id="editMaterial" class="editMaterial" href="' . base_url('material/edit/') . $val['id'] . '" title="Edit & View"><i class="fa fa-edit"></i></a>':'';

     $delete=!empty($user_access["material"]) && !empty($user_access["material"]["access_delete"]) && $user_access["material"]["access_delete"] == 1 ?'<a href="javascript:void(0)" class="deleteMaterial" data-toggle="modal" data-target="#deleteModel" title="Delete Material"><img  src="' . base_url('/assets/image/delete.png') . '" class="location-img" /></a>':'';

        $output['aaData'][] = array("DT_RowId" => $val['id'], $val['name'], (isset($val['ready']) && $val['ready']) == 1 ? 'Yes' : 'No', (isset($val['install_date']) && !empty($val['install_date']) && $val['install_date'] > 0000-00-00 ) ? date('m/d/Y', strtotime($val['install_date'])) : '', $val['instructions'],$val['updated_at'], $edit.$delete);
    }
    echo json_encode($output);
    die;
}

    /* Action : createMeterialOrder
     * Purpose : Create the material work order  	  
     * Created By : RG
     */
    public function createMeterialOrder($workOrderId = 0) {
        if ($this->require_role('admin,employee')){
        $data['user_access'] = $this->getUserAccess();
        $this->load->view('common/page_header',$data);    
        /* get the list of stage */
        $data['type'] = $this->App_model->getData('material_types', array('id', 'name'),false,false,'name','asc');
        $data['workOrderId'] = $workOrderId;
        $this->load->view('templates/materials/add', $data);
        $this->load->view('common/page_footer');

        if ($this->input->post()) {

            $workOrderData = $this->input->post();

            $meteiralArray = array(
                'install_date' => (isset($workOrderData['install_date']) && !empty($workOrderData['install_date'])) ? date('Y-m-d', strtotime($workOrderData['install_date'])) : '',
                'type' => $workOrderData['material_type'],
                'ready' => $workOrderData['ready'],
                'quantity' => $workOrderData['quantity'],
                'instructions' => $workOrderData['instructions'],
                'work_order_id' => $workOrderData['work_order_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );
            /* insert data into work order table */
            $workOrder = $this->App_model->rowInsert('materials', $meteiralArray);
            $this->App_model->rowUpdate('work_orders', array('updated_at'=> date("Y-m-d H:i:s")),array("id" => $workOrderData['work_order_id']));
           if ($workOrder > 0) {
                /* log this */
                $this->logThis('Add', 'materials', 'Create Material Order');
                $this->session->set_flashdata('success', 'Material Added Successfully');
                $workOrderId = $workOrderData['work_order_id'];
                redirect("material/$workOrderId");    
            } 
        }

   }
}
    /* Action : editWorkOrder
     * Purpose : Edit the material work order  	  
     * Created By : RG
     */

    public function editMeterialOrder($id = 0) {

        if ($this->require_role('admin,employee,installer')){
            $data['user_access'] = $this->getUserAccess();
            $this->load->view('common/page_header',$data);
        
        /* get the list of stage */
        $data['type'] = $this->App_model->getData('material_types', array('id', 'name'),false,false,'name','asc');
        $material = $this->App_model->getData('materials', array('*'), array('id' => $id));
        $data['customerData'] = (isset($material) && !empty($material)) ? $material[0] : array();
        $this->load->view('templates/materials/edit', $data);
        $this->load->view('common/page_footer');
    }
}

    /* Action : UpdateWorkOrder
     * Purpose : Update the work order  	  
     * Created By : RG
     */

    public function UpdateMeterialOrder() {
//        die('fdfd');
        if ($this->input->post()) {

            $workOrderData = $this->input->post();
//            print_r($workOrderData);die('fdf');
            $customerDataArray = array(
                'install_date' => (isset($workOrderData['install_date']) && !empty($workOrderData['install_date'])) ? date('Y-m-d', strtotime($workOrderData['install_date'])) : '',
                'type' => $workOrderData['material_type'],
                'ready' => $workOrderData['ready'],
                'quantity' => $workOrderData['quantity'],
                'instructions' => $workOrderData['instructions'],
                'updated_at' => date('Y-m-d H:i:s')
            );

            if (isset($workOrderData['work_order_id']) && !empty($workOrderData['work_order_id']) && isset($workOrderData['material_id']) && !empty($workOrderData['material_id'])) {
                $where = array('work_order_id' => $workOrderData['work_order_id'], 'id' => $workOrderData['material_id']);

                $this->App_model->rowUpdate('materials', $customerDataArray, $where);
                $this->App_model->rowUpdate('work_orders', array('updated_at'=> date("Y-m-d H:i:s")),array("id" => $workOrderData['work_order_id']));
            }
            if (isset($workOrderData['work_order_id']) && $workOrderData['work_order_id'] > 0) {
                 /* log this */
                $this->logThis('Update', 'materials', 'Update Material Order',$where);
                $this->session->set_flashdata('success', 'Material Update Successfully');
                $workOrderId = $workOrderData['work_order_id'];
                redirect("material/$workOrderId");
            } else {
                redirect('work-order/search');
            }
        } else {
            redirect('work-order/search');
        }
    }

     /* Action : deleteAjax
     * Purpose : Delete the material      
     * Created By : Vivek
     */
     public function deleteMaterial($materialId = 0) {
        $deleteData = $this->App_model->rowsDelete('materials', array('id' => $materialId));
         /* log this */
        $this->logThis('Delete', 'materials', 'Delete Material Order',array('id' => $materialId));        
        $output['success'] = $deleteData;
        $output['message'] = 'Material Deleted Successfully';
        echo json_encode($output);
    }

}
