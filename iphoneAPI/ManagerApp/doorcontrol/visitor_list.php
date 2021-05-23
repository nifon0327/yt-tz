<?php 
//读取门禁登记信息
$curDate=date("Y-m-d");
$sDate=date("Y-m-d",strtotime("$curDate  -7   day"));

$SearchRows=$TypeId==""?"":" and I.TypeId='$TypeId'";
$mySql="SELECT I.Id,I.Name,I.ComeDate,I.InTime,I.OutTime,I.Remark,I.Date,C.Name AS TypeName,I.Estate,I.TypeId,P.Forshort,M.Name AS Operator     
FROM $DataPublic.come_data I 
LEFT JOIN $DataPublic.come_type C ON C.Id=I.TypeId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=I.CompanyId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=I.Operator  
WHERE ((I.Estate>0 AND I.InTime>'$sDate') OR I.ComeDate=CURDATE() OR  I.InTime>'$sDate') $SearchRows ORDER BY I.Estate DESC,Id DESC";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do 
            {	
                    $Id=$myRow["Id"];
                    $Name=$myRow["Name"];
	                $ComeDate=$myRow["ComeDate"];
	                $InTime=$myRow["InTime"];
	                $OutTime=$myRow["OutTime"];
                    $Remark=$myRow["Remark"];
                    $TypeId=$myRow["TypeId"];
                    $TypeName=$myRow["TypeName"];
                    $Operator=$myRow["Forshort"]==""?$myRow["Operator"]:$myRow["Forshort"]; 
                    
                    $Remark=$Operator . " $TypeName"."。" . $Remark;
                    $Estate=$myRow["Estate"];
                    if ($InTime=="" && $Estate==0) continue;
                    switch($Estate){
	                     case 1:$EstateSTR="未 到";break;
	                     case 2:$EstateSTR="已 到";break;
	                     case 0:$EstateSTR="已 回";break;
                    }
                   $jsonArray[] = array( "$Id","$Name","$ComeDate","$InTime","$OutTime","$Remark","$Estate","$TypeId");
                    
            }
            while($myRow = mysql_fetch_assoc($myResult));
    }
?>