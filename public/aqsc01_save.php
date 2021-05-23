<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
//步骤2：
$Log_Item="安全管理制度汇编记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$AddName=FormatSTR($AddName);
$Name=FormatSTR($Name);
//提出上级分类的Id,以及该分类最后的排序Id,该分类的等级

if($Name=="无"){
	$PreItem=0;
	$theGrade=1;
	$checkSortSql=mysql_query("SELECT Max(Sort) AS maxSort FROM $DataPublic.aqsc01 WHERE PreItem='$PreItem'",$link_id);
	if($checkSortRow=mysql_fetch_array($checkSortSql)){
		$theSort=$checkSortRow["maxSort"]+1;
		}
	else{
		$theSort=1;
		}
	$inRecode="INSERT INTO $DataPublic.aqsc01 (Id,Grade,PreItem,Name,Sort,Estate,Locks,Date,Operator) VALUE ( NULL,'$theGrade','$PreItem','$AddName','$theSort','1','0','$DateTime','$Operator')";
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log="$TitleSTR 成功! $inRecode<br>";
		} 
	else{
		$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
		$OperationResult="N";
		}
	}
else{
	$checkSql=mysql_query("SELECT Id,Grade FROM $DataPublic.aqsc01 WHERE Name='$Name' LIMIT 1",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		$PreItem=$checkRow["Id"];
		$theGrade=$checkRow["Grade"]+1;
		$checkSort=mysql_fetch_array(mysql_query("SELECT Max(Sort) AS maxSort FROM $DataPublic.aqsc01 WHERE PreItem='$PreItem'",$link_id));
		$theSort=$checkSort["maxSort"]+1;
		$inRecode="INSERT INTO $DataPublic.aqsc01 (Id,Grade,PreItem,Name,Sort,Estate,Locks,Date,Operator) VALUE ( NULL,'$theGrade','$PreItem','$AddName','$theSort','1','0','$DateTime','$Operator')";
		$inAction=@mysql_query($inRecode);
		if ($inAction){ 
			$Log="$TitleSTR 成功! $inRecode<br>";
			} 
		else{
			$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
			$OperationResult="N";
			}
		}
	else{
		$Log=$Log."<div class=redB>读取不到名称为 $Name 的资料! SELECT Id,Grade FROM $DataPublic.aqsc01 WHERE Name='$Name' LIMIT 1</div><br>";
		$OperationResult="N";
		}
	}

//步骤4：
//$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
