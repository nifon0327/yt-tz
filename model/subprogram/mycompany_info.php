<?php 
$CheckMySql=mysql_query("SELECT * FROM $DataIn.my1_companyinfo WHERE cSign =7",$link_id);
if($CheckMyRow=mysql_fetch_array($CheckMySql)){
	do{
		$Type=$CheckMyRow["Type"];
		$Temp0=strval($Type)."_SealType";$$Temp0=$CheckMyRow["Type"];
		$Temp1=strval($Type)."_Company";$$Temp1=$CheckMyRow["Company"];
		$Temp2=strval($Type)."_Forshort";$$Temp2=$CheckMyRow["Forshort"];
		$Temp3=strval($Type)."_Tel";$$Temp3=$CheckMyRow["Tel"];
		$Temp4=strval($Type)."_Fax";$$Temp4=$CheckMyRow["Fax"];
		$Temp5=strval($Type)."_Address";$$Temp5=$CheckMyRow["Address"];
		$Temp6=strval($Type)."_ZIP";$$Temp6=$CheckMyRow["ZIP"];
		$Temp7=strval($Type)."_WebSite";$$Temp7=$CheckMyRow["WebSite"];
		$Temp8=strval($Type)."_LinkMan";$$Temp8=$CheckMyRow["LinkMan"];
		$Temp9=strval($Type)."_Mobile";$$Temp9=$CheckMyRow["Mobile"];
		$Temp10=strval($Type)."_Email";$$Temp10=$CheckMyRow["Email"];
		}while($CheckMyRow=mysql_fetch_array($CheckMySql));
	}
	
?>