<?php 
//电信-ZX  2012-08-01
$BankFromName=$BankFromName==""?"BankId":$BankFromName;
echo"<select name='$BankFromName' id='$BankFromName'>";
echo"<option value=''>请选择结付银行</option>";
$checkBankSql=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Estate=1  ORDER BY Id",$link_id);
if($checkBankRow=mysql_fetch_array($checkBankSql)){
	$i=1;$BankSelect ="";
	do{
		$Bank_Id=$checkBankRow["Id"];
		$BankTitle=$checkBankRow["Title"];
		if($Bank_Id==$$BankFromName){
			$BankSelect  ="selected";
		}else{
			$BankSelect = "";
		}
		echo"<option value='$Bank_Id' $BankSelect>$i - $BankTitle</option>";
		$i++;
		}while($checkBankRow=mysql_fetch_array($checkBankSql));
	}
echo"</select>";
?>