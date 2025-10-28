<?php
$conn=mysqli_connect("localhost","root","","esports_manager");
 if(!$conn){
    die("liwata");
 }
 $username=$_POST ['username'];
 $password=$_POST ['password'];
 $sql= "SELECT * FROM login WHERE username='$username' AND password='$password'";
 $result=mysqli_query($conn,$sql);
  if(mysqli_num_rows($result) >0){
    header("location: home.php");
    exit();

  }else{
    echo"login failed :(";
  }
  mysqli_close($conn);
  ?>