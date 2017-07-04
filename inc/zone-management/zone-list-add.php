<?php
include('../../constants.php');
include('../../db.php');
if(isset($_FILES['addzone'])){
  $errors= array();
  $file_name = $_FILES['addzone']['name'];
  $file_size =$_FILES['addzone']['size'];
  $file_tmp =$_FILES['addzone']['tmp_name'];
  $file_type=$_FILES['addzone']['type'];
  $file_ext=strtolower(end(explode('.',$_FILES['addzone']['name'])));
  
  $expensions= array("kml");
  $temp = explode(".", $_FILES["file"]["name"]);
  $newfilename = 'StoreFinderMapnew.kml';
  if(in_array($file_ext,$expensions)=== false){
     $errors[]="extension not allowed, please choose a kml file.";
  }
  
  /*if($file_size > 2097152){
     $errors[]='File size must be excately 2 MB';
  }*/
	
  if(empty($errors)==true){
     move_uploaded_file($file_tmp,"../../".$newfilename);
     Header( 'Location: http://mycloudsportal.com/app/index.php?success=1');
  }else{
     print_r($errors);
  }
}
?>