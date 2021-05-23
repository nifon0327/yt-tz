<?
//车辆费用记录


$BundleId = 'AshCloudApp';
$Device = 'iphoneAPI';
$appVersion = $AppVersion;
$segment = 'staff';
$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];

$uri = 'staff/staff_list_carfee';
				          
				          
$sql = "INSERT INTO `ac`.`app_userlog`
(
`BundleId`,
`Device`,
`Version`,
`IP`,
`Segment`,
`Uri`,
`creator`,
`Parameter`)
VALUES
(
'$BundleId',
'$Device',
'$appVersion',
'$user_IP',
'$segment',
'$uri',
'$LoginNumber',
'readNumber=>$Number');
";


mysql_query($sql);


$checkCarSql=mysql_query("SELECT C.Id FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.cardata C ON C.User=M.Name WHERE M.Number='$Number'",$link_id);
 if($checkCarRow = mysql_fetch_array($checkCarSql)) {
           $CarId=$checkCarRow["Id"];
			$SearchRows="";
			switch($sModuleId){
			      case "CarMaintain": $SearchRows=" AND S.TypeId IN (5,6,7)";break;
			      case "CarFine": $SearchRows=" AND S.TypeId =2 ";break; 
			      case "ETC": $SearchRows=" AND S.TypeId IN (3,8) ";break; 
			      case "Refuel": $SearchRows=" AND S.TypeId =4 ";break; 
			      break;
			}
			$mySql="SELECT S.Content,S.Amount,S.Bill,S.Date,S.Estate,T.Name AS TypeName,C.PreChar,D.CarNo  
			 	FROM $DataIn.carfee S 
				LEFT JOIN $DataPublic.carfee_type T ON S.TypeId=T.Id
			     LEFT JOIN $DataPublic.cardata  D ON D.Id=S.CarId 
				LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
				WHERE  S.CarId='$CarId' AND S.Estate IN(0,3)  $SearchRows order by S.Date DESC";
			//echo $mySql;
			  $myResult = mysql_query($mySql);
			  while($myRow = mysql_fetch_assoc($myResult))
			 {
					     $Content=$myRow["Content"];
					     $CarNo=$myRow["CarNo"];
					     $Date=$myRow["Date"];
					     $PreChar=$myRow["PreChar"];
					     $Amount=number_format($myRow["Amount"]);
				        
				         $jsonArray[]=array("Title"=>"$CarNo",
												        "Col1"=>"$PreChar$Amount",
												        "Col2"=>"$Date",
												        "Remark"=>"$Content"
				        );
			    }
  }
?>