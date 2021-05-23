<?php   
//$DataIn.电信---yang 20120801
$CurreyResult = mysql_query("SELECT Id,Name,Symbol FROM $DataPublic.currencydata WHERE Estate='1' ORDER BY Id",$link_id);
if( $CurreyRow = mysql_fetch_array($CurreyResult)){
	do{
		$Id=$CurreyRow["Id"];
		$Name=$CurreyRow["Name"];
		$Symbol=$CurreyRow["Symbol"];				
		if($Id==$Currency){
			echo "<option value='$Id' selected>$Symbol</option>";
			}
		else{
			echo "<option value='$Id'>$Symbol</option>";
			}
		}while($CurreyRow = mysql_fetch_array($CurreyResult));
	}
?>