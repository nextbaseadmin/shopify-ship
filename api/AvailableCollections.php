<?php
/*
* Sample Response
*
{
  "status": 200,
  "data": {
    "DropOff": "false",
    "FixedScheduled": "true",
    "SelfLodge": "true",
    "CollectionRequest": {
      "Available": {},
      "CollectionSlots": {
        "Slot": [
          {
            "CollectionDate": "2019-01-04",
            "CollectionTimeFrom": "12:00",
            "CollectionTimeTo": "13:00"
          },
          {
            "CollectionDate": "2019-01-04",
            "CollectionTimeFrom": "13:00",
            "CollectionTimeTo": "14:00"
          },
          {
            "CollectionDate": "2019-01-04",
            "CollectionTimeFrom": "14:00",
            "CollectionTimeTo": "15:00"
          },
          {
            "CollectionDate": "2019-01-04",
            "CollectionTimeFrom": "15:00",
            "CollectionTimeTo": "16:00"
          },
          .
          .
          .
        ]
      }
    }
  }
}
*
*
*/

header('Content-Type: application/json');
echo $response;
?>