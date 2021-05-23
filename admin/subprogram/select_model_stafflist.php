<?php   
//电信---yang 20120801
$CheckSql = mysql_query("SELECT T.Operator,M.Name FROM $CheckTb T LEFT JOIN 
$DataPublic.staffmain M ON M.Number=T.Operator GROUP BY T.Operator ORDER BY T.Operator",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	do{
		$Operator=$CheckRow["Operator"];
		$Name=$CheckRow["Name"];
		echo "<option value='$Operator'>$Name</option>";
		}while($CheckRow=mysql_fetch_array($CheckSql));
	}
?>