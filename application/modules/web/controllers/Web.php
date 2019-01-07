<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

class Web extends MX_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('web_model', 'web');
	}

	public function index() {
		if ($_GET['code']) {
			$this->ship_connect();
		}

		$data['shop'] = $_GET['shop'];
		$this->load->view('index',$data);
	}

	public function ship_connect() {
		// Set variables for our request
		$api_key 		= $this->config->item('shopify_api_key');
		$shared_secret 	= $this->config->item('shopify_shared_secret');
		$params 		= $_GET; // Retrieve all request parameters
		$hmac 			= $_GET['hmac']; // Retrieve HMAC request parameter
		$params 		= array_diff_key($params, array('hmac' => '')); // Remove hmac from params
		ksort($params); // Sort params lexographically

		// Compute SHA256 digest
		$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

		// Use hmac data to check that the response is from Shopify or not
		if (hash_equals($hmac, $computed_hmac)) {
			// VALIDATED
			// Set variables for our request
			$query = array(
			  "client_id" 		=> $api_key, // Your API key
			  "client_secret" 	=> $shared_secret, // Your app credentials (secret key)
			  "code" 			=> $params['code'], // Grab the access key from the URL
			);

			// Generate access token URL
			$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

			// Configure curl client and execute request
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $access_token_url);
			curl_setopt($ch, CURLOPT_POST, count($query));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
			$result = curl_exec($ch);
			curl_close($ch);

			// Store the access token
			$result = json_decode($result, true);
			$access_token = $result['access_token'];

			$check = $this->web->get_stores_by_name($params['shop']);
			$storeId = null;
			if(empty($check->data)) {
				$newStore = $this->web->add_stores(array(
					'shop' => $params['shop'],
					'access_token' => $access_token,
					'is_singapore_store' => 1,
					'date_created' => date("Y-m-d H:i:s")
				));

				$storeId = $newStore->data;
				$this->add_carrier_service($params['shop'], $result['access_token']);
			} else {
				$store = $check->data;
				$this->web->update_stores($store->id, array(
					'access_token' => $access_token
				));

				$storeId = $store->id;
			}

			//subscribe to webhook
			$check = $this->web->get_stores_by_name($params['shop']);

			if (!empty($check->data)) {
				$store = $check->data;
				$this->subscribeWebHook("/admin/webhooks.json", $params['shop'], $access_token);
			}

			$this->session->set_userdata('access_token',$result['access_token']);
			$this->session->set_userdata('ship_shop',$params['shop']);
						
			redirect(base_url() . 'home?shop=' . $params['shop']);
			exit();
		} else {
			// NOT VALIDATED – Someone is trying to be shady!
			echo '<pre>';
			print_r('NOT VALIDATED');
			exit();
		}
	}

	public function webHook() {

		$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
		$data = file_get_contents('php://input');
		$verified = $this->verify_webhook($data, $hmac_header);
		if ($verified) {
			$data = json_decode($data, true);

			$shop = $this->web->get_stores_by_name($data["domain"]);
			if(!empty($shop->data)) {
				$store = $shop->data;
				$this->web->update_stores($store->id, array(
					'is_deleted' => 'y'
				));
			}
		}
	}

	function verify_webhook($data, $hmac_header) {
	  	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
	  	return hash_equals($hmac_header, $calculated_hmac);
	}

	private function subscribeWebHook($endPoint, $shop, $token) {

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

	private function check_store($shop) {
		$shop 			= str_replace(".myshopify.com","", $shop);
		$api_key 		= $this->config->item('shopify_api_key');
		$scopes 		= "read_shipping,write_shipping,read_orders,write_orders,read_customers,write_customers,read_themes,read_products,read_fulfillments,write_fulfillments";
		$redirect_uri 	= base_url() . "ship_connect";
		$state 			= uniqid();

		// Build install/approval URL to redirect to
		$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri) . "&state=" . $state;

		// Redirect
		header("Location: " . $install_url);
		die();
	}

	public function home($offset = 0) {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$data['shop']							= $_GET['shop'];
				//save order
				$save_order 							= $this->save_order($_GET['shop'], $data['store']->access_token);

				if(empty($data['store']->address1)) {
					//save store information
					$shop 								= $this->get_store($_GET['shop'], $data['store']->access_token);
					if($shop['success'] == TRUE) {
						$shop 							= json_decode($shop['response']);
						$shop 							= $shop->shop;

						$this->web->update_stores($data['store']->id, array(
							'shop_name' 	=> $shop->name,
							'email' 		=> $shop->email,
							'phone' 		=> $shop->phone,
							'address1' 		=> $shop->address1,
							'address2' 		=> $shop->address2,
							'province' 		=> $shop->province,
							'city' 			=> $shop->city,
							'country_code' 	=> $shop->country_code,
							'country_name' 	=> $shop->country_name,
							'postcode' 		=> $shop->zip,
							'last_sync' 	=> date('Y-m-d H:i:s'),
						));
					}
				}

				$shipping_rate 							= $this->web->get_shipping_rate();
				$shipping_rate 							= $shipping_rate->data;
				$data['shipping_rate']					= $shipping_rate;

				$data['service_selected']				= 'Select Service';
				$data['delivery_time']					= '2-3 working day';

				if(isset($_GET['service']) && isset($_GET['order_id'])) {
					$this->session->unset_userdata('service_'.$_GET['order_id']);
					$this->session->set_userdata('service_'.$_GET['order_id'],$_GET['service']);

					if(!empty($_GET['service'])) {

						if($_GET['service'] == $shipping_rate->standard_price) {
							$data['service_selectedx']	= 'Standard';
							$data['delivery_timex']		= '2-3 working day';
						} elseif($_GET['service'] == $shipping_rate->priority_price) {
							$data['service_selectedx']	= 'Priority';
							$data['delivery_timex']		= '1-2 working day';
						} elseif($_GET['service'] == $shipping_rate->express_price) {
							$data['service_selectedx']	= 'Express';
							$data['delivery_timex']		= '1 working day';
						}

						$data['orderx']	= $_GET['order_id'];
					}
				}

				$search                                 = $this->input->get('search');
				$shop                                 	= $this->input->get('shop');
		        $limit                                  = 10;
		        $args                                   = array(
		            'limit'                             => $limit,
		            'offset'                            => $offset,
		            'search'                            => $search,
		            'shop'                            	=> $_GET['shop']
		        );
		        $datas                                  = $this->web->get_create_shipment($args);
		        
		        $this->config->load('pagination', false, true);
		        $config                                 = $this->config->item('paging');
		        $config['per_page']                     = $limit;
		        $config['num_links']                    = 5;
		        $config['first_url'] 					= base_url() . 'home/?shop='.$_GET['shop'];
		        $config['base_url']                     = base_url() . 'home/';
		        $config['next_tag_open'] 				= '<li>';
		        $config['next_tag_close'] 				= '</li>';
				$config['first_tag_open'] 				= '<li>';
				$config['first_tag_close'] 				= '</li>';
				$config['prev_tag_open'] 				= '<li>';
				$config['prev_tag_close'] 				= '</li>';
				$config['num_tag_open'] 				= '<li>';
				$config['num_tag_close'] 				= '</li>';
				$config['cur_tag_open'] 				= '<li><span>';
				$config['cur_tag_close'] 				= '</span></li>';
		        $config['total_rows']                   = $datas->total_data;
		        
		        if ($search || $shop) {
		            $config['suffix']                   = '?' . http_build_query($_GET, '', "&");
		        }

		        $this->pagination->initialize($config);

		        $data["paging"]                      	= $this->pagination->create_links();
		        $data["orders"]                    		= $datas->data;

		        foreach ($data["orders"] as $key => $order) {
		        	$products 							= $this->web->get_product_order_by_order_id($order->order_id);
		        	$data["orders"][$key]->products   	= $products->data;
		        }
				
				$this->load->view('header', $data);
				$this->load->view('home', $data);
				$this->load->view('footer', true);
			}
		}
	}

	public function shipping_rate($offset = 0) {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);			
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$search                                 = $this->input->get('search');
				$shop                                 	= $this->input->get('shop');
		        $limit                                  = 10;
		        $args                                   = array(
		            'limit'                             => $limit,
		            'offset'                            => $offset,
		            'search'                            => $search,
		            'shop'                            	=> $_GET['shop']
		        );
		        $datas                                  = $this->web->get_shipping_rates($args);
		        
		        $this->config->load('pagination', false, true);
		        $config                                 = $this->config->item('paging');
		        $config['per_page']                     = $limit;
		        $config['use_page_numbers']             = TRUE;
		        $config['num_links']                    = 5;
		        $config['base_url']                     = base_url() . 'shipping_rate/';
		        $config['total_rows']                   = $datas->total_data;
		        if ($search || $shop) {
		            $config['suffix']                   = '?' . http_build_query($_GET, '', "&");
		        }
		        $this->pagination->initialize($config);

		        $data["paging"]                      	= $this->pagination->create_links();
		        $data["rates"]                    		= $datas->data;
				
				$this->load->view('shipping_rate', $data);
			}
		}
	}

	public function to_receiver_information() {
		$this->session->unset_userdata('order_id');
		$this->session->set_userdata('order_id',$_POST['order_id']);		

		if(!empty($_POST['dimension'])) {
			$this->session->set_userdata('dimension',$_POST['dimension']);
			$dimension = $_POST['dimension'];

			foreach ($dimension as $key => $dim) {
				$this->web->update_product_order($key, array(
						'long' 			=> $dim['long'],
						'wide' 			=> $dim['wide'],
						'high' 			=> $dim['high'],
					));
			}
		}
		redirect('receiver_information?shop=' . $_POST['shop']);
	}

	public function receiver_information() {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$data['order_id']						= $this->session->userdata('order_id');
				$order                    				= $this->web->get_order_by_id($data['order_id']);
				$data['order']                    		= $order->data;
				$shipping                    			= $this->web->get_shipping_address_order_by_order_id($data['order_id']);
				$data['shipping']                    	= $shipping->data;
				$this->load->view('header', $data);
				$this->load->view('receiver_information', $data);
				$this->load->view('footer', true);
			}
		}
	}

	public function pickup_information() {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$data['order_id']						= $this->session->userdata('order_id');
				$data['order']							= array();
				$data['shipping']						= array();
				$data['cost_service']					= 0;
				//store address
				$data['address'] 		= 0;

				if(isset($_GET['address'])) {
					$address 			= $_GET['address'];
					$data['address'] 	= $address;
					$this->session->unset_userdata('address');
					$this->session->set_userdata('address', $_GET['address']);
				} else {
					$address 			= $this->session->userdata('address');
					
					if(isset($address)) {
						$data['address'] = $address ;
					}
				}

				$stores_address 		= $this->web->get_stores_address_by_name($_GET['shop']);
				$stores_address 		= $stores_address->data;
				$data['stores_address'] = $stores_address;
				$shipping_rate 							= $this->web->get_shipping_rate();
				$shipping_rate 							= $shipping_rate->data;
				$data['shipping_rate']					= $shipping_rate;		

				if(!empty($data['order_id'])) {
					$order                    			= $this->web->get_order_by_id($data['order_id']);
					$data['order']                    	= $order->data;
					$shipping                    		= $this->web->get_shipping_address_order_by_order_id($data['order_id']);
					$data['shipping']                   = $shipping->data;

					$data['cost_service']				= $this->session->userdata('service_'.$data['order_id']); 

					if(empty($data['cost_service'])) {
						$data['cost_service']			= $shipping_rate->standard_price;
					}

					if(!empty($data['cost_service'])) {
						if($data['cost_service'] == $shipping_rate->standard_price) {
							$data['service_selected']	= 'Standard';
							$data['delivery_time']		= '2-3 working day';
						} elseif($data['cost_service'] == $shipping_rate->priority_price) {
							$data['service_selected']	= 'Priority';
							$data['delivery_time']		= '1-2 working day';
						} elseif($data['cost_service'] == $shipping_rate->express_price) {
							$data['service_selected']	= 'Express';
							$data['delivery_time']		= '1 working day';
						}
					}
				}

				$avails = $this->shipmentAvailable();
				
				if($avails['success'] == true) {
					$avails  			= json_decode($avails['response']);
					$avails 			= $avails->data;
				}

				$CollectionRequest 		= $avails->CollectionRequest;
				$CollectionSlots 		= $CollectionRequest->CollectionSlots;
				$Slot 					= $CollectionSlots->Slot;
				$data['collections'] 	= $Slot;
				$data['collection']     = '';

				if(isset($_GET['Collection'])) {
					$collections 		= $_GET['Collection'];
					$data['collection'] = $collections;
					$collections = explode(';', $collections);

					$this->session->unset_userdata('Collection');
					$this->session->set_userdata('Collection', $_GET['Collection']);

					$this->session->unset_userdata('CollectionDate');
					$this->session->set_userdata('CollectionDate',$collections[0]);

					$this->session->unset_userdata('CollectionTimeFrom');
					$this->session->set_userdata('CollectionTimeFrom',$collections[1]);

					$this->session->unset_userdata('CollectionTimeTo');
					$this->session->set_userdata('CollectionTimeTo',$collections[2]);
				} else {
					$Collection 			= $this->session->userdata('Collection');
					if(isset($Collection)) {
						$data['collection'] = $Collection ;
					}
				}

				$data['collectionDate']     = date("l, F jS Y");
				$data['collectionTime']     = "";

				if ($this->session->userdata('CollectionDate')) {
					$data['collectionDate']     = date("l, F jS Y", strtotime($this->session->userdata('CollectionDate')));
				}

				if ($this->session->userdata('CollectionTimeFrom')) {
					$data['collectionTime']     = $this->session->userdata('CollectionTimeFrom') . ";" .
						$this->session->userdata('CollectionTimeTo');
				}

				$this->load->view('header', $data);
				$this->load->view('pickup_information', $data);
				$this->load->view('footer', true);		
			}
		}
	}

	public function orders() {
		if(isset($_GET['order_id'])) {
			$orders 		= $this->web->get_shipments_by_id($_GET['order_id']);
			$data['orders'] = $orders->data;
			$data['tipe'] 	= 'one';

			$this->load->view('order_label',$data);
		} else {
			$orders 		= $this->web->gets_shipments();
			$data['orders'] = $orders->data;
			$data['tipe'] 	= 'list';

			$this->load->view('order_label',$data);
		}
	}
	
	public function manage_address() {
		$data['shop'] 			= $_GET['shop'];
		//get shop address master
		$data['shopName'] 		= $this->web->getStores($data['shop']);
		$data['get_address'] 	= $this->web->getAddressWhere($data['shop']);

		$this->load->view('header', $data);
		$this->load->view('manage_address', $data);
		$this->load->view('footer', true);
	}

	public function manage_address_add() {
		$data['shop'] 			= $_GET['shop'];
		$this->load->view('header', $data);
		$this->load->view('manage_address_add', $data);
		$this->load->view('footer', true);
	}

	public function manage_address_edit() {
		$data['shop'] 			= $_GET['shop'];
		$id 					= $_GET['id'];

		$data['get_address'] = $this->web->getAddressWhereId($id);

		$this->load->view('header', $data);
		$this->load->view('manage_address_edit', $data);
		$this->load->view('footer', true);
	}

	public function manage_address_in() {
		$id = $this->input->post('id');
		//edit
		if(isset($id)) {
			$data = array(
				'shop'			=> $this->input->post('shop'),
				'email'			=> $this->input->post('email'),
				'phone'			=> $this->input->post('phone'),
				'address1'		=> $this->input->post('address-1'),
				'province'		=> $this->input->post('province'),
				'city'			=> $this->input->post('city'),
				'country_code'	=> $this->input->post('country-code'),
				'country_name'	=> $this->input->post('country-name'),
				'postcode'		=> $this->input->post('post-code'),
				'date_modified'	=> date('Y-m-d h:i:s')
			);

			$run = $this->web->updateStoreAddress($data, $id);
			if($run) {
				redirect('manage_address?shop='.$this->input->post('shop'));
			}
		} else {
			//add
			//get shop address master
			$shopName = $this->web->getStores($this->input->post('shop'));

			$data = array(
				'shop'			=> $this->input->post('shop'),
				'shop_name'		=> $shopName->shop_name,
				'email'			=> $this->input->post('email'),
				'phone'			=> $this->input->post('phone'),
				'address1'		=> $this->input->post('address-1'),
				'province'		=> $this->input->post('province'),
				'city'			=> $this->input->post('city'),
				'country_code'	=> $this->input->post('country-code'),
				'country_name'	=> $this->input->post('country-name'),
				'postcode'		=> $this->input->post('post-code'),
				'date_created'	=> date('Y-m-d h:i:s')
			);
			$run = $this->web->insertStoreAddress($data);
			
			if($run) {
				redirect('manage_address?shop='.$this->input->post('shop'));
			}
		}
	}

	public function confirm() {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$data['order_id']						= $this->session->userdata('order_id');
				$data['order']							= array();
				$data['shipping']						= array();
				$data['cost_service']					= 0;
				$data['response'] 						= array();
				$shipping_rate 							= $this->web->get_shipping_rate();
				$shipping_rate 							= $shipping_rate->data;
				$data['shipping_rate']					= $shipping_rate;
				$data['shipping_service'] 				= $this->session->userdata('service_'.$data['order_id']);

				if($data['shipping_service'] == $shipping_rate->standard_price) {
					$data['shipping_service']	= 'Standard';
					$data['delivery_timex']		= '2-3 working day';
				} elseif($data['shipping_service'] == $shipping_rate->priority_price) {
					$data['shipping_service']	= 'Priority';
					$data['delivery_timex']		= '1-2 working day';
				} elseif($data['shipping_service'] == $shipping_rate->express_price) {
					$data['shipping_service']	= 'Express';
					$data['delivery_timex']		= '1 working day';
				}

				if(!empty($data['order_id'])) {
					$order                    			= $this->web->get_order_by_id($data['order_id']);
					$data['order']                    	= $order->data;
					$shipping                    		= $this->web->get_shipping_address_order_by_order_id($data['order_id']);
					$data['shipping']                   = $shipping->data;
					$data['cost_service']				= $this->session->userdata('service_'.$data['order_id']); 

					if(empty($data['cost_service'])) {
						$data['cost_service']			= $shipping_rate->standard_price;
					}

					if(empty($data['store']->charge_id)) {
						//charge capped amount
						$postData = array();
						$postData['recurring_application_charge']['name'] 			= 'Shipment for order #'.$data['order_id'];
						$postData['recurring_application_charge']['price'] 			= $data['cost_service'];
						$postData['recurring_application_charge']['return_url'] 	= base_url().'billing_callback?shop='.$_GET['shop'];
						$postData['recurring_application_charge']['capped_amount'] 	= 1000.00;
						$postData['recurring_application_charge']['terms'] 			= 'Shipping Cost Collection';
						//Don't remove this param for development
						$postData['recurring_application_charge']['test'] 			= true;
						//setup variable
						$shop 							= $_GET['shop'];
						$endPoint 						= "/admin/recurring_application_charges.json";
						$token 							= $data['store']->access_token;
						$query 							= array(
							"Content-type" 			=> "application/json" // Tell Shopify that we're expecting a response in JSON format
							);

						// Run API call to add carrier service
						$event 							= $this->shopify_call($token, $shop, $endPoint, $postData, 'POST');

						// Storage response
						$response 						= json_decode($event['response']);
						$data['response'] 				= $response;	

						$this->load->view('header', $data);
						$this->load->view('confirm', $data);
						$this->load->view('footer', true);						
					} else {
						$postData 									= array();
						$postData['usage_charge']['description'] 	= 'Shipment for order #'.$data['order_id'];
						$postData['usage_charge']['price']			= $data['cost_service'];
						//Don't remove this param for development
						$postData['usage_charge']['test'] 			= true;

						//setup variable
						$shop 							= $_GET['shop'];
						$endPoint 						= "/admin/recurring_application_charges/" . $data['store']->charge_id . "/usage_charges.json";
						$token 							= $data['store']->access_token;

						// Run API call to add carrier service
						$event 							= $this->shopify_call($token, $shop, $endPoint, $postData, 'POST');

						$response 						= json_decode($event['response']);
						$data['response'] 				= $response;	

						$this->web->add_transactions(array(
							'shop'		 				=> $shop,
							'charge_id' 				=> $data['store']->charge_id,
							'description' 				=> $response->usage_charge->description,
							'order_id' 					=> $data['order_id'],
							'billing' 					=> $response->usage_charge->billing_on,
							'price' 					=> $response->usage_charge->price,
							'balance_used' 				=> $response->usage_charge->balance_used,
							'balance_remaining' 		=> $response->usage_charge->balance_remaining,
							'created_at' 				=> $response->usage_charge->created_at,
						));

						//create shipment
						$this->createShipment_process($shop, $data['order_id']);

						$this->load->view('header', $data);
						$this->load->view('billing_usage', $data);
						$this->load->view('footer', true);
					}
				}					
			}
		}
	}

	public function billing_callback() {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$data['order_id']						= $this->session->userdata('order_id');
				$data['order']							= array();
				$data['shipping']						= array();
				$data['cost_service']					= 0;
				$shipping_rate 							= $this->web->get_shipping_rate();
				$shipping_rate 							= $shipping_rate->data;
				$data['shipping_rate']					= $shipping_rate;

				if(!empty($data['order_id'])) {
					$order                    			= $this->web->get_order_by_id($data['order_id']);
					$data['order']                    	= $order->data;
					$shipping                    		= $this->web->get_shipping_address_order_by_order_id($data['order_id']);
					$data['shipping']                   = $shipping->data;
					$data['cost_service']				= $this->session->userdata('service_'.$data['order_id']); 

					if(empty($data['cost_service'])) {
						$data['cost_service']			= $shipping_rate->standard_price;
					}

					$charge_id 							= $_GET["charge_id"];
					$postData 		= array();
					//setup variable
					$shop 			= $_GET['shop'];
					$endPoint 		= "/admin/recurring_application_charges/" . $charge_id . "/activate.json";
					$token 			= $data['store']->access_token;
					// Run API call to add carrier service
					$event 			= $this->shopify_call($token, $shop, $endPoint, $postData, 'POST');
					$response = json_decode($event['response']);

					$this->web->update_stores($data['store']->id, array(
						'charge_id' 			=> $response->recurring_application_charge->id,
						'charge_name' 			=> $response->recurring_application_charge->name,
					));

					$this->web->add_transactions(array(
						'shop' 					=> $_GET['shop'],
						'charge_id' 			=> $response->recurring_application_charge->id,
						'description' 			=> $response->recurring_application_charge->name,
						'order_id' 				=> $data['order_id'],
						'billing' 				=> $response->recurring_application_charge->billing_on,
						'price' 				=> $response->recurring_application_charge->price,
						'balance_used' 			=> $response->recurring_application_charge->balance_used,
						'balance_remaining' 	=> $response->recurring_application_charge->balance_remaining,
						'capped_amount' 		=> $response->recurring_application_charge->capped_amount,
						'confirmation_url' 		=> $response->recurring_application_charge->decorated_return_url,
						'status' 				=> $response->recurring_application_charge->status,
						'activated_on' 			=> $response->recurring_application_charge->activated_on,
						'created_at' 			=> $response->recurring_application_charge->created_at,
						'updated_at' 			=> $response->recurring_application_charge->updated_at,
						'created_date' 			=> date('Y-m-d H:i:s'),
					));

					$data['response'] = $response;
					$this->createShipment_process($shop, $data['order_id']);
					$this->load->view('header', $data);
					$this->load->view('billing_callback', $data);
					$this->load->view('footer', true);
				}					
			}
		}
	}

	public function shipment_status($offset = 0) {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$search                                 = $this->input->get('search');
				$shop                                 	= $this->input->get('shop');
				$start_date                           	= $this->input->get('start_date');
				$end_date                       		= $this->input->get('end_date');
		        $limit                                  = 10;
		        $args                                   = array(
		            'limit'                             => $limit,
		            'offset'                            => $offset,
		            'search'                            => $search,
		            'shop'                            	=> $_GET['shop'],
		            'start_date'                     	=> $start_date,
		            'end_date'                         	=> $end_date
		        );
		        $datas                                  = $this->web->get_shipments($args);
		        $this->config->load('pagination', false, true);
		        $config                                 = $this->config->item('paging');
		        $config['per_page']                     = $limit;
		        $config['num_links']                    = 5;
		        $config['first_url'] 					= base_url() . 'shipment_status/?shop='.$_GET['shop'];
		        $config['base_url']                     = base_url() . 'shipment_status/';
		        $config['next_tag_open'] 				= '<li>';
		        $config['next_tag_close'] 				= '</li>';
				$config['first_tag_open'] 				= '<li>';
				$config['first_tag_close'] 				= '</li>';
				$config['prev_tag_open'] 				= '<li>';
				$config['prev_tag_close'] 				= '</li>';
				$config['num_tag_open'] 				= '<li>';
				$config['num_tag_close'] 				= '</li>';
				$config['cur_tag_open'] 				= '<li><span>';
				$config['cur_tag_close'] 				= '</span></li>';
		        $config['total_rows']                   = $datas->total_data;
		        
		        if ($search || $_GET['shop']) {
		            $config['suffix']                   = '?' . http_build_query($_GET, '', "&");
		        }
		        $this->pagination->initialize($config);

		        $data["paging"]                      	= $this->pagination->create_links();
		        $data["shipments"]                   	= $datas->data;

		        foreach ($data["shipments"] as $key => $ship) {
		        	$image 								= base_url() . '/data/' . $ship->shipment_number . '.pdf.jpg';
		        	$status_img							= $this->is_url_exist($image);
		        	$data["shipments"][$key]->img   	= $status_img;
		        }

		        $data["start_date"]                   	= $start_date;
		        $data["end_date"]                   	= $end_date;

				$this->load->view('header', $data);
				$this->load->view('shipment_status', $data);
				$this->load->view('footer', true);	
			}
		}
	}

	public function transactions($offset = 0) {
		$data['shop'] 	= $_GET['shop'];
		$store 			= $this->web->get_stores_by_name($_GET['shop']);
		$data['banner'] = $this->web->getBanner();

		if (!$store->data) {
			$this->check_store($_GET['shop']);
		} else {
			if (!$store->data->is_singapore_store) {
				$data['errorMsg'] = $data['banner']['country_text'];
				$this->load->view('error-message', $data);
			} else {
				$data['store'] 							= $store->data;
				$search                                 = $this->input->get('search');
				$shop                                 	= $this->input->get('shop');
				$start_date                           	= $this->input->get('start_date');
				$end_date                       		= $this->input->get('end_date');
		        $limit                                  = 10;
		        $args                                   = array(
		            'limit'                             => $limit,
		            'offset'                            => $offset,
		            'search'                            => $search,
		            'shop'                            	=> $_GET['shop'],
		            'start_date'                     	=> $start_date,
		            'end_date'                         	=> $end_date
		        );
		        $datas                                  = $this->web->get_transactions($args);
		        $this->config->load('pagination', false, true);
		        $config                                 = $this->config->item('paging');
		        $config['per_page']                     = $limit;
		        $config['use_page_numbers']             = TRUE;
		        $config['num_links']                    = 5;
		        $config['first_url'] 					= base_url() . 'transactions/?shop='.$_GET['shop'];
		        $config['base_url']                     = base_url() . 'transactions/';
		        $config['next_tag_open'] 				= '<li>';
		        $config['next_tag_close'] 				= '</li>';
				$config['first_tag_open'] 				= '<li>';
				$config['first_tag_close'] 				= '</li>';
				$config['prev_tag_open'] 				= '<li>';
				$config['prev_tag_close'] 				= '</li>';
				$config['num_tag_open'] 				= '<li>';
				$config['num_tag_close'] 				= '</li>';
				$config['cur_tag_open'] 				= '<li><span>';
				$config['cur_tag_close'] 				= '</span></li>';
		        $config['total_rows']                   = $datas->total_data;
		        
		        if ($search || $shop) {
		            $config['suffix']                   = '?' . http_build_query($_GET, '', "&");
		        }
		        $this->pagination->initialize($config);

		        $data["paging"]                      	= $this->pagination->create_links();
		        $data["transactions"]               	= $datas->data;
		        $data["start_date"]                   	= $start_date;
		        $data["end_date"]                   	= $end_date;
				
				$this->load->view('header', $data);
				$this->load->view('transaction', $data);
				$this->load->view('footer', true);	
			}
		}
	}

	public function is_url_exist($url) {
	    $ch = curl_init($url);    
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_exec($ch);
	    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	    if($code == 200) {
	    	$status = true;
	    } else {
	    	$status = false;
	    }
	    curl_close($ch);
		return $status;
	}

	/* API */
	public function shopify_call($token, $shop, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array()) {
    
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

	public function add_carrier_service($shop, $access_token) {
		// Set variables for our request
		$token = $access_token;
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Modify add carrier service
		$data = array(
			"carrier_service" => array(
				"name" => "Nextbase",
				"callback_url" => base_url()."calculate_shipping",
				"service_discovery" => true,
			)
		);

		// Run API call to add carrier service
		$carrier_services = $this->shopify_call($token, $shop, "/admin/carrier_services.json", $data, 'POST');

		// Storage response
		$carrier_services_response = $carrier_services['response'];
		return TRUE;
	}

	public function get_carrier_service() {
		// Set variables for our request
		$shop = "nextbase-ship";
		$token = $this->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$carrier_services = $this->shopify_call($token, $shop, "/admin/carrier_services.json", array(), 'GET');

		// Storage response
		$carrier_services_response = $carrier_services['response'];

		echo '<pre>';
		print_r($carrier_services_response);
		exit();
	}

	public function get_carrier_service_by_id($carrier_service_id) {
		// Set variables for our request
		$shop = "nextbase-ship";
		$token = $this->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$carrier_services = $this->shopify_call($token, $shop, "/admin/carrier_services/".$carrier_service_id.".json", array(), 'GET');

		// Storage response
		$carrier_services_response = $carrier_services['response'];

		echo '<pre>';
		print_r($carrier_services_response);
		exit();
	}

	public function add_webhook() {
		// Set variables for our request
		$shop = "nextbase-ship";
		$token = $this->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Modify add carrier service
		$data = array(
			"webhook" => array(
				"topic" => "orders/paid",
				"address" => base_url()."order_capture",
				"format" => "json",
			)
		);

		// Run API call to add carrier service
		$webhooks = $this->shopify_call($token, $shop, "/admin/webhooks.json", $data, 'POST');

		// Storage response
		$webhooks_response = $webhooks['response'];

		echo '<pre>';
		print_r($webhooks_response);
		exit();
	}

	public function update_webhook($webhook_id) {
		// Set variables for our request
		$shop = "nextbase-ship";
		$token = $this->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Modify add carrier service
		$data = array(
			"webhook" => array(
				"id" => $webhook_id,
				"address" => base_url()."order_capture",
			)
		);

		// Run API call to add carrier service
		$webhooks = $this->shopify_call($token, $shop, "/admin/webhooks/".$webhook_id.".json", $data, 'PUT');

		// Storage response
		$webhooks_response = $webhooks['response'];

		echo '<pre>';
		print_r($webhooks_response);
		exit();
	}

	public function get_event_order() {
		// Set variables for our request
		$shop = "nextbase-ship";
		$token = $this->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$event = $this->shopify_call($token, $shop, "/admin/events.json", array(), 'GET');

		// Storage response
		$event_response = $event['response'];

		echo '<pre>';
		print_r($event_response);
		exit();
	}

	public function save_order($shop, $access_token) {
		$datas = array();

		//get list order
		$orders = $this->get_orders($shop, $access_token);

		if($orders['success'] == TRUE) {
			$orders 	= json_decode($orders['response']);

			if ($orders && $orders->orders) {

				foreach ($orders->orders as $key => $order) {
					//detail order
					$check 		= $this->web->get_order_by_id($order->id);
					$order_data = $check->data;

					if(empty($order_data)) {

						if($order->financial_status == 'paid') {
							$shipping_lines = $order->shipping_lines;
							$customer 			= $order->customer;
							$customer_address 	= $customer->default_address;

							$Weight 			= floatval($order->total_weight);
							$quantity 			= 0;
							
							foreach ($order->line_items as $key => $item) {
								$quantity 		= $quantity + $item->quantity;
								$this->web->add_product_order(array(
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

							$this->web->add_shipping_address_order(array(
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

							$this->web->add_orders(array(
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
		}
		return true;
	}

	public function order_capture() {
		$datas = array();
		$stores = $this->web->gets_stores();
		$stores = $stores->data;

		foreach ($stores as $key => $store) {
			$shop_name = $store->shop;
			//get list order
			$orders = $this->save_order($store->shop, $store->access_token);
		}

		echo '<pre>';
		print_r('Done');
		exit();
	}

	public function order_capture_old() {
		$datas = array();
		$labels = array();
		$ShipmentNumbers = array();
		$stores = $this->web->gets_stores();
		$stores = $stores->data;

		foreach ($stores as $key => $store) {
			$shop_name = $store->shop;
			//get list order
			$orders = $this->get_orders($store->shop, $store->access_token);

			if($orders['success'] == TRUE) {
				$orders 	= json_decode($orders['response']);
				//$store 		= array();
				$stores 	= $this->get_store($store->shop, $store->access_token);

				if($stores['success'] == TRUE) {
					$store 	= json_decode($stores['response']);
					$store 	= $store->shop;
				}

				foreach ($orders->orders as $key => $order) {
					//detail order
					$check = $this->web->get_shipments_by_id($order->id);

					$order_data = $check->data;

					if(empty($order_data)) {
						
						if($order->financial_status == 'paid') {
							$shipping_lines = $order->shipping_lines;
							$customer 			= $order->customer;
							$customer_address 	= $customer->default_address;

							$Weight 			= $order->total_weight;
							$Weight 			= $Weight/1000;

							//trigger create shipment
							$shipmentData = array();
							$shipmentData['ShipFromAddress']['Name1'] = $store->name;
							$shipmentData['ShipFromAddress']['Name2'] = "";
							$shipmentData['ShipFromAddress']['AddressLine1'] = $store->address1;
							$shipmentData['ShipFromAddress']['AddressLine2'] = $store->address2;
							$shipmentData['ShipFromAddress']['Postcode'] = '408600';//$store->zip;
							$shipmentData['ShipFromAddress']['Country'] = 'SG';//$store->country_code;
							$shipmentData['ShipFromAddress']['Contact']['ContactName'] = $store->name;
							$shipmentData['ShipFromAddress']['Contact']['EmailAddress'] = $store->email;
							$shipmentData['ShipFromAddress']['Contact']['PhoneNumber'] = $store->phone;
							$shipmentData['ShipToAddress']['Name1'] = $customer->first_name;
							$shipmentData['ShipToAddress']['Name2'] = $customer->last_name;
							$shipmentData['ShipToAddress']['AddressLine1'] = $customer_address->address1;
							$shipmentData['ShipToAddress']['AddressLine2'] = $customer_address->address2;
							$shipmentData['ShipToAddress']['Postcode'] = '408600';
							$shipmentData['ShipToAddress']['Country'] = 'SG';//$customer_address->country_code;
							$shipmentData['ShipToAddress']['Contact']['ContactName'] = $customer_address->name;
							$shipmentData['ShipToAddress']['Contact']['EmailAddress'] = $customer->email;
							$shipmentData['ShipToAddress']['Contact']['PhoneNumber'] = $customer->phone; 
							$shipmentData['SenderReference'] = uniqid();
							$shipmentData['HandlingUnits']['Weight'] = $Weight;
							$shipmentData['HandlingUnits']['ContentDetailItems']['ItemWeight'] = $Weight;
							$shipmentData['HandlingUnits']['ContentDetailItems']['ItemCurrency'] = $order->currency;
							$shipmentData['HandlingUnits']['ContentDetailItems']['TotalAmount'] = $order->total_price;
							$shipmentData['TotalValue'] = $order->total_price;
							$shipmentData['TotalValueCurrency'] = $order->currency;
							$createShipment = $this->createShipment($shipmentData);

							if($createShipment['success'] == true) {
								$shipments  = json_decode($createShipment['response']);
								$shipment 	= $shipments->Shipment;
								$ShipmentNumber = $shipment->ShipmentNumber;
								//create label
								$label = array();
								$label['ShipmentNumber'] = $ShipmentNumber; 
								$shipmentLabel = $this->shipmentLabel($label);
								$shipmentsLabel  	= json_decode($shipmentLabel['response']);
								$ShipmentLabels 	= $shipmentsLabel->ShipmentLabels;
								$LabelURL 			= preg_replace('/\\\\/', '', $ShipmentLabels->LabelURL); 
								//create fullfilment for order
								$order_id = $datas['order_id'];
								$location_id = $this->get_locations($store->shop, $store->access_token);
								$tracking_number = $ShipmentNumber;
								$tracking_urls = array();
								$add_fullfilment = $this->add_fullfilment(array(
									'shop' => $store->shop,
									'token' => $store->access_token,
									'order_id' => $order_id,
									'location_id' => $location_id,
									'tracking_number' => $tracking_number,
									'tracking_urls' => $tracking_urls
								));

								//assignment shipment
								$assignment = array();
								$assignment['ShipmentNumber'] = $ShipmentNumber; 
								$shipmentLabel = $this->assignShipment($assignment);
								$this->web->add_shipments(array(
									'shop' => $shop_name,
									'order_id' => $order->id,
									'shipment_number' => $ShipmentNumber,
									'label' => $LabelURL,
									'status' => 'done',
									'created_date' => date('Y-m-d H:i:s')
								));

								array_push($labels, $LabelURL);
								array_push($ShipmentNumbers, $ShipmentNumber);
							}
						}
					}
				}
			}
		}

		echo '<pre>';
		print_r(array(
			'labels' => $labels,
			'ShipmentNumbers' => $ShipmentNumbers,
		));
		exit();
	}

	public function get_orders($shop, $token) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://".$shop."/admin/orders.json",
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

	public function get_order_detail($order_id,$token) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://".$shop."/admin/orders/".$order_id.".json",
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

	public function get_store($shop, $token) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://".$shop."/admin/shop.json",
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

	public function add_fullfilment($args) {
		// Set variables for our request
		$check = $this->web->get_stores_by_name($args['shop']);
		
		if(!empty($check->data)) {
			$store = $check->data;
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
		$fulfillments = $this->shopify_call($token, $shop, "/admin/orders/".$args['order_id']."/fulfillments.json", $data, 'POST');

		// Storage response
		$fulfillments_response = $fulfillments['response'];

		$this->web->add_fullfilment(array(
			'shop' => $shop,
			'tracking_number' => $args['tracking_number'],
			'response' => json_encode($fulfillments_response)
		));

		return true;
	}

	public function get_fullfilment($order_id) {
		// Set variables for our request
		$shop = "nextbase-ship";
		$token = $this->session->userdata('access_token');
		$query = array(
			"Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
		);

		// Run API call to add carrier service
		$fulfillments = $this->shopify_call($token, $shop, "/admin/webhooks/".$order_id."/fulfillments.json", array(), 'GET');

		// Storage response
		$fulfillments_response = $fulfillments['response'];

		echo '<pre>';
		print_r($fulfillments_response);
		exit();
	}

	public function get_locations($shop, $token) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://".$shop."/admin/locations.json",
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
		  	return $err;
		} else {
		  	$locations  = json_decode($response);
			$location 	= $locations->locations;
			$id 		= $location[0]->id;

			return $id;
		}
	}

	public function recurring_application_charges($shop, $token) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://".$shop."/admin/orders.json",
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

	public function recurring_application_charges_activate($shop, $token, $charge_id) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://".$shop."/admin/recurring_application_charges/" . $charge_id . "/activate.json",
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

	public function usage_charges($shop, $token, $charge_id) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://".$shop."/admin/recurring_application_charges/" . $charge_id . "/usage_charges.json",
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

	public function createShipment_process($shop, $order_id) {
		$store 			= $this->web->get_stores_by_name($shop);
		$store 			= $store->data;
		$order          = $this->web->get_order_by_id($order_id);
		$order 			= $order->data;
		$sa          	= $this->web->get_shipping_address_order_by_order_id($order_id);
		$sa 			= $sa->data;

		//detail order
		$check 			= $this->web->get_shipments_by_id($order_id);
		$order_data 	= $check->data;

		if(empty($order_data)) {
			$weight 		= $order->weight;
			$weight 		= $weight/1000;

			if($weight < 0.001) {
				$weight 	= 0.001;
			}

			$long 			= 0;
			$wide 			= 0;
			$high 			= 0;
			$dimension 		= $this->session->userdata('dimension');
			
			if(isset($dimension) && !empty($dimension)) {
				
				foreach ($dimension as $key => $dim) {
						$long += $dim['long'];
						$wide += $dim['wide'];
						$high += $dim['high'];
				}
			}
			
			$CollectionDate = $this->session->userdata('CollectionDate');
			$CollectionTimeFrom = $this->session->userdata('CollectionTimeFrom');
			$CollectionTimeTo = $this->session->userdata('CollectionTimeTo');

			$address_id			= $this->session->userdata('address');
			
			if(isset($address) && !empty($address)) {
				$address 		= $this->web->get_stores_address_by_id($address_id);
				$address 		= $address->data;
				$AddressLine1 	= $address->address1;
				$AddressLine2 	= '';
				$Postcode 		= $address->postcode;
				$Country 		= $address->country_code;

				if(!empty($Country)) {
					
					if($Country != 'SG') {
						$Country 	= 'SG';
					}
				} else {
					$Country 	= 'SG';
				}
			} else {
				$AddressLine1 	= $store->address1;
				$AddressLine2 	= $store->address2;
				$Postcode 		= $store->postcode;
				$Country 		= $store->country_code;
			}

			if(empty($Country)) {
				$Country 	= 'SG';
			}

			if(empty($Postcode)) {
				$Postcode 	= '408600';
			}

			//trigger create shipment
			$shipmentData = array();
			$shipmentData['order_id'] = $order_id;
			$shipmentData['ShipFromAddress']['Name1'] = $store->shop_name;
			$shipmentData['ShipFromAddress']['Name2'] = "";
			$shipmentData['ShipFromAddress']['AddressLine1'] = $AddressLine1;
			$shipmentData['ShipFromAddress']['AddressLine2'] = $AddressLine2;
			$shipmentData['ShipFromAddress']['Postcode'] = $Postcode;//$store->postcode;
			$shipmentData['ShipFromAddress']['Country'] = $Country;
			$shipmentData['ShipFromAddress']['Contact']['ContactName'] = $store->shop_name;
			$shipmentData['ShipFromAddress']['Contact']['EmailAddress'] = $store->email;
			$shipmentData['ShipFromAddress']['Contact']['PhoneNumber'] = $store->phone;
			$shipmentData['ShipToAddress']['Name1'] = $sa->first_name;
			$shipmentData['ShipToAddress']['Name2'] = $sa->last_name;
			$shipmentData['ShipToAddress']['AddressLine1'] = $sa->address1;
			$shipmentData['ShipToAddress']['AddressLine2'] = $sa->address2;
			$shipmentData['ShipToAddress']['Postcode'] = $sa->postcode;
			$shipmentData['ShipToAddress']['Country'] = $sa->country_code;
			$shipmentData['ShipToAddress']['Contact']['ContactName'] = $sa->name;
			$shipmentData['ShipToAddress']['Contact']['EmailAddress'] = '';//$sa->email;
			$shipmentData['ShipToAddress']['Contact']['PhoneNumber'] = $sa->phone; 
			$shipmentData['SenderReference'] = uniqid();
			$shipmentData['HandlingUnits']['Weight'] = (!empty($weight))?$weight:1;
			$shipmentData['HandlingUnits']['PackageLength'] = (!empty($long))?$long:1;
			$shipmentData['HandlingUnits']['PackageWidth'] = (!empty($wide))?$long:1;
			$shipmentData['HandlingUnits']['PackageHeight'] = (!empty($high))?$high:1;
			$shipmentData['HandlingUnits']['ContentDetailItems']['ItemWeight'] = $weight;
			$shipmentData['HandlingUnits']['ContentDetailItems']['ItemCurrency'] = $order->currency;
			$shipmentData['HandlingUnits']['ContentDetailItems']['TotalAmount'] = $order->total_price;
			$shipmentData['TotalValue'] = $order->total_price;
			$shipmentData['TotalValueCurrency'] = $order->currency;
			$shipmentData['CollectionDate'] = $CollectionDate;
			$shipmentData['CollectionTimeFrom'] = $CollectionTimeFrom;
			$shipmentData['CollectionTimeTo'] = $CollectionTimeTo;

			$createShipment = $this->createShipment($shipmentData);

			if($createShipment['success'] == true) {
				$shipments  = json_decode($createShipment['response']);
				$shipment 	= $shipments->Shipment;
				$ShipmentNumber = $shipment->ShipmentNumber;
				//arrange for collection
				//create label
				$label = array();
				$label['ShipmentNumber'] = $ShipmentNumber; 
				$shipmentLabel = $this->shipmentLabel($label);
				$shipmentsLabel  	= json_decode($shipmentLabel['response']);
				$ShipmentLabels 	= $shipmentsLabel->ShipmentLabels;
				$LabelURL 			= preg_replace('/\\\\/', '', $ShipmentLabels->LabelURL); 
				//create fullfilment for order
				$location_id = $this->get_locations($store->shop, $store->access_token);
				$tracking_number = $ShipmentNumber;
				$tracking_urls = array();
				$add_fullfilment = $this->add_fullfilment(array(
					'shop' => $shop,
					'token' => $store->access_token,
					'order_id' => $order_id,
					'location_id' => $location_id,
					'tracking_number' => $tracking_number,
					'tracking_urls' => $tracking_urls
				));

				//assignment shipment
				$assignment = array();
				$assignment['ShipmentNumber'] = $ShipmentNumber; 
				$shipmentLabel = $this->assignShipment($assignment);
				$create_status = 'success';
				
				if($shipment->code != 'STATUS_SUCCESS') {
					$create_status = $shipment->message;
				}

				$this->web->add_shipments(array(
					'shop' => $shop,
					'order_id' => $order_id,
					'shipment_number' => $ShipmentNumber,
					'label' => $LabelURL,
					'status' => 'new',
					'request_create' => $createShipment['request'],
					'response_create' => $createShipment['response'],
					'create_code' => $shipment->code,
					'create_status' => $create_status,
					'created_date' => date('Y-m-d H:i:s')
				));

				array_push($labels, $LabelURL);
				array_push($ShipmentNumbers, $ShipmentNumber);
			}
			return true;
		} else {
			return false;
		}
	}

	public function cron_shipment() {
		$shipments 		= $this->web->gets_shipments();
		$shipments      = $shipments->data;
		$shipmentInfo_total 		= 0;
		$ShipmentAssigns_total 		= 0;
		
		foreach ($shipments as $key => $ship) {
			$args['ShipmentNumber'] = $ship->shipment_number; 
			$shipmentInfo = $this->shipmentInfo($args);

			if($shipmentInfo['success'] == true) {
				$save['response_info'] = $shipmentInfo['response'];
				$shipmentInfo  						= json_decode($shipmentInfo['response']);
				$data								= $shipmentInfo->data;
				$save['shipment_code'] = $data->code;
			}

			$assignShipment = $this->assignShipment($args);
			
			if($assignShipment['success'] == true) {
				$save['response_assign'] 	= $assignShipment['response'];
				$assignShipment  			= json_decode($assignShipment['response']);
				$ShipmentAssigns			= $assignShipment->ShipmentAssigns;
				$save['assign_code'] 		= $ShipmentAssigns->code;
			}

			$this->web->update_shipments($ship->id, $save);
			$shipmentInfo_total++;
			$ShipmentAssigns_total++;
		}

		echo '<pre>';
		print_r(array(
			'shipmentInfo_total' => $shipmentInfo_total,
			'ShipmentAssigns_total' => $ShipmentAssigns_total,
		));
		exit();
	}

	public function createShipment($args) {
		$curl = curl_init();

		$request = "{
		  	\n\t\"Ticket\": \"KaEVPWBk6PI3F7u1wTSOXw-Vvm3PKK2-vvOVABy7x_e5s47NgIgzilMRn2ArXwDscBdn4ZqcdUdV8NjRuWtFgQ,,\",
			\n\t\"ShipmentType\": \"REGULAR_SHIPMENT\",
			\n\t\"CarrierCode\": \"LOG\",
			\n\t\"ServiceCode\": \"IWCNDD\",
			\n\t\"ShipByAddressCode\": \"OP\",
			\n\t\"ShipFromAddress\": {\n\t\t\"Name1\": \"".$args['ShipFromAddress']['Name1']."\",
		  							\n\t\t\"Name2\": \"".$args['ShipFromAddress']['Name2']."\",
		  							\n\t\t\"AddressLine1\": \"".$args['ShipFromAddress']['AddressLine1']."\",
		  							\n\t\t\"AddressLine2\": \"".$args['ShipFromAddress']['AddressLine2']."\",
		  							\n\t\t\"Postcode\": \"".$args['ShipFromAddress']['Postcode']."\",
		  							\n\t\t\"Country\": \"".$args['ShipFromAddress']['Country']."\",
		  							\n\t\t\"Contact\": {\n\t\t\t\"ContactName\": \"".$args['ShipFromAddress']['Contact']['ContactName']."\",
		  												\n\t\t\t\"EmailAddress\": \"".$args['ShipFromAddress']['Contact']['EmailAddress']."\",
		  												\n\t\t\t\"PhoneNumber\": \"".$args['ShipFromAddress']['Contact']['PhoneNumber']."\",
		  												\n\t\t\t\"AlternatePhoneNo\": \"\",
		  												\n\t\t\t\"FaxNumber\": \"\"\n\t\t
		  												}\n\t
		  							},
		  	\n\t\"ShipToAddress\": {\n\t\t\"Name1\": \"".$args['ShipToAddress']['Name1']."\",
		  							\n\t\t\"Name2\": \"".$args['ShipToAddress']['Name2']."\",
		  							\n\t\t\"AddressLine1\": \"".$args['ShipToAddress']['AddressLine1']."\",
		  							\n\t\t\"AddressLine2\": \"".$args['ShipToAddress']['AddressLine2']."b\",
		  							\n\t\t\"AddressLine3\": \"\",
		  							\n\t\t\"Postcode\": \"".$args['ShipToAddress']['Postcode']."\",
		  							\n\t\t\"Country\": \"".$args['ShipToAddress']['Country']."\",
		  							\n\t\t\"Contact\": {\n\t\t\t\"ContactName\": \"".$args['ShipToAddress']['Contact']['ContactName']."\",
		  												\n\t\t\t\"EmailAddress\": \"".$args['ShipToAddress']['Contact']['EmailAddress']."\",
		  												\n\t\t\t\"PhoneNumber\": \"".$args['ShipToAddress']['Contact']['PhoneNumber']."\",
		  												\n\t\t\t\"AlternatePhoneNo\": \"\",
		  												\n\t\t\t\"FaxNumber\": \"\"\n\t\t
		  												}\n\t
		  							},
		  	\n\t\"EmailNotification\": \"1\",
		  	\n\t\"EmailNotificationShipper\": \"1\",
		  	\n\t\"SenderReference\": \"".$args['SenderReference']."\",
		  	\n\t\"ItemType\": \"P\",
		  	\n\t\"Size\": \"\",
		  	\n\t\"HandlingUnits\": {\n\t\t\"Weight\": \"".$args['HandlingUnits']['Weight']."\",
		  							\n\t\t\"PackageLength\": \"".$args['HandlingUnits']['PackageLength']."\",
		  							\n\t\t\"PackageWidth\": \"".$args['HandlingUnits']['PackageWidth']."\",
		  							\n\t\t\"PackageHeight\": \"".$args['HandlingUnits']['PackageHeight']."\",
		  							\n\t\t\"ContentDetailItems\": {\n\t\t\t\"ItemDescription\": \"Item\",
		  															\n\t\t\t\"ItemWeight\": \"".$args['HandlingUnits']['ContentDetailItems']['ItemWeight']."\",
		  															\n\t\t\t\"ItemCurrency\": \"".$args['HandlingUnits']['ContentDetailItems']['ItemCurrency']."\",
		  															\n\t\t\t\"TotalAmount\": \"".$args['HandlingUnits']['ContentDetailItems']['TotalAmount']."\"\n\t\t
		  															}\n\t
		  							},
		  	\n\t\"TotalValue\": \"".$args['TotalValue']."\",
		  	\n\t\"TotalValueCurrency\": \"".$args['TotalValueCurrency']."\",
		  	\n\t\"AccountNumber\": \"0050505F\",
		  	\n\t\"ContractNumber\": \"\"\n,
		  	\n\t\"CollectionDate\": \"".$args['CollectionDate']."\",
		  	\n\t\"CollectionTimeFrom\": \"".$args['CollectionTimeFrom']."\",
		  	\n\t\"CollectionTimeTo\": \"".$args['CollectionTimeTo']."\"
		  }";

		curl_setopt_array($curl, array(
		  CURLOPT_URL => base_url()."api/CreateShipment.php?order_id=".$args['order_id'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $request,
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/json",
		    "postman-token: fefe56c3-78ed-9888-0ab4-fe1729c5accb"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  		'success' 	=> false,
		  		'response' 	=> $err,
		  		'request'	=> $request
		  );
		} else {
		  return array(
		  		'success' 	=> true,
		  		'response' 	=> $response,
		  		'request'	=> $request
		  );
		}
	}

	public function shipmentLabel($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => base_url()."api/ShipmentLabel.php",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"KaEVPWBk6PI3F7u1wTSOXw-Vvm3PKK2-vvOVABy7x_e5s47NgIgzilMRn2ArXwDscBdn4ZqcdUdV8NjRuWtFgQ,,\",
		  \n\t\"ShipmentNumber\": \"".$args['ShipmentNumber']."\"\n}",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  		'success' 	=> false,
		  		'response' 	=> $err
		  );
		} else {
		  return array(
		  		'success' 	=> true,
		  		'response' 	=> $response
		  );
		}
	}

	public function shipmentInfo($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => base_url()."api/ShipmentInfo.php",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"KaEVPWBk6PI3F7u1wTSOXw-Vvm3PKK2-vvOVABy7x_e5s47NgIgzilMRn2ArXwDscBdn4ZqcdUdV8NjRuWtFgQ,,\",\n\t\"ShipmentNumber\": \"".$args['ShipmentNumber']."\"\n}",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  		'success' 	=> false,
		  		'response' 	=> $err
		  );
		} else {
		  return array(
		  		'success' 	=> true,
		  		'response' 	=> $response
		  );
		}
	}

	public function assignShipment($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => base_url()."api/AssignShipments.php",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"KaEVPWBk6PI3F7u1wTSOXw-Vvm3PKK2-vvOVABy7x_e5s47NgIgzilMRn2ArXwDscBdn4ZqcdUdV8NjRuWtFgQ,,\",
		  	\n\t\"CollectionType\": \"SELF_LODGE\",
		  	\n\t\"Shipments\": [\n\t\t{\n\t\t\t\"ShipmentNumber\": \"".$args['ShipmentNumber']."\"\n\t\t}\n\t\t]\n}",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/json",
		    "postman-token: 62906bd2-6b31-ab17-4438-26c327dce5c3"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  		'success' 	=> false,
		  		'response' 	=> $err
		  );
		} else {
		  return array(
		  		'success' 	=> true,
		  		'response' 	=> $response
		  );
		}
	}

	public function shipmentAvailable($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => base_url()."api/AvailableCollections.php",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"KaEVPWBk6PI3F7u1wTSOXw-Vvm3PKK2-vvOVABy7x_e5s47NgIgzilMRn2ArXwDscBdn4ZqcdUdV8NjRuWtFgQ,,\"\n}",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return array(
		  		'success' 	=> false,
		  		'response' 	=> $err
		  );
		} else {
		  return array(
		  		'success' 	=> true,
		  		'response' 	=> $response
		  );
		}
	}
	
	/* API for shopify */
	public function calculate_shipping() {
		$json_str = file_get_contents('php://input');
		$body = json_decode($json_str, TRUE);
		$from_city  = $body['rate']['origin']['city'];
		$to_city    = $body['rate']['destination']['city'];

		if(!isset($from_city)) {
			$from_city  = 'Bukit Batok Central';
		}

		if(!isset($to_city)) {
			$to_city    = 'Jurong';
		}

		$shipping   	= $this->web->get_shipping_rate();
		$shipping 		= $shipping->data;
		$weight         = 0;
		$weight_unit    = '';
		$standard_price = 0;
		$priority_price = 0;
		$express_price 	= 0;
		
		foreach ($body['rate']['items'] as $key => $value) {
			$weight       = $weight + $value['quantity'];
			$weight_unit  = $value['grams'];

			if($shipping->weight_unit != 'gram') {
			  //convert kilo to gram
			  $shipping->weight_unit  = 'gram';
			  $shipping->weight       = $shipping->weight * 1000;
			}

			if($shipping->weight_unit == 'gram') {
			  
			  if($weight <= 2) {
			    $standard_price = $standard_price + $shipping->standard_price;
			    $priority_price = $priority_price + $shipping->priority_price;
			    $express_price = $express_price + $shipping->express_price;
			  } elseif($weight > 2) {
			    $standard_price = $standard_price + ($shipping->standard_price * $weight);
			    $priority_price = $priority_price + ($shipping->priority_price * $weight);
			    $express_price = $express_price + ($shipping->express_price * $weight);
			  }
			}
		}
		
		//convert currency to SGD
		$rates  = array(
	        array(
	            'service_name'      => 'Speedpost Standard',
	            'description'       => '1 - 2 Days',
	            'service_code'      => "SS",
	            'currency'          => "SGD",
	            'total_price'       => $standard_price,
	            'phone_required'    => '021-111-1111',
	            'min_delivery_date' => date("Y-m-d H:i:s", strtotime('+2 hours')).' -0400',
	            'max_delivery_date' => date("Y-m-d H:i:s", strtotime('+7 days')).' -0400',
	        ),
	        array(
	            'service_name'      => 'Speedpost Priority',
	            'description'       => '0 - 1 Day',
	            'service_code'      => "SP",
	            'currency'          => "SGD",
	            'total_price'       => $priority_price,
	            'phone_required'    => '021-111-1111',
	            'min_delivery_date' => date("Y-m-d H:i:s", strtotime('+2 hours')).' -0400',
	            'max_delivery_date' => date("Y-m-d H:i:s", strtotime('+7 days')).' -0400',
	        ),
	        array(
	            'service_name'      => 'Speedpost Express',
	            'description'       => '0 - 1 Day',
	            'service_code'      => "SE",
	            'currency'          => "SGD",
	            'total_price'       => $express_price,
	            'phone_required'    => '021-111-1111',
	           'min_delivery_date' => date("Y-m-d H:i:s", strtotime('+2 hours')).' -0400',
	            'max_delivery_date' => date("Y-m-d H:i:s", strtotime('+7 days')).' -0400',
	        )
	    );

		header('Content-Type: application/json');
		echo json_encode(array(
                'status'    => 200,
                'message'   => 'success',
                'rates'     => $rates
            ));
	}
}