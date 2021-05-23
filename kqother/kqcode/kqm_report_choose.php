<?php 
//电信-EWEN
     	echo"<select name='DefaultNumber' id='DefaultNumber' style='width: 80px;'>";		
		$PD_Sql = "SELECT B.Number,A.Name,A.JobId 
		FROM $DataPublic.staffmain A,$DataPublic.paybase B 
		WHERE A.Number=B.Number 
		and B.JbSign=1 
		and A.ComeIn<='$EndDay' 
		and (A.Dimission='0000-00-00' or A.Dimission<'$EndDay') 
		order by A.BranchType,A.JobId,A.ComeIn";
		
		$PD_Result = mysql_query($PD_Sql);
		$k=1;
		while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
			$NumberTemp=$PD_Myrow["Number"];
			$NameTemp=$PD_Myrow["Name"];
			if($DefaultNumber==""){
				if ($k==1){
					$DefaultNumber=$NumberTemp;
					$k=2;}
				}
			if($DefaultNumber==$NumberTemp){				
				echo "<option value='$DefaultNumber' selected>$NameTemp</option>";
				}
			else{
				echo "<option value='$NumberTemp'>$NameTemp</option>";}
			} 
        echo" </select> 的考勤统计"; 
		echo "&nbsp;&nbsp;<input type='button' name='Submit' value='查询' onclick='javascript:document.form1.submit();'>";

?>