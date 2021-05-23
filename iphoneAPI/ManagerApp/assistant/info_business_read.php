<?php 
//读取出差登记信息
$mySql="SELECT I.Id,I.StartTime,I.EndTime,C.CarNo,M.Name AS Drivers,I.Remark,I.Date,I.Businesser 
FROM $DataPublic.info1_business I 
LEFT JOIN $DataPublic.cardata C ON C.Id=I.CarId 
LEFT JOIN $DataPublic.staffmain  M ON M.Number=I.Drivers 
WHERE  I.EndTime='0000-00-00 00:00:00' OR I.EndTime>NOW() 
ORDER BY I.Id DESC";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do 
            {	
                    $Id=$myRow["Id"];
                    $Businesser=$myRow["Businesser"];
	                $StartTime=$myRow["StartTime"];
	                $Year=substr($StartTime, 0,4);
                    $StartTime= substr($StartTime, 5,11);
                     $EndTime=$myRow["EndTime"];
                     $EndTime= substr($EndTime, 5,11);
                    $Remark=$myRow["Remark"];
                    $CarNo=$myRow["CarNo"];
                    $Drivers=$myRow["Drivers"];
                    
                    $Drivers=$myRow["Drivers"]==""?"自驾":$Drivers;
                    $Drivers=$Drivers==$Businesser?" 自驾":$Drivers;
                    
                    $jsonArray[] = array( "Id"=>"$Id",
                    "Year"=>"$Year",
                     "Range"=>array("T0"=>"$StartTime","T1"=>"$EndTime"),
                      "Driver"=>"$Drivers",
                      "Car"=> "$CarNo",
                      "Estate"=> "0",
                      "Remark"=>"$Remark",
                      "Accompany"=>array());
            }
            while($myRow = mysql_fetch_assoc($myResult));
    }
  ?>