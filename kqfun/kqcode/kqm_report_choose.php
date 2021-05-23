<?php 
//****$DataIn.电信---yang 20120801
     	echo"<select name='DefaultNumber' id='DefaultNumber' style='width: 80px;'>";		
		/*
		$PD_Sql = "SELECT $DataPublic.paybase.Number,staffmain.Name,staffmain.JobId 
		FROM $DataPublic.staffmain,$DataPublic.paybase 
		WHERE $DataPublic.staffmain.Number=$DataPublic.paybase.Number 
		and $DataPublic.paybase.JbSign=1 
		and $DataPublic.staffmain.ComeIn<='$EndDay' 
		and ($DataPublic.staffmain.Dimission='0000-00-00' or $DataPublic.staffmain.Dimission<'$EndDay') 
		order by $DataPublic.staffmain.BranchType,$DataPublic.staffmain.JobId,$DataPublic.staffmain.ComeIn";
		*/
		$PD_Sql = "SELECT P.Number,M.Name,M.JobId 
		FROM $DataPublic.staffmain M,$DataPublic.paybase P 
		WHERE M.Number=P.Number 
		and P.JbSign=1 
		and M.ComeIn<='$EndDay' 
		and (M.Dimission='0000-00-00' or M.Dimission<'$EndDay') 
		order by M.BranchType,M.JobId,M.ComeIn";


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