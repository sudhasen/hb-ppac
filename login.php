<?php
$con=mysqli_connect("ap-cdbr-azure-southeast-a.cloudapp.net:3306","b8fdb7a0f430b6","3770357f","datacomm");
if (mysqli_connect_errno($con))
{
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$username = $_POST['email'];
$password = $_POST['password'];
//$password=$password;
$result = mysqli_query($con,"SELECT * FROM users where 
email='$username' and password='$password'");
$row = mysqli_fetch_array($result);

if($row){
	$data=array('level'=>$row[3],'role'=>$row[4]);    
	$json=json_encode($data);
	echo $json;
}
else
echo 'incorrect';
mysqli_close($con);
?>