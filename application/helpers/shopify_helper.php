<?php

defined('BASEPATH') or exit('No direct script access allowed');

	function shopify_call($token, $shop, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array()) {
    
		// Build URL
		$url = "https://" . $shop . "" . $api_endpoint;
		if (!is_null($query) && in_array($method, array('GET', 	'DELETE'))) $url = $url . "?" . http_build_query($query);

		// Configure cURL
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, TRUE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
		// curl_setopt($curl, CURLOPT_SSLVERSION, 3);
		curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

		// Setup headers
		$request_headers[] = "";
		if (!is_null($token)) $request_headers[] = "X-Shopify-Access-Token: " . $token;
		curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

		if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
			if (is_array($query)) $query = http_build_query($query);
			curl_setopt ($curl, CURLOPT_POSTFIELDS, $query);
		}
	    
		// Send request to Shopify and capture any errors
		$response = curl_exec($curl);
		$error_number = curl_errno($curl);
		$error_message = curl_error($curl);

		// Close cURL to be nice
		curl_close($curl);

		// Return an error is cURL has a problem
		if ($error_number) {
			return $error_message;
		} else {

			// No error, return Shopify's response by parsing out the body and the headers
			$response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

			// Convert headers into an array
			$headers = array();
			$header_data = explode("\n",$response[0]);
			$headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
			array_shift($header_data); // Remove status, we've already set it above
			foreach($header_data as $part) {
				$h = explode(":", $part);
				$headers[trim($h[0])] = trim($h[1]);
			}

			// Return headers and Shopify's response
			return array('headers' => $headers, 'response' => $response[1]);
		}
	    
	}

	function update_stores($id, $data) {
	    $CI = & get_instance();

	    $CI->db->where('id', $id);

	    return $CI->db->update('stores', $data);
	}

	function webHook() {

		$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
		$data = file_get_contents('php://input');
		$verified = verify_webhook($data, $hmac_header);
		if ($verified) {
			$data = json_decode($data, true);

			$shop = get_stores_by_name($data["domain"]);
			if(!empty($shop)) {
				$store = $shop;
				update_stores($store['id'], array(
					'is_deleted' => 'y'
				));
			}
		}
		return true;
	}

	function verify_webhook($data, $hmac_header) {
	  	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
	  	return hash_equals($hmac_header, $calculated_hmac);
	}	

	function subscribeWebHook($endPoint, $shop, $token) {

		$postData = array();
		$postData['webhook']['topic'] = 'app/uninstalled';
		$postData['webhook']['address'] = base_url() . 'webHook';
		$postData['webhook']['format'] = 'json';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, "https://" . $shop . $endPoint);
		curl_setopt($ch, CURLOPT_POST, count($postData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Cache-Control: no-cache",
		    "X-Shopify-Access-Token: " . $token
		));
		$response = curl_exec($ch);
		curl_close($ch);

		$response = json_decode($response, true);

		if (!empty($response['webhook']['id'])) {
			return 1;
		} else {
		  	return 0;
		}
	}

	function add_carrier_service($shop, $access_token) {
		$CI = & get_instance();	

		// Set variables for our request
		$token = $access_token;
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Modify add carrier service
		$data = array(
			"carrier_service" => array(
				"name" => "Shopify Shipping",
				"callback_url" => base_url() . "calculate_shipping",
				"service_discovery" => true,
			)
		);

		// Run API call to add carrier service
		$carrier_services = $CI->shopify_call($token, $shop, "/admin/carrier_services.json", $data, 'POST');

		// Storage response
		$carrier_services_response = $carrier_services['response'];

		return $carrier_services_response;
	}

	function get_carrier_service($shop) {
		$CI = & get_instance();	

		// Set variables for our request
		$token = $CI->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$carrier_services = $CI->shopify_call($token, $shop, "/admin/carrier_services.json", array(), 'GET');

		// Storage response
		$carrier_services_response = $carrier_services['response'];

		return $carrier_services_response;
	}

	function get_carrier_service_by_id($shop, $carrier_service_id) {
		$CI = & get_instance();	

		// Set variables for our request
		$token = $CI->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$carrier_services = $CI->shopify_call($token, $shop, "/admin/carrier_services/" . $carrier_service_id . ".json", array(), 'GET');

		// Storage response
		$carrier_services_response = $carrier_services['response'];

		return $carrier_services_response;
	}

	function add_webhook($shop) {
		$CI = & get_instance();	

		// Set variables for our request
		$token = $CI->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Modify add carrier service
		$data = array(
			"webhook" => array(
				"topic" => "orders/paid",
				"address" => base_url() . "order_capture",
				"format" => "json",
			)
		);

		// Run API call to add carrier service
		$webhooks = $CI->shopify_call($token, $shop, "/admin/webhooks.json", $data, 'POST');

		// Storage response
		$webhooks_response = $webhooks['response'];

		return $webhooks_response;
	}

	function update_webhook($shop, $webhook_id) {
		$CI = & get_instance();	

		// Set variables for our request
		$token = $CI->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Modify add carrier service
		$data = array(
			"webhook" => array(
				"id" => $webhook_id,
				"address" => base_url() . "order_capture",
			)
		);

		// Run API call to add carrier service
		$webhooks = $CI->shopify_call($token, $shop, "/admin/webhooks/" . $webhook_id . ".json", $data, 'PUT');

		// Storage response
		$webhooks_response = $webhooks['response'];

		return $webhooks_response;
	}

	function get_event_order($shop) {
		$CI = & get_instance();	

		// Set variables for our request
		$token = $CI->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$event = $CI->shopify_call($token, $shop, "/admin/events.json", array(), 'GET');

		// Storage response
		$event_response = $event['response'];

		return $event_response;
	}

	function get_order_by_id($order_id) {
	    $CI = & get_instance();
		
		if(empty($order_id)) {
			return FALSE;
		}
		
		$CI->db->select("*");
        $CI->db->from('orders');
        $CI->db->where("order_id", $order_id);

        $query                  = $CI->db->get();

        $total_data             = $query->num_rows();
        $datas                  = $query->row();
	    
	    return $datas;
	}

	function add_product_order($args) {
	    $CI = & get_instance();

	    return $CI->db->insert('product_order', $args);
	}

	function add_shipping_address_order($args) {
	    $CI = & get_instance();

	    return $CI->db->insert('shipping_address_order', $args);
	}

	function add_orders($args) {
	    $CI = & get_instance();

	    return $CI->db->insert('orders', $args);
	}

	function gets_stores() {
    	$CI = & get_instance();

        $query                  = "SELECT * FROM stores";

        $total_data             = $CI->db->query($query)->num_rows();
        $datas                  = $CI->db->query($query)->result();

        return $datas;
    }

	function save_order($shop, $access_token) {
		$CI = & get_instance();	

		$datas = array();

		//get list order
		$orders = get_orders($shop, $access_token);

		if($orders['success'] == TRUE) {
			$orders 	= json_decode($orders['response']);
				
			foreach ($orders->orders as $key => $order) {

				//detail order
				$check 		= get_order_by_id($order->id);

				$order_data = $check;

				if(empty($order_data)) {
					
					if($order->financial_status == 'paid') {
						$shipping_lines = $order->shipping_lines;

						$customer 			= $order->customer;
						$customer_address 	= $customer->default_address;

						$Weight 			= floatval($order->total_weight);

						$quantity 			= 0;
						foreach ($order->line_items as $key => $item) {
							$quantity 		= $quantity + $item->quantity;

							$CI->add_product_order(array(
								'shop' 			=> $shop,
								'order_id'		=> $order->id,
								'product_id' 	=> $item->id,
								'title' 		=> $item->title,
								'quantity' 		=> $item->quantity,
								'sku' 			=> $item->sku,
								'weight' 		=> floatval($item->grams),
								'status'		=> 'new',
								'last_sync' 	=> date('Y-m-d H:i:s'),
							));
						}

						$shipping_address 	= $order->shipping_address;

						add_shipping_address_order(array(
							'shop' 			=> $shop,
							'order_id'		=> $order->id,
							'name' 			=> $shipping_address->name,
							'first_name' 	=> $shipping_address->first_name,
							'last_name' 	=> $shipping_address->last_name,
							'phone' 		=> $shipping_address->phone,
							'address1' 		=> $shipping_address->address1,
							'address2' 		=> $shipping_address->address2,
							'city'			=> $shipping_address->city,
							'province'		=> $shipping_address->province,
							'province_code'	=> $shipping_address->province_code,
							'country' 		=> $shipping_address->country,
							'country_code' 	=> $shipping_address->country_code,
							'postcode' 		=> $shipping_address->zip,
						));

						add_orders(array(
							'shop' 			=> $shop,
							'order_id'		=> $order->id,
							'name' 			=> $customer_address->name,
							'first_name' 	=> $customer->first_name,
							'last_name' 	=> $customer->last_name,
							'email' 		=> $customer->email,
							'phone' 		=> $customer->phone,
							'address1' 		=> $customer_address->address1,
							'address2' 		=> $customer_address->address2,
							'city'			=> $customer_address->city,
							'province'		=> $customer_address->province,
							'province_code'	=> $customer_address->province_code,
							'country' 		=> $customer_address->country,
							'country_code' 	=> $customer_address->country_code,
							'postcode' 		=> $customer_address->zip,
							'quantity' 		=> $quantity,
							'weight' 		=> $Weight,
							'currency' 		=> $order->currency,
							'total_price' 	=> $order->total_price,
							'status' 		=> 'new'

						));
					}
				}
			}
		}
		
		return true;
	}

	function order_capture() {
		$CI = & get_instance();	
		$datas = array();
		$stores = gets_stores();

		foreach ($stores as $key => $store) {
			$shop_name = $store->shop;
			//get list order
			$orders = save_order($store->shop, $store->access_token);
		}
	}

	function get_orders($shop, $token) {
		$CI = & get_instance();
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://" . $shop . "/admin/orders.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"X-Shopify-Access-Token: " . $token
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  	'success' 	=> FALSE,
		  	'response' 	=> $err
		  );
		} else {

		  return array(
		  	'success' 	=> TRUE,
		  	'response' 	=> $response
		  );
		}
	}

	function get_order_detail($order_id,$token) {
		$CI = & get_instance();	
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://" . $shop . "/admin/orders/" . $order_id . ".json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"X-Shopify-Access-Token: ".$token
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  	'success' 	=> FALSE,
		  	'response' 	=> $err
		  );
		} else {
		  return array(
		  	'success' 	=> TRUE,
		  	'response' 	=> $response
		  );
		}
	}

	function get_store($shop, $token) {
		$CI = & get_instance();	
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://" . $shop . "/admin/shop.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"X-Shopify-Access-Token: " . $token
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  	'success' 	=> FALSE,
		  	'response' 	=> $err
		  );
		} else {
		  return array(
		  	'success' 	=> TRUE,
		  	'response' 	=> $response
		  );
		}
	}

	function get_stores_by_name($name) {
    	$CI = & get_instance();	

        $CI->db->select("*");
        $CI->db->from('stores');
        $CI->db->where("shop", $name);

        $query                  = $CI->db->get();
        $total_data             = $query->num_rows();
        $datas                  = $query->row();

        return $datas;
    }

    function add_fullfilments($args) {
	    $CI = & get_instance();

	    return $CI->db->insert('fullfilment_api_response', $args);
	}

	function add_fullfilment($args) {
		$CI = & get_instance();	

		// Set variables for our request
		$check = get_stores_by_name($args['shop']);
		if(!empty($check)) {
			$store = $check;
			$token = $args['token'];
		}

		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Modify add carrier service
		$data = array(
			"fulfillment" => array(
				"location_id" => $args['location_id'],
				"tracking_number" => $args['tracking_number'],
				"tracking_urls" => $args['tracking_urls'],
				"notify_customer"=> true
			)
		);

		// Run API call to add carrier service
		$fulfillments = shopify_call($token, $shop, "/admin/orders/" . $args['order_id'] . "/fulfillments.json", $data, 'POST');

		// Storage response
		$fulfillments_response = $fulfillments['response'];

		add_fullfilments(array(
			'shop' => $shop,
			'tracking_number' => $args['tracking_number'],
			'response' => json_encode($fulfillments_response),
			'date_created' => date('Y-m-d H:i:s')
		));

		return true;
	}

	function get_fullfilment($shop, $order_id) {
		$CI = & get_instance();	

		// Set variables for our request
		$token = $CI->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$fulfillments = shopify_call($token, $shop, "/admin/webhooks/" . $order_id . "/fulfillments.json", array(), 'GET');

		// Storage response
		$fulfillments_response = $fulfillments['response'];

		return $fulfillments_response;
	}

	function get_locations($shop, $token) {
		$CI = & get_instance();	

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://" . $shop . "/admin/locations.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"X-Shopify-Access-Token: " . $token
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  	return $err;
		} else {
		  	$locations  = json_decode($response);
			$location 	= $locations->locations;
			$id 		= $location[0]->id;

			return $id;
		}
	}

	function recurring_application_charges($shop, $token) {
		$CI = & get_instance();
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://" . $shop . "/admin/orders.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"X-Shopify-Access-Token: ".$token
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  	'success' 	=> FALSE,
		  	'response' 	=> $err
		  );
		} else {

		  return array(
		  	'success' 	=> TRUE,
		  	'response' 	=> $response
		  );
		}
	}

	function recurring_application_charges_activate($shop, $token, $charge_id) {
		$CI = & get_instance();	
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://" . $shop . "/admin/recurring_application_charges/" . $charge_id . "/activate.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"X-Shopify-Access-Token: " . $token
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  	'success' 	=> FALSE,
		  	'response' 	=> $err
		  );
		} else {

		  return array(
		  	'success' 	=> TRUE,
		  	'response' 	=> $response
		  );
		}
	}

	function usage_charges($shop, $token, $charge_id) {
		$CI = & get_instance();
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://" . $shop . "/admin/recurring_application_charges/" . $charge_id . "/usage_charges.json",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
				"X-Shopify-Access-Token: " . $token
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  	'success' 	=> FALSE,
		  	'response' 	=> $err
		  );
		} else {

		  return array(
		  	'success' 	=> TRUE,
		  	'response' 	=> $response
		  );
		}
	}
