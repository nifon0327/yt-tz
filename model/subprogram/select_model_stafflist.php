<?php 
//电信---yang 20120801
$OperatorSTR=$OperatorSTR==""?"T.Operator":$OperatorSTR;
$CheckSql = mysql_query("SELECT $OperatorSTR AS Operator,M.Name FROM $CheckTb T LEFT JOIN 
$DataPublic.staffmain M ON M.Number=$OperatorSTR  GROUP BY $OperatorSTR ORDER BY $OperatorSTR",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	do{
		$Operator=$CheckRow["Operator"];
		$Name=$CheckRow["Name"];
		if  (strlen($Name)>0 ){
		     echo "<option value='$Operator'>$Name</option>";
		 }
		}while($CheckRow=mysql_fetch_array($CheckSql));
	}
?>