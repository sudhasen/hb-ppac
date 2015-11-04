<?php
$con=mysqli_connect("ap-cdbr-azure-southeast-a.cloudapp.net:3306","b8fdb7a0f430b6","3770357f","datacomm");
if (mysqli_connect_errno($con))
{
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$username = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];
$level = $_POST['level'];
$flag=0;
$result = mysqli_query($con,"SELECT * FROM users where email='$username';");
$row = mysqli_fetch_array($result);

if($row){
    $flag=0;
}
else{
    $flag=1;
}
if($flag==0){
    echo 'exist';
}
else{
    $res=mysqli_query($con,"INSERT INTO users values(0,'$username','$password','$level','$role');");
    if($res){
        echo 'success'; 
    }

    else{
        echo 'error' ; 
    }
}
mysqli_close($con);
?>