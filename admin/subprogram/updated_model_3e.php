<?php   
//多记录操作 二合一已更新
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		//插入转正登记表
		include "staff_maxnum.php";  
		
		include "../basic/parameter.inc";  //

		$checkNumRow=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataPublic.staffmain ORDER BY Number DESC",$link_id));
		$Local_MaxNumber=$checkNumRow["Number"];
		//echo "Local_MaxNumber:$Local_MaxNumber <br>";
		$MaxNumber=$Local_MaxNumber>$remote_MaxNumber?$Local_MaxNumber:$remote_MaxNumber;

		
		if($MaxNumber>1){
			$Number=$MaxNumber+1;}
		else{
			$Number=10001;
			}
		//echo "$MaxNumber=$Local_MaxNumber>$remote_MaxNumber?$Local_MaxNumber:$remote_MaxNumber";
		
		$checkNumRow=mysql_fetch_array(mysql_query("SELECT Number,Name FROM $DataIn.stafftempmain WHERE Id = '$Id' LIMIT 1",$link_id));
		$tmpNumber=$checkNumRow["Number"];
		$Name=$checkNumRow["Name"];
		//echo "$Number:$tmpNumber";
		$ThisYear=date("Y");
		$ThisMonth=date("m");

		if($ThisMonth==12){
			$ThisMonth=1;
			$ThisYear=$ThisYear+1; //换年了
		}
		else{
			$ThisMonth=$ThisMonth+1;
		}
		
		if($ThisMonth<10){
			$ThisMonth='0'.$ThisMonth;
		}
		
		$ComeIn="$ThisYear"."-$ThisMonth"."-01";
		
		$cSign='7';$Nickname=''; $IdNum=''; $Grade='1';$KqSign='1';$Mail='';$ExtNo='';		
       	//INSERT INTO $DataPublic.staffmain_xx;
		$reSQL="INSERT INTO $DataPublic.staffmain (SELECT NULL , '$cSign' , '$Number' , IdNum , Name , '$Nickname' , '$Grade' , '$KqSign' ,BranchId ,JobId,GroupId ,'$Mail' , '$ExtNo' , '$ComeIn' , Introducer , Estate , Locks , Date , Operator  FROM  $DataIn.stafftempmain WHERE Id = '$Id'  ) ";		
		$result1=mysql_query($reSQL); //执行，不管是否成功
		//echo "$reSQL";

		$Dh='';$InFile='0';
		//INSERT INTO $DataPublic.staffsheet_xx
		$reSQL="INSERT INTO $DataPublic.staffsheet (select       NULL,'$Number',Sex,Nation,Rpr,Education,Married,Birthday,Photo,IdcardPhoto,HealthPhoto,Idcard,Address,Postalcode,Tel,Mobile,'$Dh',Bank,Note,'$InFile'  FROM $DataIn.stafftempsheet S
    	 LEFT JOIN $DataIn.stafftempmain M ON M.Number=S.Number
		 WHERE M.Id = '$Id'  ) ";		
		$result1=mysql_query($reSQL); //执行，不管是否成功	
		//echo "$reSQL";


		


		$FilePath="../download/stafftempphoto/";
		$StaffFilePath="../download/staffPhoto/";

		$PreFileName1=$FilePath."P".$tmpNumber.".jpg";
		if(file_exists($PreFileName1)){
			$sPreFileName1=$StaffFilePath."P".$Number.".jpg";
			if(!copy($PreFileName1, $sPreFileName1)) {
				$Log.="临时员工 $Name /$tmpNumber 的照片拷贝失败! <br>";
				}
		}
		
		$PreFileName2=$FilePath."C".$tmpNumber.".jpg";
		if(file_exists($PreFileName2)){
			$sPreFileName2=$StaffFilePath."C".$Number.".jpg";
			if(!copy($PreFileName2, $sPreFileName2)) {
				$Log.="临时员工 $Name /$tmpNumber 的身份证图片拷贝失败! <br>";
			}
		}
		$PreFileName3=$FilePath."H".$tmpNumber.".jpg";
		if(file_exists($PreFileName3)){
			$sPreFileName3=$StaffFilePath."H".$Number.".jpg";
			if(!copy($PreFileName3, $sPreFileName3)) {
				$Log.="临时员工 $Name /$tmpNumber 的健康证图片拷贝失败! <br>";
			}
		}
		
		if($result1){
			
			$Log.="临时员工 $Name /$tmpNumber 的转正成功 </br>";
		   //3-薪资基础初始化Id/Number/Jxjj/Locks/Date/Operator
			$baseinsql = "INSERT INTO $DataPublic.paybase (Id,Number,Jj,Jtbz,Locks,Date,Operator) VALUES (NULL,'$Number','0','0','0','$Date','$Operator')";
			$baseinresult = @mysql_query($baseinsql);
			if($baseinresult){
				$Log.="薪资初始化成功！<br>";
				}
			else{
				$Log.="<div class=redB>薪资初始化失败! $baseinsql </div><br>";
				$OperationResult="N";
				}
			//短消息通知
			$smsNote="临时员工转正 $Name / $Number 的资料已加入,初始化员工等级: $Grade ;考勤状态:需考勤; 薪资基础的默认奖金:0，请核实。";	//短消息内容
			$smsfunId=3;																//短消息通知编号：权限，经理
			include "subprogram/tosmsdata.php";			
			
			}
		else{
			$Log.="临时员工 $Name /$tmpNumber 转正失败! $sql</br>";
			$OperationResult="N";
			}		
	}
	
}

/*
$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id IN ($Ids)";
$result = mysql_query($sql);
if($result){
	$Log="ID号在 $Ids 的记录成功 $Log_Funtion.</br>";
	}
else{
	$Log="ID号为 $Ids 的记录$Log_Funtion 失败! $sql</br>";
	$OperationResult="N";
	}
*/	
?>