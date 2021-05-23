<?php 
//电信-EWEN
     	echo"<select name='Number' id='Number' style='width: 80px;'>";
		$PD_Sql = "SELECT B.Number,A.Name FROM $DataPublic.staffmain A,$DataPublic.paybase B WHERE A.Number=B.Number and B.JbSign=1ORDER BY A.Name";
		$PD_Result = mysql_query($PD_Sql);
		$k=1;
		while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
			$NumberTemp=$PD_Myrow["Number"];
			$NameTemp=$PD_Myrow["Name"];
			if($Number==""){
				if ($k==1){
					$DefaultNumber=$NumberTemp;
					$k=2;}
				}
			else{
				$DefaultNumber=$Number;
				}
			if($Number==$NumberTemp){				
				echo "<option value='$NumberTemp' selected>$NameTemp</option>";
				}
			else{
				echo "<option value='$NumberTemp'>$NameTemp</option>";}
			} 
        echo" </select> 的考勤统计"; 
		echo "&nbsp;&nbsp;<input type='button' name='Submit' value='查询' onclick='javascript:document.form1.submit();'> &nbsp;&nbsp;
		<input type='radio' name='radiobutton' value='radiobutton' onclick='javascript:document.form1.action=\"checkinout_read.php?Number=$Number\";document.form1.submit();'>日统计 
		 <input type='radio' name='radiobutton' value='radiobutton' onclick='toAction(\"mounthreport\")' >月统计";

?>