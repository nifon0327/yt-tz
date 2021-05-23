<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
1、$SharingShowSTR    是否显示 “共享”,Y显示，其他值不显示 
2、$staffName_InputID  输入框ID号
3、$staffNumber_InputID  记录员工Number输入框ID号
*/
  //$SharingShowSTR=$SharingShow=="Y"?"":" AND M.cSign='" . $_SESSION["Login_cSign"]  . "' ";
  $staffName_InputID=$staffName_InputID==""?"StaffName":$staffName_InputID;
  $staffNumber_InputID=$staffNumber_InputID==""?"StaffNumber":$staffNumber_InputID;

  $StaffSql = mysql_query("SELECT M.Id,M.Number,M.Name, M.Nickname FROM $DataPublic.staffmain M 
	           WHERE M.Estate='1'  $SharingShowSTR ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number",$link_id);
while ($StaffRow = mysql_fetch_array($StaffSql)){
		$sNumber=$StaffRow["Number"];
        $sName=$StaffRow["Name"];
		$sNickname=$StaffRow["Nickname"];
       $subStaffName[]=array($sNumber,$sNickname,$sName);
	}

?>
<link rel='stylesheet' href='../plugins/inputSuggest/inputSuggest.css'>
<script type='text/javascript' src='../plugins/inputSuggest/inputSuggest1.1a.js'></script>
<script type="text/JavaScript">
	window.onload = function(){
	        var subName=<?php  echo json_encode($subStaffName);?>;
	        var Name_Input='<?php  echo $staffName_InputID;?>';   
	        var Id_Input='<?php  echo $staffNumber_InputID;?>';   
	       
	        var input_Width=document.getElementById(Name_Input).style.width;          
			var sinaSuggest = new InputSuggest({
			    input: document.getElementById(Name_Input),
				poseinput: document.getElementById(Id_Input),
				data: subName,
				width: input_Width
			});
					
		}
</script>