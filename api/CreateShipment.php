<?php
/*
* Sample Response
*
{
  "code": "STATUS_SUCCESS",
  "message": "Transaction has been successfully completed!!",
  "Shipment": {
    "URLTrackTrace": {},
    "URLneoPod": {},
    "ShipmentNumber": "ABC0000012345",
    "Reference": "ABC0000012345",
    "ManifestNumber": {},
    "CollectionDocketNumber": {}
  }
}    
*
*
*/

header('Content-Type: application/json');
echo $response;
?>