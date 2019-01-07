<?php

defined('BASEPATH') or exit('No direct script access allowed');

	function createShipment($args) {
		$curl = curl_init();

		$request = "{
		  	\n\t\"Ticket\": \"\",
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
			CURLOPT_URL => base_url() . "api/CreateShipment.php?order_id=" . $args['order_id'],
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

	function shipmentLabel($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => base_url() . "api/ShipmentLabel.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"\",\n\t\"ShipmentNumber\": \"".$args['ShipmentNumber']."\"\n}",
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

	function shipmentInfo($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => base_url() . "api/ShipmentInfo.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"\",\n\t\"ShipmentNumber\": \"".$args['ShipmentNumber']."\"\n}",
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

	function assignShipment($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => base_url() . "api/AssignShipments.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"\",\n\t\"CollectionType\": \"SELF_LODGE\",\n\t\"Shipments\": [\n\t\t{\n\t\t\t\"ShipmentNumber\": \"".$args['ShipmentNumber']."\"\n\t\t}\n\t\t]\n}",
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

	function shipmentAvailable($args) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => base_url() . "api/AvailableCollections.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{\n\t\"Ticket\": \"\"\n}",
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