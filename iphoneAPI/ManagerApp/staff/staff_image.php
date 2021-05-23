<?php 
//员工资料明细
$checkRow=mysql_fetch_array(mysql_query("SELECT C.OutIP,M.Name     
	                        FROM  $DataPublic.staffmain M 
	                        LEFT JOIN $DataPublic.companys_group C ON C.cSign=M.cSign  WHERE  M.Number='$Number'  LIMIT 1",$link_id));
$OutIP=$checkRow["OutIP"];
$Name =$checkRow["Name"];	                        

switch($sModuleId){
     case "oe":
	        $Staff_URL="http://$OutIP/download/tjfile/";
	       $mySql="SELECT Attached FROM $DataPublic.staff_tj WHERE Number='$Number' ";
	       $Result = mysql_query($mySql,$link_id);
			while($myRow = mysql_fetch_array($Result)) { 
				 $Attached=$Staff_URL .$myRow["Attached"];
				 $jsonArray[] = array(  "Name"=>"$Name","Text"=>"","URL"=>"$Attached");
			}
       break;
    case "cert":
	       $mySql="SELECT S.IdcardPhoto,S.DriverPhoto,S.PassPort,S.PassTicket,C.OutIP,M.Name     
	                        FROM $DataPublic.staffsheet S  
	                        LEFT JOIN  $DataPublic.staffmain M ON M.Number=S.Number 
	                        LEFT JOIN $DataPublic.companys_group C ON C.cSign=M.cSign  WHERE  S.Number='$Number'  LIMIT 1";
			$Result = mysql_query($mySql,$link_id);
			 if($myRow = mysql_fetch_array($Result)) { 
			      
			      $Staff_URL="http://$OutIP/download/staffPhoto/";
			      
			      if ($myRow["IdcardPhoto"]==1){
			          $Attached=$Staff_URL . "c".$Number.".jpg";
				      $jsonArray[] = array( "Name"=>"$Name","Text"=>"身份证","URL"=>"$Attached");
			      }
			      
			      if ($myRow["DriverPhoto"]==1){ //驾驶证
			         $Attached=$Staff_URL ."D".$Number.".jpg";
				      $jsonArray[] = array(  "Name"=>"$Name","Text"=>"驾驶证","URL"=>"$Attached");
			     }
			    
			     if ($myRow["PassPort"]==1){//护照PassPort
			         $Attached=$Staff_URL ."PP".$Number.".jpg";
				      $jsonArray[] = array(  "Name"=>"$Name","Text"=>"护照","URL"=>"$Attached");
			     }
			 	if ($myRow["PassTicket"]==1){	//通行证PassTicket
			 	     $Attached=$Staff_URL ."PT".$Number.".jpg";
				      $jsonArray[] = array(  "Name"=>"$Name","Text"=>"通行证","URL"=>"$Attached");
			 	 }
			 }
       break;
       
       case "papers":
	        $Staff_URL="http://$OutIP/download/Certificate/";
	       $mySql="SELECT Picture FROM $DataPublic.staff_Certificate WHERE Number='$Number' ";
	       $Result = mysql_query($mySql,$link_id);
			while($myRow = mysql_fetch_array($Result)) { 
				 $ImgName=$myRow["Picture"];
				 $Attached=$Staff_URL .$ImgName;
				 $jsonArray[] = array(  "Name"=>"$Name","Text"=>"","URL"=>"$Attached");
			}
       break;
       
}
?>