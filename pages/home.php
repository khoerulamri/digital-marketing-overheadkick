<?php
	session_start();
	if(!isset($_SESSION['is_login'])){
		header("Location: ../index.php");
	}
	include "../function/koneksi.php";
include "p_header.php";
include "p_menu.php";
?>

 
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
             Dashboard
            <small>Control panel</small>
          </h1>
          <ol class="breadcrumb">
            <li class="active"><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
			<li class="active">Dashboard</li>
          </ol>
        </section>
      </div><!-- /.content-wrapper -->
<?php
include "p_footer.php"
?>
