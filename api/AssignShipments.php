<?php
/*
* Sample Response
*
{
  "ShipmentAssigns": {
    "code": "STATUS_SHIPMENT_IS_ALREADY_ASSIGNED_OR_DELETED",
    "message": "Given shipment is already assigned or deleted",
    "ManifestNumber": {},
    "ShipmentNumber": "ABC0000012345",
    "CollectionDocketNumber": {}
  }
}
*
*
*/

header('Content-Type: application/json');
echo $response;
?>