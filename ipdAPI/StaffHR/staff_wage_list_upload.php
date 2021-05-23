<?php 
	
	include "../../basic/parameter.inc";
	$caption = $_POST["caption"];
	$typeId = $_POST["typeId"];
	$operation = $_POST["operator"];
	$month = $_POST["month"];
	$cSign = $_POST["cSign"];
	$date = date('Y-m-d');
	$endDate = date('2025-m-d');
	$dataList = date('YmdHis');
	$fileName = $dataList.".pdf";
	$uploadTag = "no";
	$info = "";
	
	if(is_uploaded_file($_FILES['wageList']['tmp_name']))
	{
		$path = $_SERVER['DOCUMENT_ROOT']."/download/hzdoc/".$fileName;
		if(move_uploaded_file($_FILES['wageList']['tmp_name'],$path))
		{
			$uploadTag = "yes";			
		}
	}
	else
	{
		$info = "上传文件失败";
	}
	
	if($uploadTag == "yes")
	{	
		//插入zw2_hzdoc
		$IN_recode="INSERT INTO $DataIn.zw2_hzdoc (Id,Caption,Attached, cSign,TypeId,Date,EndDate,Locks,Operator) VALUES (NULL,'$caption','$fileName','$cSign','$typeId','$date','$endDate','0','$operation')";
		
		$uploadResult = mysql_query($IN_recode);
		if($uploadResult)
		{
			//插入wagelist
			$updateWageList = "Update $DataPublic.wage_list Set FileName='$fileName',Estate = '1' Where Month = '$month' And cSign = '$cSign'";
			$updateWageLIstResult = mysql_query($updateWageList);
			if($updateWageLIstResult)
			{
				$info = "上传成功";
			}
			else
			{
				$info = "状态改变失败";
			}
		}
		
	}
		
	echo $info;
		
?>