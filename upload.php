<?php

include dirname(_FILE_)."/aesEncryption.php";
session_start();
$sid=session_id();
$level=NULL;
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 0;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    
        $uploadOk = 1;
    
}
if(isset($_POST["level"])) {
        
        $level=$_POST["level"];
        $uploadOk = 1;
    
}
// Check if file already exists
/*if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}*/
// Check file size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Incorrect".basename($_FILES["fileToUpload"]["name"]);
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        if(encryptFileAndPrepareKeys())
        echo "Success".basename($_FILES["fileToUpload"]["name"]);
    
        
    } else {
        echo "Error".basename($_FILES["fileToUpload"]["name"]);
    }
}

function encryptFileAndPrepareKeys(){
    global $target_dir;
    $eKey = openssl_random_pseudo_bytes(32);
    $aKey = openssl_random_pseudo_bytes(32);
    //File currentFile=fopen($target_dir.basename($_FILES["fileToUpload"]["name"]));
    $myfile = fopen($target_dir.basename($_FILES["fileToUpload"]["name"]), "r+") or die("Unable to open file for read!");
    $fileContent=fread($myfile,filesize($target_dir.basename($_FILES["fileToUpload"]["name"])));
    fclose($myfile);
    $encrypted = ExperimentalAES256DoNotActuallyUse::encrypt($fileContent, $eKey, $aKey);
    $myfile = fopen($target_dir.basename($_FILES["fileToUpload"]["name"]), "r+") or die("Unable to open file for write!");
    fwrite($myfile,$encrypted);
    fclose($myfile);
    
    return encryptSymmetricAndMacKeyUsingRSA($eKey,$aKey);
}

function encryptSymmetricAndMacKeyUsingRSA($sym,$mac){
    $config=array(
        "digest_alg"=>"sha512",
        "private_key_bits"=>4096,
        "private_key_type"=>OPENSSL_KEYTYPE_RSA
    );
    $privKey=NULL;
    $res=openssl_pkey_new($config);
    if($res==false){
        echo "pkey false";
        
    }
    else{
        echo $res;
    }
    openssl_pkey_export($res,$privKey);
    $pubKey=openssl_pkey_get_details($res);
    $pubKey=$pubKey["key"];
    
    $encryptedSymmetricKey=";
    $encryptedMacKey=";
    openssl_public_encrypt($sym,$encryptedSymmetricKey,$pubkey);
    openssl_public_encrypt($mac,$encryptedMacKey,$pubkey);
    return storeInDatabase($encryptedSymmetricKey,$encryptedMacKey,$privKey);
}

function storeInDatabase($esk,$emk,$pk){
    global $level;
    $con=mysqli_connect("ap-cdbr-azure-southeast-a.cloudapp.net:3306","b8fdb7a0f430b6","3770357f","datacomm");
    if (mysqli_connect_errno($con))
    {
       die();
    }
    $fn=basename($_FILES["fileToUpload"]["name"]);
    
    $result = mysqli_query($con,"SELECT * FROM cloudfiles where filename='$fn';");
    $row = mysqli_fetch_array($result);
    if($row){
        $res=mysqli_query($con,"UPDATE cloudfiles set level='$level',esk='$esk',emk='$emk',sk='$pk' where filename='$fn';");
        if($res){
            return true; 
        }

        else{
            return false; 
        }
    }
    else{
        $res=mysqli_query($con,"INSERT INTO cloudfiles values('$fn','$level','$esk','$emk','$pk');");
        if($res){
            return true; 
        }

        else{
            return false; 
        }
    }
}
?>