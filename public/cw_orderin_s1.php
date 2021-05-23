<?php 
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|80|外箱标签|60|出货金额|100|状态|40|出货日期|80|货运信息|120";
$ColsNumber=14;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid";
$SearchSTR=0;		//不允许搜索
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
//echo "Jid:".$Jid;
switch($Action){
         case "3":
              $curDate=date("Y-m-d");
              $endDate=date("Y-m-d",strtotime("$curDate  -180 day"));
              $SearchRows="  AND (M.cwSign>0 OR (M.cwSign=0 AND M.Date>='$endDate')) AND NOT EXISTS(
                SELECT H.Id FROM $DataIn.hzqksheet H  WHERE H.Id=OtherId AND H.Property=2 
                )";
            break;
		 case "6":
               $SearchRows="  AND M.cwSign>0 ";
         break;
         case "7":
               $MidArray = explode("^^", $Jid);
               $Mids = implode(",", $MidArray);
               $SearchRows="  and M.CompanyId=$Bid AND M.Estate=0 AND M.Id NOT IN ($Mids) ";
               echo $SearchRows;
         break;
           
       default:
            $SearchRows=" and M.CompanyId=$Bid AND M.cwSign>0";
       break;
}
//步骤4：需处理-可选条件下拉框
$otherAction="<span onClick='Comeback($Action)' $onClickCSS>确定</span>&nbsp;";//自定义功能
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Operator,C.Forshort 
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
WHERE 1 $SearchRows ORDER BY M.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		//Invoice查看
		//加密参数
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		if($CompanyId==1001){
			$d2=anmaIn("invoice/mca",$SinkOrder,$motherSTR);
			$InvoiceFile.="&nbsp;&nbsp;<span onClick='OpenOrLoad(\"$d2\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>★</span>";
			}
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"]);
		//已收货款
		$checkreAmount=mysql_query("SELECT SUM(Amount) AS reAmount FROM $DataIn.cw6_orderinsheet WHERE chId='$Id'",$link_id);
		$reAmount=mysql_result($checkreAmount,0,"reAmount");		
		$reAmount=$reAmount==""?0:sprintf("%.2f",$reAmount);
		$unAmount=sprintf("%.2f",$Amount-$reAmount);
			switch($Action){
			        case "3":
		 	        case "6":
		 	        case "7":
			    	$BackValue=$Id."^^".$InvoiceNO;
				    break;
                  default: 
		            $BackValue=$Id."^^".$Date."^^".$Number."^^".$InvoiceNO."^^".$Amount."^^".$unAmount;
                   break;
				}		

		if($Amount!=$reAmount){
			$Amount="<span class='redB'>$Amount</span>";
             $reStr="<span class='redB'>×</span>";
			}
		else{
		    $Amount="<span class='greenB'>$Amount</span>";
			$reStr="<div class='greenB'>√</div>";
		}
		$Locks=1;
		$ValueArray=array(
			array(0=>$Number, 		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$InvoiceNO),
			array(0=>$InvoiceFile,	1=>"align='center'"),
			array(0=>$BoxLable,		1=>"align='center'"),
			array(0=>$Amount,		1=>"align='right'"),
			array(0=>$reStr,		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Wise)
			);
		$checkidValue=$BackValue;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script  type=text/javascript>
//返回选定的采购流水号
function Comeback(Action){
	var returnq="";
	var j=1;
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.type=="checkbox"){
			if(e.checked){
				if (j==1){
					returnq=e.value;j++;
					}
				else{
					returnq=returnq+"``"+e.value;j++;
					}					
				} 
			}
		}
	returnValue=returnq;
	this.close();
	}
</script>