<?php
/*
*Author: Kevin Barasa
*Phone : +254724778017
*Email : kevin.barasa001@gmail.com
*/
error_reporting(0);
  // Reads the variables sent via POST from our gateway
  $sessionId   = $_POST["sessionId"];
  $serviceCode = $_POST["serviceCode"];
  $phoneNumber = $_POST["phoneNumber"];
  $text        = $_POST["text"];
  //create data fields  
  $pNumber="";  
  $shopNumber="";   
  
  $level =0;  
  
  if($text != ""){  
  $text=  str_replace("#", "*", $text);  
  $text_explode = explode("*", $text);  
  $level = count($text_explode);  
  }  
  
if ($level==0){  
    $response  = "CON Welcome to Trash Royalities \n";
    $response .= "1. Report Trash \n";
    $response .= "2. Register";  
    //End of the level 
    } 


  if ($level>0){  
      switch ($text_explode[0])  {  
          case 1:  
              displayReport($text_explode, $phoneNumber);
          case 2:  
              displayRegister($text_explode, $phoneNumber);
          break;  
      }  
  } 
  
  function displayReport(){  
       $ussd_text  = "CON Which kind of trash is it? \n1. Biogradable \n2. Non-Biogradable";
       ussd_proceed($ussd_text);  
       exit();
  }
  function displayRegister(){  
       $ussd_text  = "CON 1. Fill registration details";
       ussd_proceed($ussd_text);  
       exit();
  }
  function ussd_proceed ($ussd_text){  
      echo $ussd_text;  
  }
  
  //Display Pads 
  function nest_register($details,$pNumber){  
    if (count($details)==1){  
      $ussd_text= displayRegister();  
      ussd_proceed($ussd_text);  
      } 
      else if (count($details)==2){  
      $ussd_text="CON \n Full Name";  
      ussd_proceed($ussd_text);  
      } 
      else if (count($details)==3){  
      $ussd_text="CON \n ID Number";  
      ussd_proceed($ussd_text);  
      }
      else if (count($details)==4){  
      $ussd_text="CON \n Region of Nairobi";  
      ussd_proceed($ussd_text);  
      }  
      else if(count($details) == 5){  
      $ussd_text = "CON \n1. Finish Registration \n2. Abort";  
      ussd_proceed($ussd_text);  
      }
      else if(count($details) == 4){  
      $choice=$details[1]; 
      $fullName=$details[2];
      $IDNumber=$details[3];
      $region=$details[4];  
      $retval = $details[5];
      if($choice=="1"){  
      $item="Chose to register";  
      }  
      if($retval=="1"){  
      //=================Do your business logic here=========================== 
        include 'database.php'; 
        $pdo = Database::connect();
        $sql = "SELECT * FROM kiosks WHERE `agent_no` = '$shopNumber'";
        $check = $pdo->query($sql);
        $found = count($check);
        if ($found) {
            foreach ($check as $row) {
                $shop = $row['name'];
                $phone = $row['phone'];
              //Remember to put "END" at the start of each echo statement that comes here  
              echo "END You have registered with: \n Name:".$fullName." \nID:".$IDNumber."\nRegion ". $region ." \n\n Feel free to use Trash Royalities";  
              include('messaging.php');
            } 
        } 
        //if (!$found){echo "END The Agent Number doesn't exist!";}
        Database::disconnect();
      }else{
      //Choice is cancel  
      $ussd_text = "END You have cancelled the registration";  
      
      ussd_proceed($ussd_text);  
      }
  } 
  exit();
  } 
  
  // Print the response onto the page so that our gateway can read it
  header('Content-type: text/plain');
  echo $response;
  // DONE!!!
  ?>