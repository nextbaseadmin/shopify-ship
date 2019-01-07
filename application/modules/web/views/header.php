<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1,user-scalable=0">
        <meta name="format-detection" content="telephone=no">
        <title>Nextbase Ship</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/magnific-popup.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/ship/css/app.css">
        <link rel="author" href="humans.txt">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

        <script src="<?php echo base_url(); ?>assets/ship/js/vendors/modernizr-custom.js"></script>      
        <script src="<?php echo base_url(); ?>assets/ship/js/vendors/jquery.min.js"></script>      
        
    </head>

    <body>
        <?php
            $cleanurl = explode("?", $_SERVER["REQUEST_URI"]);
            $action = explode("/", $cleanurl[0]);
            $action = str_replace("_", " ", $action);
            $action = $action[2];
        ?>
        <div class="top-nav">
            <div class="top-nav--left">
                <ul class="breadcrumb">
                    <li><a href="<?php echo base_url(); ?>home?shop=<?php echo $shop; ?>">Nextbase Ship</a></li>
                    <?php if ($action && $action != "home") { ?>
                        <li><?php echo ucwords($action); ?></li>
                    <?php } ?>
                </ul>
            </div>

            <div class="top-nav--right">
                <ul class="top-nav--list">
                    <li><a href="<?php echo base_url(); ?>transactions?shop=<?php echo $shop; ?>" class="button <?php echo $action == "transactions" ? "blue" : "white";?> ">Transactions</a></li>
                    <li><a href="<?php echo base_url(); ?>shipment_status?shop=<?php echo $shop; ?>" class="button <?php echo $action == "shipment status" ? "blue" : "white";?>">Shipment Status</a></li>
                    <li><a href="<?php echo base_url(); ?>home?shop=<?php echo $shop; ?>" class="button <?php echo ($action == "home" || ($action != "transactions" && $action != "shipment status")) ? "blue" : "white";?>">Shipment</a></li>
                </ul>
            </div>
        </div>