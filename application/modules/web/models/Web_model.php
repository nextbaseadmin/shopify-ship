<?php
class Web_model extends CI_Model { 
	
	function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* create shipment */
    public function get_create_shipment($args) {
        $args                   = array_merge(array(
            'limit'             => 10,
            'offset'            => 0,
            'search'            => '',
            'shop'              => ''
        ), $args);
        
        $query                  = " SELECT
                                        o.*
                                    FROM
                                        orders o 
                                    LEFT JOIN shipments s ON s.`order_id` = o.`order_id` 
                                    WHERE s.`shipment_number` IS NULL 
                                    AND o.`country` != '' 
                                    AND o.`address1` != '' 
                                ";

        if(!empty($args['shop'])) {
            $query              .= " AND o.shop = '".$args['shop']."' ";
        }
        
        if (!empty($args['search'])) {
            $query              .= " AND ( ";
            $query              .= " lower(o.order_id) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(o.name) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(o.email) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(o.phone) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(o.address1) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(o.address2) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(o.postcode) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " ) ";
        }
        
        $total_data             = $this->db->query($query)->num_rows();
        
        if(!empty($args['order_by']) && !empty($args['sort'])) {
            $query              .= "
                                    ORDER BY " . $args['order_by'] . " " . $args['sort'] . " 
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        } else {
            $query                  .= "
                                    ORDER BY id DESC
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        }
        
        $datas                  = $this->db->query($query)->result();
        
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    /* orders */
    public function add_orders($data) {
        if (!$this->db->insert('orders', $data)) {

            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;

        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();

            return (object) $result;
        }
    }

    public function get_order_by_id($order_id) {
        $this->db->select("*");
        $this->db->from('orders');
        $this->db->where("order_id", $order_id);

        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_order_by_name($name) {
        $this->db->select("*");
        $this->db->from('orders');
        $this->db->where("shop", $name);

        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_orders($args) {
        $args                   = array_merge(array(
            'limit'             => 10,
            'offset'            => 0,
            'search'            => '',
            'shop'				=> ''
        ), $args);
        
        $query                  = " SELECT
                                        *
                                    FROM
                                        orders c  
                                    WHERE 1 = 1 
                                ";

        if(!empty($args['shop'])) {
        	$query              .= " AND c.shop = '".$args['shop']."' ";
        }
        
        if (!empty($args['search'])) {
            $query              .= " AND ( ";
            $query              .= " lower(c.order_id) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.name) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.email) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.phone) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.address1) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.address2) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.postcode) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " ) ";
        }
        
        $total_data             = $this->db->query($query)->num_rows();
        
        if(!empty($args['order_by']) && !empty($args['sort'])) {
            $query              .= "
                                    ORDER BY " . $args['order_by'] . " " . $args['sort'] . " 
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        } else {
            $query                  .= "
                                    ORDER BY id DESC
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        }

        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function gets_orders() {
        $query                  = " SELECT * 
                                    FROM orders  
                                    ";

        $total_data             = $this->db->query($query)->num_rows();
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    /* product_order */
    public function add_product_order($data) {
        if (!$this->db->insert('product_order', $data)) {

            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;

        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function get_product_order_by_product_id($product_id) {
        $this->db->select("*");
        $this->db->from('product_order');
        $this->db->where("product_id", $product_id);

        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_product_order_by_order_id($order_id) {
        $this->db->select("*");
        $this->db->from('product_order');
        $this->db->where("order_id", $order_id);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_product_order_by_shop($shop) {
        $this->db->select("*");
        $this->db->from('product_order');
        $this->db->where("shop", $shop);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function gets_product_order() {
        $query                  = " SELECT * 
                                    FROM product_order  
                                    ";

        $total_data             = $this->db->query($query)->num_rows();
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function update_product_order($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('product_order', $data);
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['data']         = $id;
        return (object) $result;
    }

    /* shipping_address_order */
    public function add_shipping_address_order($data) {
        
        if (!$this->db->insert('shipping_address_order', $data)) {
            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;

        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function get_shipping_address_order_by_order_id($order_id) {
        $this->db->select("*");
        $this->db->from('shipping_address_order');
        $this->db->where("order_id", $order_id);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function update_shipping_address_order($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('shipping_address_order', $data);
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['data']         = $id;
        return (object) $result;
    }
	
    /* shipments */
    public function add_shipments($data) {
        
        if (!$this->db->insert('shipments', $data)) {
            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;
        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function get_shipments_by_id($order_id) {
        $this->db->select("*");
        $this->db->from('shipments');
        $this->db->where("order_id", $order_id);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_shipments($args) {
        $args                   = array_merge(array(
            'limit'             => 10,
            'offset'            => 0,
            'search'            => '',
            'shop'              => ''
        ), $args);
        
        $query                  = " SELECT
                                        *
                                    FROM
                                        shipments c  
                                    WHERE 1 = 1 
                                ";

        if(!empty($args['shop'])) {
            $query              .= " AND c.shop = '".$args['shop']."' ";
        }

        if(!empty($args['start_date']) && !empty($args['end_date'])) {
            $query              .= " AND c.created_date >= '".$args['start_date']." 00:00:00' AND c.created_date <= '".$args['end_date']." 23:59:59' ";
        }

        if(!empty($args['start_date']) && empty($args['end_date'])) {
            $query              .= " AND c.created_date >= '".$args['start_date']." 00:00:00' ";
        }

        if(empty($args['start_date']) && !empty($args['end_date'])) {
            $query              .= " AND c.created_date <= '".$args['end_date']." 23:59:59' ";
        }
        
        if (!empty($args['search'])) {
            $query              .= " AND ( ";
            $query              .= " lower(c.order_id) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.shipment_number) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.label) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.status) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " ) ";
        }
        
        $total_data             = $this->db->query($query)->num_rows();
        
        if(!empty($args['order_by']) && !empty($args['sort'])) {
            $query              .= "
                                    ORDER BY " . $args['order_by'] . " " . $args['sort'] . " 
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        } else {
            $query                  .= "
                                    ORDER BY id DESC
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        }
        
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function gets_shipments() {
        $query                  = " SELECT * 
                                    FROM shipments  
                                    ";

        $total_data             = $this->db->query($query)->num_rows();
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function update_shipments($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('shipments', $data);
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['data']         = $id;
        return (object) $result;
    }

    /* shipping_rate */
    public function add_shipping_rate($data) {
        
        if (!$this->db->insert('shipping_rate', $data)) {
            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;
        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function get_shipping_rate() {
        $this->db->select("*");
        $this->db->from('shipping_rate');
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_shipping_rates($args) {
        $args                   = array_merge(array(
            'limit'             => 10,
            'offset'            => 0,
            'search'            => '',
            'shop'              => ''
        ), $args);
        
        $query                  = " SELECT
                                        *
                                    FROM
                                        shipping_rate c  
                                    WHERE 1 = 1 
                                ";
        
        if (!empty($args['search'])) {
            $query              .= " AND ( ";
            $query              .= " lower(c.from_city) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.to_city) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.standard_price) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.priority_price) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.express_price) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.weight) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.weight_unit) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.shipping_name) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " ) ";
        }
        
        $total_data             = $this->db->query($query)->num_rows();
        
        if(!empty($args['order_by']) && !empty($args['sort'])) {
            $query              .= "
                                    ORDER BY " . $args['order_by'] . " " . $args['sort'] . " 
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        } else {            
            $query                  .= "
                                    ORDER BY id DESC
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        }
        
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function gets_shipping_rate() {
        $query                  = " SELECT * 
                                    FROM shipping_rate  
                                    ";

        $total_data             = $this->db->query($query)->num_rows();
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function update_shipping_rate($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('shipping_rate', $data);
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['data']         = $id;
        return (object) $result;
    }   

    /* stores */
    public function add_stores($data) {
        
        if (!$this->db->insert('stores', $data)) {
            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;
        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function get_stores_by_id($order_id) {
        $this->db->select("*");
        $this->db->from('stores');
        $this->db->where("order_id", $order_id);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_stores_by_name($name) {
        $this->db->select("*");
        $this->db->from('stores');
        $this->db->where("shop", $name);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function gets_stores() {
        $query                  = " SELECT * 
                                    FROM stores  
                                    ";

        $total_data             = $this->db->query($query)->num_rows();
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function update_stores($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('stores', $data);
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['data']         = $id;
        return (object) $result;
    }   

    /* stores_address */
    public function add_stores_address($data) {
        
        if (!$this->db->insert('stores_address', $data)) {
            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;
        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function get_stores_address_by_id($id) {
        $this->db->select("*");
        $this->db->from('stores_address');
        $this->db->where("id", $id);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function get_stores_address_by_name($name) {
        $this->db->select("*");
        $this->db->from('stores_address');
        $this->db->where("shop", $name);
        $query                  = $this->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function gets_stores_address() {
        $query                  = " SELECT * 
                                    FROM stores_address  
                                    ";

        $total_data             = $this->db->query($query)->num_rows();
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function update_stores_address($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('stores_address', $data);
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['data']         = $id;
        return (object) $result;
    }   

    /* transactions */
    public function add_transactions($data) {
        
        if (!$this->db->insert('transactions', $data)) {
            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;
        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function get_transactions($args) {
        $args                   = array_merge(array(
            'limit'             => 10,
            'offset'            => 0,
            'search'            => '',
            'shop'              => ''
        ), $args);
        
        $query                  = " SELECT
                                        *
                                    FROM
                                        transactions c  
                                    WHERE 1 = 1 
                                ";

        if(!empty($args['shop'])) {
            $query              .= " AND c.shop = '".$args['shop']."' ";
        }

        if(!empty($args['start_date']) && !empty($args['end_date'])) {
            $query              .= " AND c.created_at >= '".$args['start_date']." 00:00:00' AND c.created_at <= '".$args['end_date']." 23:59:59' ";
        }

        if(!empty($args['start_date']) && empty($args['end_date'])) {
            $query              .= " AND c.created_at >= '".$args['start_date']." 00:00:00' ";
        }

        if(empty($args['start_date']) && !empty($args['end_date'])) {
            $query              .= " AND c.created_at <= '".$args['end_date']." 23:59:59' ";
        }
        
        if (!empty($args['search'])) {
            $query              .= " AND ( ";
            $query              .= " lower(c.charge_id) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.charge_name) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.order_id) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " OR lower(c.price) LIKE '%" . $this->db->escape_like_str(strtolower($args['search'])) . "%' ";
            $query              .= " ) ";
        }
        $total_data             = $this->db->query($query)->num_rows();
        
        if(!empty($args['order_by']) && !empty($args['sort'])) {
            $query              .= "
                                    ORDER BY " . $args['order_by'] . " " . $args['sort'] . " 
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        } else {
            $query                  .= "
                                    ORDER BY id DESC
                                     LIMIT " . $args['offset'] . ", " . $args['limit'];
        }
        
        $datas                  = $this->db->query($query)->result();
        $result['status']       = '000000';
        $result['message']      = 'success';
        $result['total_data']   = $total_data;
        $result['data']         = $datas;
        return (object) $result;
    }

    public function add_fullfilment($data) {
        
        if (!$this->db->insert('fullfilment_api_response', $data)) {
            $result['status']   = '000000';
            $result['message']  = 'failed';
            $result['data']     = $this->db->error();
            return (object) $result;
        } else {
            $result['status']   = '000000';
            $result['message']  = 'success';
            $result['data']     = $this->db->insert_id();
            return (object) $result;
        }
    }

    public function insertSettings($data) {
        $this->db->insert('settings', $data);
    }

    public function getBanner() {
        $this->db->select("*");
        $this->db->from('settings');
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        return $this->db->get()->row_array();
    }

    public function getAddressWhere($shop_name) {
        $this->db->select('*');
        $this->db->from('stores_address');
        $this->db->where('shop', $shop_name);
        return  $this->db->get();
    }

    public function getAddressWhereId($id) {
        $this->db->select('*');
        $this->db->from('stores_address');
        $this->db->where('id', $id);
        return  $this->db->get()->row();
    }

    public function getStores($shop_name) {
        $this->db->select('*');
        $this->db->from('stores');
        $this->db->where('shop', $shop_name);
        return  $this->db->get()->row();
    }

    public function insertStoreAddress($data) {
         
        return  $this->db->insert('stores_address', $data);
    }

    public function updateStoreAddress($data, $id) {
         
        $this->db->where('id', $id);
        return $this->db->update('stores_address', $data);
    }
}
?>