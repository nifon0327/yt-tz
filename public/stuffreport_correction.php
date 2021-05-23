<?php 
/*$DataIn.电信---yang 20120801
$DataIn.stuffdata
$DataIn.cg1_stocksheet
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
$DataIn.ck7_bprk
$DataIn.ck8_bfsheet
$DataIn.ck9_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 配件库存自动更正");
$funFrom="stuffreport";
$nowWebPage=$funFrom."_read";
$Th_Col="操作|40|序号|40|配件Id|50|配件名称|280|在库分析|80|可用库存分析|80|更正提示|270";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.StuffId,S.StuffCname,S.Price,K.dStockQty,K.tStockQty,K.oStockQty
FROM $DataIn.stuffdata S
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE 1 $SearchRows
ORDER BY S.Id";
$SumoStockQty=0;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$dStockQty=$myRow["dStockQty"];
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$SumoStockQty=$SumoStockQty+$oStockQty;
		//订单总数
		$orderQty=0;
		$cgQty=0;
		$CheckGSql=mysql_query("SELECT SUM(OrderQty) AS orderQty,SUM(FactualQty+AddQty) AS cgQty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'",$link_id);
		if($CheckGRow=mysql_fetch_array($CheckGSql)){
			$orderQty=$CheckGRow["orderQty"]==""?0:$CheckGRow["orderQty"];
			$cgQty=$CheckGRow["cgQty"]==""?0:$CheckGRow["cgQty"];
			}

		//入库总数
		$UnionSTR3=mysql_query("SELECT SUM(Qty) AS rkQty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId'",$link_id);
		$rkQty=mysql_result($UnionSTR3,0,"rkQty");
		$rkQty=$rkQty==""?0:$rkQty;

		//领料总数
		$UnionSTR4=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId'",$link_id);
		$llQty=mysql_result($UnionSTR4,0,"llQty");
		$llQty=$llQty==""?0:$llQty;

		//备品转入数量
		$UnionSTR5=mysql_query("SELECT SUM(Qty) AS bpQty FROM $DataIn.ck7_bprk WHERE StuffId='$StuffId'",$link_id);
		$bpQty=mysql_result($UnionSTR5,0,"bpQty");
		$bpQty=$bpQty==""?0:$bpQty;
		
		//报废数量,只有审核通过的才算 modify by zx 2010-11-30
		$UnionSTR6=mysql_query("SELECT SUM(Qty) AS bfQty FROM $DataIn.ck8_bfsheet WHERE  Estate=0 AND StuffId='$StuffId'",$link_id);
		$bfQty=mysql_result($UnionSTR6,0,"bfQty");
		$bfQty=$bfQty==""?0:$bfQty;

		$tValue=$dStockQty+$rkQty+$bpQty-$llQty-$bfQty;
		$oValue=$dStockQty+$cgQty+$bpQty-$orderQty-$bfQty;		
		if($tValue!=$tStockQty || $oValue!=$oStockQty){
			//自动更正库存
			if($tValue<0 || $oValue<0){//有负数，不更新
				$Info="<div class='redB'>分析数量出现负数不做更正,需分析数据错误.</div>";
				}
			else{						//更新
				//$LockSql=" LOCK TABLES $DataIn.ck9_stocksheet WRITE";$LockRes=@mysql_query($LockSql);
				$Date=date("y-m-d");
				$UpSql = "UPDATE $DataIn.ck9_stocksheet SET tStockQty='$tValue',oStockQty='$oValue',Date='$Date' WHERE StuffId='$StuffId'";
				$UpResult = mysql_query($UpSql);
				//解锁
				//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
				if($UpResult){
					$Info="库存数量已做更正.";
					}
				else{
					$Info="<div class='redB'>库存不对,但更正不成功.</div>";
					}
				}
			//输出资料
			$ChooseOut="N";
			$myOpration="<a href='stuffreport_result.php?Idtemp=$StuffId&Nametemp=$StuffCname' target='_blank'>分析</a>";
			$ValueArray=array(
				array(0=>$StuffId,
						 1=>"align='center'"),
				array(0=>$StuffCname,
						 3=>"..."),
				array(0=>$tValue,
						 1=>"align='center'"),
				array(0=>$oValue,
						 1=>"align='center'"),
				array(0=>$Info)
				);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			}
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
pBottom($i-1,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>