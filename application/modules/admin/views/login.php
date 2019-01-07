<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Administrator | Log in</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="<?php echo base_url();?>assets/panel/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https:/maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo base_url();?>assets/panel/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/panel/dist/css/skins/skin-black.css" rel="stylesheet" type="text/css" />

  </head>
  <body class="">
    <div class="login-box">
      <div class="login-logo">
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
         <form id="logform" name="logform" action="<?php echo base_url() ?>admin/loginAct" method="post">
          <div class="form-group has-feedback">            
            <input type="text" class="form-control"  name="username" id="username" placeholder="Username" autocomplete = "off" required>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password"  class="form-control" name="password" id="password" placeholder="Password" autocomplete = "off" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div><!-- /.col -->
             <div class="col-xs-8">
            </div><!-- /.col -->
          </div>
        </form>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->
    <script src="<?php echo base_url();?>assets/panel/plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo base_url();?>assets/panel/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    
  </body>
</html>