<?php
/*
* Sample Response
*
{
  "code": "STATUS_SUCCESS",
  "ShipmentLabels": {
    "LabelURL": "<URL>/data/ABC0000012345.pdf"
  }
}
*
*
*/

header('Content-Type: application/json');
echo $response;
?>