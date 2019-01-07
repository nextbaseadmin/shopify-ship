<?php
/*
* Sample Response
*
{
  "status": 200,
  "data": {
    "original_reference": {},
    "code": "STATUS_SHIPMENT_GET_SUCCESS",
    "message": "Shipment get successfully",
    "ShipmentInfoData": {
      "Reference": {},
      "ShipmentNumber": "ABC0000012345",
      "TrackTrace": {
        "Event": {
          "EventCode": "AB",
          "EventName": "Notification of shipment confirmation",
          "EventDate": "04012019",
          "EventTime": "1234",
          "EventSignatoryName": {}
        }
      },
      "ManifestNumber": {}
    }
  }
}
*
*
*/

header('Content-Type: application/json');
echo $response;
?>