<?php
session_start();
require_once("config/class.user.php");
$login = new USER();

# this is just a checkpoint just in case a logged in user will go back to this page.
if($login->is_loggedin()) // if user logged in
{
    $login->redirect('Application/?dashboard'); // redirect to homepage
}

# action script if login button was clicked
if(isset($_POST['btn-login']))
{
    $uname  = strip_tags($_POST['uname']);
    $uemail = strip_tags($_POST['uname']);
    $upass  = strip_tags($_POST['upass']);

    # declared variable are passed to doLogin function, which is located at class.user.php
    if($login->doLogin($uname,$uemail,$upass))
    {
        # if login is successful, redirect to homepage, `redirect` function can be found in class.user.php
        $login->redirect('Application/?dashboard');
    }
    else
    {
        # if login details are incorred, display error message.
        $error = "Username/Email or Password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AddEditDelete - Login</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="dist/font-awesome-4.7.0/css/font-awesome.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="dist/ionicons-2.0.1/css/ionicons.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="dist/css/animate.css">
        <!-- Pace style -->
        <link rel="stylesheet" href="plugins/pace/pace.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="./"><b>Admin</b>LTE</a><br>
                <span style="font-size: 25px;">Add • Edit • Delete</span>
            </div>
            <!-- /.login-logo -->
            <?php
                if(isset($error))
                {
            ?>
            <div class="alert alert-danger flat animated bounceIn">
                <span style="font-size: 15px;"><i class="fa fa-frown-o"></i> &nbsp;&nbsp; <?php echo $error; ?></span>
            </div>
            <?php } ?>
            <div class="login-box-body">
                <p class="login-box-msg">Login in to start your session</p>
                <form name="frmLogin" id="frmLogin" method="POST">
                    <div class="form-group has-feedback">
                        <input name="uname" id="uname" class="form-control" placeholder="Username or Email">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input name="upass" id="upass" type="password" class="form-control" placeholder="Password">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="submit" name="btn-login" class="btn btn-primary btn-block btn-flat">Sign In</button>
                        </div>
                    </div>
                </form>
                <!-- /.login-box-body -->
            </div>
            <div>
                <p class="text-center" style="margin-top:20px;">
                    Copyright &copy; 2016 - <?php echo date('Y'); ?>
                </p>
                <div class="text-center">• • •</div>
                <p class="text-center" style="margin-top:16px;font-size: 12px;">
                    Developed by<br><i>Lyndon John™&nbsp;&nbsp;•&nbsp;&nbsp;<a href="https://twitter.com/lyndonjohnv" target="_new"><i class="fa fa-twitter"></i> @lyndonjohn</a></i>
                </p>
            </div>
            <!-- /.login-box -->
        </div>
        <!-- jQuery 2.2.3 -->
        <script src="<?php echo WEB_ROOT; ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="<?php echo WEB_ROOT; ?>bootstrap/js/bootstrap.min.js"></script>
        <!-- PACE -->
        <script src="plugins/pace/pace.min.js"></script>
    </body>
</html>