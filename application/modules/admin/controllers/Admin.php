<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        ob_start();
        $this->load->helper('url');
        $this->load->library('Grocery_CRUD');
        $this->load->model("User_model");
    }

    public function edit_profile()
    {
        redirect('admin/index/edit/'.$this->session->userdata('ID'),'refresh');
    }

    public function login()
    {
        $this->load->view("login");
    }

    public function loginAct()
    {
        $this->User_model->cek();
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect("admin/login");
    }

    public function index()
    {
        if ($this->session->userdata('LOGIN') == 'TRUE') {
            $crud = new Grocery_CRUD();
            $crud->set_table('shipping_rate');
            $crud->columns('from_city', 'to_city', 'standard_price', 'priority_price', 'express_price', 'weight', 'weight_unit', 'shipping_name');
            $crud->edit_fields('from_city', 'to_city','standard_price', 'weight', 'weight_unit', 'shipping_name');
            $crud->unset_add_fields('created_by','date_created','modified_by','date_modified','deleted_by','date_deleted','is_deleted');
            $crud->unset_edit_fields('created_by','date_created','modified_by','date_modified','deleted_by','date_deleted','is_deleted');            
            $crud->unset_back_to_list();
            $output = $crud->render();
            $this->load->view('header');
            $this->load->view('pages.php', $output);
            $this->load->view('footer');
        } else {
            redirect('admin/login');
        }
    }

    public function shipping_rate()
    {
        if ($this->session->userdata('LOGIN') == 'TRUE') {
            $crud = new Grocery_CRUD();
            $crud->set_table('shipping_rate');
            $crud->columns('from_city', 'to_city', 'standard_price', 'priority_price', 'express_price', 'weight', 'weight_unit', 'shipping_name');
            $crud->edit_fields('from_city', 'to_city', 'standard_price', 'priority_price', 'express_price', 'weight', 'weight_unit', 'shipping_name');
            $crud->unset_add_fields('created_by','date_created','modified_by','date_modified','deleted_by','date_deleted','is_deleted');
            $crud->unset_edit_fields('created_by','date_created','modified_by','date_modified','deleted_by','date_deleted','is_deleted');            
            $crud->unset_back_to_list();
            $output = $crud->render();
            $this->load->view('header');
            $this->load->view('pages.php', $output);
            $this->load->view('footer');
        } else {
            redirect('admin/login');
        }
    }
    
    public function shipment()
    {
        if ($this->session->userdata('LOGIN') == 'TRUE') {
            $crud = new Grocery_CRUD();
            $crud->set_table('shipments');
            if ($this->session->userdata('shopify_shop') !== null) {
                $crud->where('shop', $this->session->userdata('shopify_shop'));
            }
            $crud->columns('order_id', 'shipment_number','label', 'created_date');
            $crud->edit_fields('order_id', 'shipment_number','label',  'created_date');
            $crud->callback_column('label',array($this,'_callback_webpage_url'));
            $crud->unset_add_fields('created_date','status');
            $crud->unset_edit_fields('created_date','status');            
            $crud->unset_back_to_list();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            $output = $crud->render();
            $this->load->view('header');
            $this->load->view('pages.php', $output);
            $this->load->view('footer');
        } else {
            redirect('admin/login');
        }
    }

    public function _callback_webpage_url($value, $row)
    {
        return "<a href='".$value."' target='_blank'>$value</a>";
    }
}
/* End of file users.php */
/* Location: ./application/modules/users/controllers/users.php */
