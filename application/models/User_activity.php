<?php
    Class User_activity extends CI_Model
    {
     function activity()
     {
        if($this->session->userdata('logged_in'))
       {
         $session_data = $this->session->userdata('logged_in');
       //  $data['username'] = $session_data['username'];

           $data = array(
                  'session_id'=>"",
                  'ip_address'=>$session_data['ip_address'],
                  'user_agent'=>$session_data['user_agent'],
                  'username'=>$session_data['username'],
                  'time_stmp'=>Now(),
                  'user_data'=>$session_data['username']."Logged in Account"
                );
        $this->db->insert('user_activity',$data);        
       }
       else
       {
          return  false;
       }

       // Function to get the client ip address
    function get_client_ip() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
       }
      }
    }
    ?>