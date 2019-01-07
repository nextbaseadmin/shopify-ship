<?php
class User_model extends CI_Model{ 
	
	 function __construct() {
        parent::__construct();
        $this->load->database();
        
    }
	
	function cek(){
		$username = $this->input->post('username', TRUE);
		$password = md5($this->input->post('password', TRUE));
		
		$query=$this->db->get_where('t_user', array('email' => $username,'password' => $password));

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $data) {
				$data=array('LOGIN'=>TRUE,'ID'=>$data->id,'NAME'=>$data->fullname,'EMAIL'=>$data->email,'STATUS'=>$data->status,'RULE'=>$data->rule);
				$this->session->set_userdata($data);	

				redirect('admin/index');		
			}
		} else {
			redirect('admin/login');
		}			
	}
	

}
?>