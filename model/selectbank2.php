<?php 

$BankFromName=$BankFromName==""?"BankId":$BankFromName;
echo"<select name='$BankFromName' id='$BankFromName' msg='未选择银行'>";
echo"<option value='' selected>请选择结付银行</option>";
$checkBankSql=mysql_query("SELECT B.Id,B.Title FROM $DataPublic.my2_bankinfo B WHERE B.Estate=1  ORDER BY B.Id",$link_id);
if($checkBankRow=mysql_fetch_array($checkBankSql)){
	$i=1;
	do{
		$Temp_Id=$checkBankRow["Id"];
		$Temp_Title=$checkBankRow["Title"];
		if($$BankFromName==$Temp_Id){
			echo"<option value='$Temp_Id' selected>$i - $Temp_Title</option>";
			}
		else{
			echo"<option value='$Temp_Id'>$i - $Temp_Title</option>";
			}
		$i++;
		}while($checkBankRow=mysql_fetch_array($checkBankSql));
	}
echo"</select>";
?>