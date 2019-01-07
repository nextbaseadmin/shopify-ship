<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Shipping</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="<?php echo base_url();?>assets/panel/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />    
    <!-- FontAwesome 4.3.0 -->
    <link href="<?php echo base_url();?>assets/panel/dist/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo base_url();?>assets/panel/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo base_url();?>assets/panel/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- bootstrap wysihtml5 - text editor -->
    <link href="<?php echo base_url();?>assets/panel/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
    <!-- DATA TABLES -->
    <link href="<?php echo base_url();?>assets/panel/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABES SCRIPT -->
    <script src="<?php echo base_url();?>assets/panel/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="<?php echo base_url();?>assets/panel/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
  <script>
     var siteURL = "<?php echo base_url(); ?>";
     var segmentURL = "<?php echo $this->uri->segment(2); ?>";
   </script>
  </head>
  <body class="skin-black">
    <div class="wrapper">      
      <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo base_url();?>panel" class="logo">Shipping</a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">          
            
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="hidden-xs"><?php echo $this->session->userdata('NAME'); ?></span>
                </a>
                <ul class="dropdown-menu">
                  <li class="user-footer">
                    <div class="pull-right">
                      <a href="<?php echo base_url(); ?>admin/edit_profile/" class="btn btn-primary btn-flat">Edit Profile</a>
                      <a href="<?php echo base_url(); ?>panel/logout" class="btn btn-danger btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img onerror="imgError(this);" src="<?php echo base_url();?>assets/uploads/images/default.png" class="img-circle" />
            </div>
            <div class="pull-left info">
              <p><?php echo $this->session->userdata('NAME'); ?></p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class=""> 
              <a href="<?php echo base_url();?>admin/shipping_rate">
                <i class=""></i> <span>Shipping Rate</span>
              </a>
            </li>
            <li class="">
              <a href="<?php echo base_url();?>admin/shipment">
                <i class=""></i> <span>Shipment</span>
              </a>
            </li>           
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper" style="min-height:767px;">
    