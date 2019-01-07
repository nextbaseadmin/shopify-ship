<?php

        $customerId = "<Customer Id>";
        $user = "<User>";
        $password = "<Password>";
        $aes_key = "<aes key>";
        date_default_timezone_set("Asia/Singapore");

        // 1) Encode User password in SHA1 : SHA1(User_Password) 
        $password = sha1($password);

        // 2) concat Customer ID, user name, user password encoded in SHA1 and date 
        $ticket = $customerId . $user . $password . date('Ymd');

        // Get padding and AES Initialization Vector
        $IVsize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC); 
        $IV = substr(str_pad($aes_key, $IVsize, $aes_key), 0, $IVsize);
        $keySize = mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC); 
        $aes_key = substr(str_pad($aes_key, $keySize, $aes_key), 0, $keySize);
        $BlockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC); 
        $StringLength = strlen($ticket);
        $Padding = $BlockSize - ($StringLength % $BlockSize);

        // 3) Encrypt the result of 2) in AES 256 (Rijndael) with padding in CBC mode : MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC
        $ticket .= str_repeat(chr($Padding), $Padding);
        $ticket = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $aes_key, $ticket, MCRYPT_MODE_CBC, $IV);

        // 4) Encode the result of 3) in base 64 
        $ticket = base64_encode($ticket);

        // 5) In the result of 4), replace reserved characters : 
        $ticket = strtr($ticket, '+/=', '-_,');

        echo $ticket;
?>