<?php 
//步骤1：

//步骤2：
$Log_Item="配件资料";			
$Log_Funtion="保存";

/*
2015-04-15 19:03:13.527 DailyManagement[2235:462871] Price=2

2015-04-15 19:03:13.527 DailyManagement[2235:462871] StuffType=9125

2015-04-15 19:03:13.528 DailyManagement[2235:462871] Spec=

2015-04-15 19:03:13.529 DailyManagement[2235:462871] Weight=

2015-04-15 19:03:13.530 DailyManagement[2235:462871] DevelopState=0

2015-04-15 19:03:13.530 DailyManagement[2235:462871] StuffName=testname

2015-04-15 19:03:13.531 DailyManagement[2235:462871] Unit=20

2015-04-15 19:03:13.531 DailyManagement[2235:462871] CompanyId=2001

2015-04-15 19:03:13.532 DailyManagement[2235:462871] Property=2
DevelopWeek=201522
DevelopCompany=1056
*/


$TypeId = $_POST["StuffType"];
$StuffCname = $_POST["StuffName"];;
$Price = $_POST["Price"];;
$Spec = $_POST["Spec"];;
$Weight = $_POST["Weight"] == "" ? "0.0000":$_POST["Weight"];
$Unit = $_POST["Unit"];
$Property = $_POST["Property"];
$DevelopWeek = $_POST["DevelopWeek"];
$DevelopTargetDate = "";
if ($DevelopWeek!=NULL && $DevelopWeek>0) {
	$weekArray = GetWeekToDate($DevelopWeek,"Y-m-d");
	$DevelopTargetDate = date("Y-m-d",strtotime("-2 days",strtotime($weekArray[1])));
}
$DevelopCompany = $_POST["DevelopCompany"];
$DevelopState = $_POST["DevelopState"] == ""?0:$_POST["DevelopState"];
$CompanyId = $_POST["CompanyId"];
$relation = $_POST["relation"];
$relations = explode(",",$relation);
$DateTime=date("Y-m-d H:i:s");
$Operator=$LoginNumber;
$OperationResult="N";
//步骤3：需处理

$Date=date("Y-m-d");
$maxSql = mysql_query("SELECT MAX(StuffId) AS Mid FROM $DataIn.stuffdata",$link_id);
$StuffId=mysql_result($maxSql,0,"Mid");
if($StuffId){
	$StuffId=$StuffId+1;
}
else{
	$StuffId=90001;
}
//新增操作
//写入记录

$checkResult=mysql_fetch_array(mysql_query("SELECT T.BuyerId,T.DevelopGroupId,T.DevelopNumber,T.Position,M.CheckSign  FROM $DataIn.StuffType T 
LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
$BuyerId=$checkResult["BuyerId"];
$DevelopGroupId=$checkResult["DevelopGroupId"];
$DevelopNumber=$checkResult["DevelopNumber"];
$SendFloor=$checkResult["Position"];
$CheckSign=$checkResult["CheckSign"];

$SendFloor=$SendFloor==""?0:$SendFloor;
$CheckSign=$CheckSign==""?0:$CheckSign;
$DevelopGroupId=$DevelopGroupId==""?0:$DevelopGroupId;
$DevelopNumber=$DevelopNumber==""?0:$DevelopNumber;

$Pjobid=	$GicJobid=$DevelopGroupId;
$PicNumber=	$GicNumber=$DevelopNumber;

$GCField=array(-1,-1);	//系统默认
$ForcePicSpe = -1;
$GCjobid=$GCField[0];
$GcheckNumber=$GCField[1];


$jhDays=0;

$StuffEname="";$BoxPcs ="0";$Remark="";
$inRecode="INSERT INTO $DataIn.stuffdata (Id,StuffId,StuffCname,StuffEname,TypeId,Spec,Weight,Price,Unit,BoxPcs,Remark,Gfile,Gstate,Gremark,Picture,Pjobid,PicNumber,Jobid,GicNumber,GcheckNumber,SendFloor,CheckSign,ForcePicSpe,DevelopState,jhDays,Estate,Locks,Date,GfileDate,Operator) VALUES 
(NULL,'$StuffId','$StuffCname','$StuffEname','$TypeId','$Spec','$Weight','$Price','$Unit','$BoxPcs','$Remark','','0','','0','$Pjobid','$PicNumber','$GicJobid','$GicNumber','$GcheckNumber','$SendFloor','$CheckSign','$ForcePicSpe','$DevelopState','$jhDays','2','0','$Date',NULL,'$Operator')";
$inAction=@mysql_query($inRecode);

//解锁表
if($inAction){ 
     //配件属性
	 $OperationResult = "Y";
	 $Property = explode(",",$Property);
       $tempCount=count($Property);
       for($k=0;$k<$tempCount;$k++){
            if($Property[$k]>0){
                   $inSql3="INSERT INTO $DataIn.stuffproperty(Id,StuffId,Property)VALUES(NULL,'$StuffId','$Property[$k]')";
                   $inRes3=@mysql_query($inSql3);
                  }
				  
				  if ($Property[$k]==11 && $DevelopTargetDate!="") {
					  $inSql3="INSERT INTO `$DataIn`.`stuffdevelop`
(`Id`,
`StuffId`,
`GroupId`,
`Number`,
`Targetdate`,
`Finishdate`,
`CompanyId`,
`Type`,
`Grade`,
`KfRemark`,
`Remark`,
`dFile`,
`ReturnReasons`,
`Estate`,
`Date`,
`Operator`,
`Locks`,
`PLocks`,
`creator`,
`created`,
`modifier`,
`modified`)
VALUES
(NULL,
'$StuffId',
'$DevelopGroupId',
'$DevelopNumber',
'$DevelopTargetDate',
'0000-00-00 00:00:00',
'$DevelopCompany',
0,
0,
'',
'',
NULL,
NULL,
1,
'$Date',
'$Operator',
0,
0,
'$Operator',
'$DateTime',
NULL,
NULL);";
                   $inRes3=@mysql_query($inSql3);
				  }
           }
	$inRecode1="INSERT INTO $DataIn.bps (Id,StuffId,BuyerId,CompanyId,Locks) VALUES (NULL,'$StuffId','$BuyerId','$CompanyId','0')";
	$inRres1=@mysql_query($inRecode1);
	if ($inRres1){ 
		$Log="<br>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增成功!配件采购供应商关系设定成功!";
		$OperationResult="Y";
		}
	else{
		$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增成功!但配件采购供应商关系设定不成功!</div>";
		$OperationResult="N";
		}
	$inRecode2="INSERT INTO $DataIn.ck9_stocksheet (Id,StuffId,dStockQty,tStockQty,oStockQty,mStockQty,Date) VALUES (NULL,'$StuffId','0','0','0','0','$Date')";
	$inRes2=@mysql_query($inRecode2);
	if($inRes2){
		$Log.="<br>&nbsp;&nbsp;&nbsp;&nbsp;库存资料设定成功!!!";
		$OperationResult="Y";
		}
	else{
		$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;库存资料设定失败!!!</div>";
		$OperationResult="N";
		}
	} 
	$tempCount=count($relations);
	if ($tempCount > 0) {
		
		//插入新的关系	
		
		
		foreach ($relations as $GoodsId){
	$IN_recodeN="INSERT INTO $DataIn.cut_die (Id, ProductId, StuffId, GoodsId) VALUES (NULL,'0','$StuffId','$GoodsId')";
	$resN=@mysql_query($IN_recodeN);
	if($resN){
		$Log.="&nbsp;&nbsp; $x -ID号为 $StuffId 的配件和ID号为 $GoodsId 的模具已加入关系表中!</br>";
		}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp; $x -ID号为 $StuffId 的配件和ID号为 $GoodsId 的模具已加入关系表中!</div></br>";
		}
		}
	}
	
else{
	//失败后的处理,删除已经上传的文件
	$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;名称为 $StuffCname 的配件资料新增失败! $inRecode</div>";
	$OperationResult="N";
	}

//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("success"=>"$OperationResult");
?>
