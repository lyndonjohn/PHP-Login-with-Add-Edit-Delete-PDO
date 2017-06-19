<?php
require_once("../config/session.php");
require_once("../config/class.user.php");
$auth_user = new USER();
$user_id = $_SESSION['user_session'];
# get logged in user details through session id
$stmt = $auth_user->runQuery("SELECT username, useremail, dateadded FROM users WHERE id=:user_id");
$stmt->execute(array(":user_id"=>$user_id));
$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
$lu_uname = $userRow['username'];
$lu_email = $userRow['useremail'];
$lu_date  = $userRow['dateadded'];
$date = new DateTime($lu_date);

# for adding user
if(isset($_POST['btn-add']))
{
	# check if user is logged-in
	if(!$auth_user->is_loggedin())
	{
		# redirect to login page, gtfo
		$auth_user->doLogout();
	}
	else
	{
		$uname  = strval($_POST['uname']);
		$uemail = strval($_POST['uemail']);
		$upass  = strval($_POST['upass']);
		# check duplicate username
		$stmt=$auth_user->runQuery('SELECT username FROM users WHERE username=:uname');
		$stmt->execute(array(':uname'=>$uname));
		if($stmt->rowCount()==1)
		{
			$error='Username already exists.';
		}
		if(!isset($error))
		{
			try
			{
				if($auth_user->register($uname,$uemail,$upass))
				{
					$auth_user->redirect('?users&confirm');
				}
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}
		}
	}
}

# for updating user
if(isset($_POST['btn-update']))
{
	# check if user is logged-in
	if(!$auth_user->is_loggedin())
	{
		# redirect to login page, gtfo
		$auth_user->doLogout();
	}
	else
	{
		$id     = intval($_POST['id']);
		$uname  = strval($_POST['uname']);
		$uemail = strval($_POST['uemail']);
		# check duplicate username
		$stmt=$auth_user->runQuery('SELECT username FROM users WHERE username=:uname AND id!=:id');
		$stmt->execute(array(':uname'=>$uname, ':id'=>$id));
		if($stmt->rowCount()==1)
		{
			$error='Username already exists.';
		}
		if(!isset($error))
		{
			try
			{
				if($auth_user->update_user($uname,$uemail,$id))
				{
					$auth_user->redirect('?users&update');
				}
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}
		}
	}
}

# for password update
if(isset($_POST['btn-password']))
{
	# check if user is logged-in
	if(!$auth_user->is_loggedin())
	{
		# redirect to login page, gtfo
		$auth_user->doLogout();
	}
	else
	{
		$id     = intval($_POST['id']);
		$upass  = strval($_POST['upass']);

		try
		{
			if($auth_user->updatepassword($id,$upass))
			{
				$auth_user->redirect('?users&update');
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
}

# for deleting user
if(isset($_GET['userID']))
{
	# check if user is logged-in
	if(!$auth_user->is_loggedin())
	{
		# redirect to login page, gtfo
		$auth_user->doLogout();
	}
	else
	{
		$id = intval($_GET['userID']);

		try
		{
			if($auth_user->delete_user($id))
			{
				$auth_user->redirect('?users&deleted');
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>AdminLTE 2 | Users</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.6 -->
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>bootstrap/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/font-awesome-4.7.0/css/font-awesome.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/ionicons-2.0.1/css/ionicons.css">
		<!-- DataTables -->
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/datatables/dataTables.bootstrap.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/AdminLTE.min.css">
		<!-- AdminLTE Skins. Choose a skin from the css/skins
		folder instead of downloading all of them to reduce the load. -->
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/skins/_all-skins.min.css">
		<!-- Pace style -->
		<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/pace/pace.css">
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<div class="wrapper">
			<?php include ($_SERVER["DOCUMENT_ROOT"] . '/addeditdelete/header.php'); ?>
			<?php include ($_SERVER["DOCUMENT_ROOT"] . '/addeditdelete/sidebar.php'); ?>
			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>
					List of Users
					<small>List of system admin &nbsp;&nbsp; <button type="button" class="btn btn-primary btn-flat btn-sm" data-toggle="modal" data-target=".bs-example-modal-md"><i class="fa fa-user-plus"></i> Create New</button></small>
					</h1>
					<ol class="breadcrumb">
						<li><a href="<?php echo WEB_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
						<li class="active">List of Users</li>
					</ol>
				</section>
				<!-- Main content -->
				<section class="content">
					<div class="row">
						<div class="col-xs-12">
							<div class="box">
								<div class="box-body">
									<?php
										if(isset($error))
										{
									?>
									<div class="alert alert-warning alert-dismissible flat">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										<h4><i class="icon fa fa-warning"></i> Warning</h4>
										<?php echo $error; ?>
									</div>
									<?php } else if(isset($_GET['confirm'])) { ?>
									<div class="alert alert-success alert-dismissible flat">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										<h4><i class="icon fa fa-check"></i> Confirmation</h4>
										New user added.
									</div>
									<?php } else if(isset($_GET['update'])) { ?>
									<div class="alert alert-info alert-dismissible flat">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										<h4><i class="icon fa fa-check"></i> Confirmation</h4>
										User updated.
									</div>
									<?php } else if(isset($_GET['deleted'])) { ?>
									<div class="alert alert-info alert-dismissible flat">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										<h4><i class="icon fa fa-check"></i> Confirmation</h4>
										User deleted.
									</div>
									<?php } ?>
									<table id="example1" class="table table-bordered table-striped">
										<thead>
											<tr>
												<th>Username</th>
												<th>Email</th>
												<th>Date</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$x=0;
												$stmt=$auth_user->runQuery('SELECT id, username, useremail, dateadded FROM users WHERE deleted=:val AND tag=:val1');
												$stmt->execute(array(':val'=>0, ':val1'=>0));
												while($row=$stmt->fetch(PDO::FETCH_ASSOC))
												{
													$x++;
													$dateAdded = new DateTime($row['dateadded']);
													$dateadded = $dateAdded->format('M. d, Y');
											?>
											<tr>
												<td><?php echo $row['username']; ?></td>
												<td><?php echo $row['useremail']; ?></td>
												<td><?php echo $dateadded; ?></td>
												<td>
													<button type="button" class="btn btn-primary btn-flat" title="edit record" data-toggle="modal" data-target=".bs-example-modal-md<?php echo $x; ?>"/><i class="fa fa-edit"></i> Edit</button>
													&nbsp;
													<button type="button" class="btn btn-danger btn-flat" onClick="window.location.href='javascript:deleteuser(<?php echo $row['id']; ?>);'"><i class="fa fa-trash"></i> Delete</button>
													<!-- modal -->
													<div class="modal fade bs-example-modal-md<?php echo $x; ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
														<div class="modal-dialog modal-md" role="document">
															<div class="modal-content">
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																	<h4 class="modal-title" id="gridSystemModalLabel"><i class="ion ion-compose"></i> Edit User</h4>
																</div>
																<div class="modal-body">
																	<form data-toggle="validator" role="form" method="POST" enctype="multipart/form-data">
																		<div class="box-body">
																			<div style="margin-top:8px;margin-right:10px;" class="form-group">
																				<label>Username</label>
																				<div class="clearfix"></div>
																				<input style="width: 250px;" required data-minlength="6" type="text" class="form-control" name="uname" placeholder="Enter Username" value="<?php echo $row['username']; ?>">
																			</div>
																			<div style="margin-top:8px;margin-right:10px;" class="form-group">
																				<label>Email</label>
																				<div class="clearfix"></div>
																				<input style="width: 250px;" required type="text" class="form-control" name="uemail" placeholder="Enter Email" value="<?php echo $row['useremail']; ?>">
																			</div>
																		</div>

																		<div class="box-body">
																			<button type="submit" name="btn-update" class="btn btn-primary btn-flat btn-sm">Save Changes</button>
																		</div>
																		<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
																		<!-- /.box-body -->
																	</form>
																</div>
																<div style="margin-top: -20px;" class="modal-body">
																<form data-toggle="validator" role="form" method="POST">
																	<div class="box-body">
																		<i class="text-muted">*Leave Password blank if you dont want changes. Password must not less than 6 characters.</i>

																		<div style="margin-top:8px;margin-right:10px;" class="form-group">
																			<label for="inputPassword">Password</label>
																			<div class="clearfix"></div>
																			<input required style="width: 250px;" data-minlength="6" type="password" class="form-control" name="upass" id="inputPassword<?php echo $x; ?>" placeholder="Enter New Password">
																		</div>
																		<div style="margin-top:8px;margin-right:10px;" class="form-group">
																			<label for="inputPasswordConfirm">Confirm Password</label>
																			<div class="clearfix"></div>
																			<input required style="width: 250px;" type="password" class="form-control" id="inputPasswordConfirm" data-match-error="Whoops, passwords don't match" data-match="#inputPassword<?php echo $x; ?>" placeholder="Retype Password">
																		</div>
																	</div>
																	<div class="box-body">
																		<button type="submit" name="btn-password" class="btn btn-primary btn-flat btn-sm">Update Password</button>
																	</div>
																	<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
																</form>
																</div>
															</div>
														</div>
													</div>
													<!-- /end modal -->
												</td>
											</tr>
											<?php } ?>
										</tbody>
										<tfoot>
										<tr>
											<th>Username</th>
											<th>Email</th>
											<th>Date</th>
											<th>Action</th>
										</tr>
										</tfoot>
									</table>
								</div>
								<!-- /.box-body -->
								<!-- modal -->
								<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
									<div class="modal-dialog modal-md" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="gridSystemModalLabel"><i class="ion ion-ios-plus"></i> Create New</h4>
											</div>
											<div class="modal-body">
												<form data-toggle="validator" role="form" method="POST">
													<div class="box-body">
														<div class="col-md-6">
															<div class="form-group">
																<label>Username</label>
																<input required data-minlength="6" style="text-transform: lowercase;" type="text" class="form-control" name="uname" placeholder="Enter Username (not < 6 characters)">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label>Email</label>
																<input required data-minlength="6" style="text-transform: lowercase;" type="email" class="form-control" name="uemail" placeholder="Enter Email">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="confirmPassword1">Password</label>
																<input required data-minlength="6" type="password" class="form-control" name="upass" id="confirmPassword1" placeholder="Enter Password (not < 6 characters)">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="exampleInputPassword">Confirm Password</label>
																<input data-match="#confirmPassword1" required data-minlength="6" type="password" class="form-control" name="upass" placeholder="Retype Password">
															</div>
														</div>
													</div>
													<div class="box-footer">
														<button type="submit" name="btn-add" class="btn btn-primary btn-flat">Create</button>
													</div>
													<!-- /.box-body -->
												</form>
											</div>
										</div>
									</div>
								</div>
								<!-- /end modal -->
							</section>
						</div>
						<!-- /.box -->
					</div>
					<!-- /.col -->
				</div>
				<!-- /.row -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Version</b> 2.3.8
			</div>
			<strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights
			reserved.
		</footer>
		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Create the tabs -->
			<ul class="nav nav-tabs nav-justified control-sidebar-tabs">
				<li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
				<li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<!-- Home tab content -->
				<div class="tab-pane" id="control-sidebar-home-tab">
					<h3 class="control-sidebar-heading">Recent Activity</h3>
					<ul class="control-sidebar-menu">
						<li>
							<a href="javascript:void(0)">
								<i class="menu-icon fa fa-birthday-cake bg-red"></i>
								<div class="menu-info">
									<h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
									<p>Will be 23 on April 24th</p>
								</div>
							</a>
						</li>
						<li>
							<a href="javascript:void(0)">
								<i class="menu-icon fa fa-user bg-yellow"></i>
								<div class="menu-info">
									<h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>
									<p>New phone +1(800)555-1234</p>
								</div>
							</a>
						</li>
						<li>
							<a href="javascript:void(0)">
								<i class="menu-icon fa fa-envelope-o bg-light-blue"></i>
								<div class="menu-info">
									<h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>
									<p>nora@example.com</p>
								</div>
							</a>
						</li>
						<li>
							<a href="javascript:void(0)">
								<i class="menu-icon fa fa-file-code-o bg-green"></i>
								<div class="menu-info">
									<h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>
									<p>Execution time 5 seconds</p>
								</div>
							</a>
						</li>
					</ul>
					<!-- /.control-sidebar-menu -->
					<h3 class="control-sidebar-heading">Tasks Progress</h3>
					<ul class="control-sidebar-menu">
						<li>
							<a href="javascript:void(0)">
								<h4 class="control-sidebar-subheading">
								Custom Template Design
								<span class="label label-danger pull-right">70%</span>
								</h4>
								<div class="progress progress-xxs">
									<div class="progress-bar progress-bar-danger" style="width: 70%"></div>
								</div>
							</a>
						</li>
						<li>
							<a href="javascript:void(0)">
								<h4 class="control-sidebar-subheading">
								Update Resume
								<span class="label label-success pull-right">95%</span>
								</h4>
								<div class="progress progress-xxs">
									<div class="progress-bar progress-bar-success" style="width: 95%"></div>
								</div>
							</a>
						</li>
						<li>
							<a href="javascript:void(0)">
								<h4 class="control-sidebar-subheading">
								Laravel Integration
								<span class="label label-warning pull-right">50%</span>
								</h4>
								<div class="progress progress-xxs">
									<div class="progress-bar progress-bar-warning" style="width: 50%"></div>
								</div>
							</a>
						</li>
						<li>
							<a href="javascript:void(0)">
								<h4 class="control-sidebar-subheading">
								Back End Framework
								<span class="label label-primary pull-right">68%</span>
								</h4>
								<div class="progress progress-xxs">
									<div class="progress-bar progress-bar-primary" style="width: 68%"></div>
								</div>
							</a>
						</li>
					</ul>
					<!-- /.control-sidebar-menu -->
				</div>
				<!-- /.tab-pane -->
				<!-- Stats tab content -->
				<div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
				<!-- /.tab-pane -->
				<!-- Settings tab content -->
				<div class="tab-pane" id="control-sidebar-settings-tab">
					<form method="post">
						<h3 class="control-sidebar-heading">General Settings</h3>
						<div class="form-group">
							<label class="control-sidebar-subheading">
								Report panel usage
								<input type="checkbox" class="pull-right" checked>
							</label>
							<p>
								Some information about this general settings option
							</p>
						</div>
						<!-- /.form-group -->
						<div class="form-group">
							<label class="control-sidebar-subheading">
								Allow mail redirect
								<input type="checkbox" class="pull-right" checked>
							</label>
							<p>
								Other sets of options are available
							</p>
						</div>
						<!-- /.form-group -->
						<div class="form-group">
							<label class="control-sidebar-subheading">
								Expose author name in posts
								<input type="checkbox" class="pull-right" checked>
							</label>
							<p>
								Allow the user to show his name in blog posts
							</p>
						</div>
						<!-- /.form-group -->
						<h3 class="control-sidebar-heading">Chat Settings</h3>
						<div class="form-group">
							<label class="control-sidebar-subheading">
								Show me as online
								<input type="checkbox" class="pull-right" checked>
							</label>
						</div>
						<!-- /.form-group -->
						<div class="form-group">
							<label class="control-sidebar-subheading">
								Turn off notifications
								<input type="checkbox" class="pull-right">
							</label>
						</div>
						<!-- /.form-group -->
						<div class="form-group">
							<label class="control-sidebar-subheading">
								Delete chat history
								<a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
							</label>
						</div>
						<!-- /.form-group -->
					</form>
				</div>
				<!-- /.tab-pane -->
			</div>
		</aside>
		<!-- /.control-sidebar -->
		<!-- Add the sidebar's background. This div must be placed
		immediately after the control sidebar -->
		<div class="control-sidebar-bg"></div>
	</div>
	<!-- ./wrapper -->
	<!-- jQuery 2.2.3 -->
	<script src="<?php echo WEB_ROOT; ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
	<!-- Bootstrap 3.3.6 -->
	<script src="<?php echo WEB_ROOT; ?>bootstrap/js/bootstrap.min.js"></script>
	<!-- PACE -->
	<script src="<?php echo WEB_ROOT; ?>plugins/pace/pace.min.js"></script>
	<!-- DataTables -->
	<script src="<?php echo WEB_ROOT; ?>plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo WEB_ROOT; ?>plugins/datatables/dataTables.bootstrap.min.js"></script>
	<!-- SlimScroll -->
	<script src="<?php echo WEB_ROOT; ?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="<?php echo WEB_ROOT; ?>plugins/fastclick/fastclick.js"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo WEB_ROOT; ?>dist/js/app.min.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="<?php echo WEB_ROOT; ?>dist/js/demo.js"></script>
	<script src="<?php echo WEB_ROOT; ?>dist/js/validator.js"></script>
	<!-- page script -->
	<script>
	$(function () {
	$("#example1").DataTable();
	$('#example2').DataTable({
	"paging": true,
	"lengthChange": false,
	"searching": false,
	"ordering": true,
	"info": true,
	"autoWidth": false
	});
	});
	</script>
	<script>
	function deleteuser(userID)
	{
	if (confirm('Delete this user?')) {
	window.location.href = 'index.php?userID=' + userID;
	}
	}
	</script>
	<script>
	$(document).ready(function(){
		setTimeout(function(){
			$(".alert").fadeOut("slow", function () {
			$(".alert").remove();
		});
		}, 5000);
	});
	</script>
</body>
</html>