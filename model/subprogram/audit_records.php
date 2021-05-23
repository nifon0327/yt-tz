<?php 
/*
记录审核时间
传入参数:$Record_Id,$UpFileSign

*/
function addAuditRecords($Record_Id,$AuditSign,$ActionId,$Remark,$Operator,$DataIn,$link_id)
{
    switch($AuditSign){
		case "TestStandard":  $TypeId=1; break;
		case "PackPicture":   $TypeId=2; break;
		case "NoPackPicture": $TypeId=3; break;
		case "Stuffpdf":      $TypeId=4; break;
	    default:              $TypeId=0; break;
   }
   $R_inRecode="INSERT INTO $DataIn.audit_records (Id,RId,TypeId,TypeName,ActionId,Remark,Date,Estate,Operator) VALUES 
			(NULL,'$Record_Id','$TypeId','$AuditSign','$ActionId','$Remark',CURDATE(),'1','$Operator')";  
   $R_inAction=mysql_query($R_inRecode,$link_id);
}
?>
