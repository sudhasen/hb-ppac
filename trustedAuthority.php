<?php
$con=mysqli_connect("ap-cdbr-azure-southeast-a.cloudapp.net:3306","b8fdb7a0f430b6","3770357f","datacomm");
if (mysqli_connect_errno($con))
{
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$filename = $_POST['filename'];
$acps = $_POST['acps'];
$unblind = $_POST['unblind'];
$flag=0;
if(!isset($_POST['filename'])||!isset($_POST['acps'])||!isset($_POST['unblind'])){
    $flag=2;
}else{
    $result = mysqli_query($con,"SELECT * FROM policyconfig where filename='$filename';");
    $row = mysqli_fetch_array($result);

    if($row){
        $flag=0;
    }
    else{
        $flag=1;
    }
    
}
if($flag==2){
    echo 'missing terms';
}
elseif($flag==0){
        echo 'exist';
}
else{
    $res=mysqli_query($con,"INSERT INTO policyconfig values('$filename','$acps','$unblind');");
    if($res){
        echo 'success'; 
    }

    else{
        echo 'error' ; 
    }
}
mysqli_close($con);
?>