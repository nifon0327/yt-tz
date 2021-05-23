<?php 
$iPhoneTag = "yes";
include "../../basic/parameter.inc";
	include "../../model/kq_YearHolday.php";
  switch($dModuleId){
       case "1190":
		    $carArray=array();
		    $mySql = "select Id,CarNo FROM $DataPublic.cardata WHERE Estate=1  AND UserSign IN (1,2) order by Id";
		    $myResult = mysql_query($mySql);
		    if($myRow = mysql_fetch_assoc($myResult))
		     {
		        do{
		            $carArray[] =array("Id"=>$myRow["Id"],"Name"=>$myRow["CarNo"]);		
		        } while($myRow = mysql_fetch_assoc($myResult));
		     }
		     $jsonArray[]=array("Car"=>$carArray);
		      
		     //读取司机信息
		    $driverArray=array();
		    $mySql = "select Number,Name FROM $DataPublic.staffmain WHERE JobId=11 AND Estate=1 order by Number";
		    $myResult = mysql_query($mySql);
		    if($myRow = mysql_fetch_assoc($myResult))
		     {
		        do{
		             $driverArray[]=array("Id"=>$myRow["Number"],"Name"=>$myRow["Name"]);		
		        }while($myRow = mysql_fetch_assoc($myResult));
		     }
		    $driverArray[]=array("Id"=>"0","Name"=>"自驾");	
		    $jsonArray[]=array("Driver"=>$driverArray); 
		break;
	   case "1346":	    
	          $dataArray=array();
	          {
	          	
		          //年、休假信息
		         $YearDays=0;$BxDays=0;
			 	 //年假
				 $YearDays=GetYearHolDays($LoginNumber,date("Y-m-d"),"",$DataIn,$DataPublic,$link_id);
			    $YearDays=$YearDays<0?0:$YearDays;
			      //补休
			      $bxCheckSql = "Select  Sum(hours) as hours From $DataPublic.bxSheet Where Number = '$LoginNumber'";
				  $bxCheckResult =mysql_fetch_array(mysql_query($bxCheckSql,$link_id));
			       $bxHours=$bxCheckResult["hours"]*1;
			       
			     if ($bxHours>0){
				        $usedBxHours=0;
				        $bxQjCheckSql = "Select * From $DataPublic.kqqjsheet Where Number = '$LoginNumber' and Type= '5' AND DATE_FORMAT(StartDate,'%Y')>='2013'";
						$bxQjCheckResult = mysql_query($bxQjCheckSql,$link_id);
						
						while($bxQjCheckRow = mysql_fetch_array($bxQjCheckResult))
						{
							$startTime = $bxQjCheckRow["StartDate"];
							$endTime = $bxQjCheckRow["EndDate"];
							$bcType = $bxQjCheckRow["bcType"];
							$usedBxHours+= GetBetweenDateDays($LoginNumber,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
							
						}
						// echo "$Number ----$bxHours-$usedBxHours </br>";
						$bxHours-=$usedBxHours;
				}
				 $bxDays=$bxHours>0?$bxHours/8:0;
			   
$dataArray[]=array("Id"=>"1","Name"=>"事假");//1 4 5
if ($YearDays>0) {
	$dataArray[]=array('Id'=>'4','Name'=>'带薪年假');
}if ($bxHours>0) {
	$dataArray[]=array('Id'=>'5','Name'=>'补休');
}
	          }
	          /*
	          $myResult = mysql_query("SELECT Id,Name FROM $DataPublic.qjtype  WHERE Estate=1  order by Id",$link_id);
	          while($myRow = mysql_fetch_array($myResult)){
		              $Id=$myRow["Id"];
		               $Name=$myRow["Name"];
		               $dataArray[]=array("Id"=>"$Id","Name"=>"$Name");
	          }
	          */
	           $jsonArray[]=array("Type"=>$dataArray); 
	    break;
	    case "1060":	    
	          $dataArray=array();
	          $myResult = mysql_query("SELECT TypeId,Name FROM $DataPublic.adminitype WHERE Estate=1 order by Id",$link_id);
	          while($myRow = mysql_fetch_array($myResult)){
		              $Id=$myRow["TypeId"];
		               $Name=$myRow["Name"];
		               $dataArray[]=array("Id"=>"$Id","Name"=>"$Name");
	          }
	           $jsonArray[]=array("Type"=>$dataArray); 
	        
	        $currencyArray=array();   
	         $myResult = mysql_query("SELECT Id,Name,Symbol FROM $DataPublic.currencydata WHERE Estate=1 AND Id<5 order by Id",$link_id);
	          while($myRow = mysql_fetch_array($myResult)){
		              $Id=$myRow["Id"];
		               $Name=$myRow["Symbol"];
		               $currencyArray[]=array("Id"=>"$Id","Name"=>"$Name");
	          }
	           $jsonArray[]=array("Currency"=>$currencyArray);   
	    break;
	    case "1402":
	        $MenuArray=array();$dataArray=array();
		    $mySql="SELECT A.Id,A.Name,A.CtId,A.Price,B.Name AS CTName 
		     FROM $DataPublic.ct_menu A 
		     LEFT JOIN $DataPublic.ct_data B ON B.Id=A.CtId
		    WHERE A.Estate=1 ORDER BY A.CtId DESC,A.Id DESC";
		    $myResult = mysql_query($mySql);
		    if($myRow = mysql_fetch_assoc($myResult))
		     { 
		         $oldCtId=$myRow["CtId"];
		         $CTName=$myRow["CTName"];
		        do{
		            $CtId=$myRow["CtId"];	
		            if ($CtId!=$oldCtId){
			             $dataArray[]=array("Id"=>"$oldCtId","Name"=>"$CTName","Menu"=>$MenuArray);
			             $oldCtId=$CtId;
			             $CTName=$myRow["CTName"];
			             $MenuArray=array();
		            }
		            $MenuId=$myRow["Id"];
		            $MenuName=$myRow["Name"];	
		            $Price=$myRow["Price"];
		            $MenuArray[]=array("Id"=>"$MenuId","Name"=>"$MenuName","Price"=>"$Price");
		        } while($myRow = mysql_fetch_assoc($myResult));
		        $dataArray[]=array("Id"=>"$oldCtId","Name"=>"$CTName",$MenuArray);
		     }
		      $jsonArray[]=array("Menu"=>$dataArray);   
	    break;
  }
?>