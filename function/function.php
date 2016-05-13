<?php 
session_start();
include "koneksi.php";

$id = $_GET['id'];

switch ($id) {
	case $id; $id(); break;
	default; echo "belum diswitch atau belum ada fungsinya"; //Pernyataaan false
}

	function login(){
		
		$email = $_POST['email'];
		$password = $_POST['password'];
		//$pass = md5($bpass);
		//$coo = "uiQfmqYYkzhQUeYQDsv6LBIBhjEMRfU6wP55qjYEGTA1SJZ3K4RJJU1b";//lokal
		$coo = "Ds9suRCBX4c3d7JOKodnJQQih92hCzHwvvP0HSTQbjnp8hJmU0SRsTZv";//inet
		$encpass = md5($coo."".$password);
		$sql = mysql_query("SELECT * FROM ps_employee WHERE email='$email' && passwd='$encpass'");
		
		
		$num = mysql_num_rows($sql);
			if ($num == 1) {
				while($data = mysql_fetch_array($sql)){
					$_SESSION['email'] = $data['email'];
					$_SESSION['is_login'] = 1;
				}
				if(isset($_SESSION['error'])){
					unset($_SESSION['error']);
				}else{}
				header("Location: ../pages/home.php");
			}else{
				$_SESSION['error'] = 1;
				header("Location: ../index.php");	
			}
	}
	
	function logout(){
		
		unset($_SESSION['email']);
		unset($_SESSION['is_login']);
		unset($_SESSION['error']);
		header("Location: ../index.php");
	}
	
?>