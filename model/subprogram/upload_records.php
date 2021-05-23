<?php 
/*
记录文件上传时间
传入参数:$Record_Id,$UpFileSign

*/


switch($UpFileSign){
	case "TestStandard":  $TypeId=1; break;
	case "PackPicture":   $TypeId=2; break;
	case "NoPackPicture": $TypeId=3; break;
	case "Stuffpdf":      $TypeId=4; break;
    default:              $TypeId=0; break;
      
}

$R_inRecode="INSERT INTO $DataIn.upload_records (Id,RId,TypeId,TypeName,Date,Estate,Operator) VALUES 
			(NULL,'$Record_Id','$TypeId','$UpFileSign',CURDATE(),'1','$Login_P_Number')";  
			//echo $R_inRecode;
$R_inAction=mysql_query($R_inRecode,$link_id);


?>
