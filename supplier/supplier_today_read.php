<?php
//zxq-2013-01-18
//步骤1
session_start();
$Login_WebStyle="default";
include "../model/modelhead.php";
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
//步骤2：需处理
$ColsNumber=18;
$tableMenuS=500;
ChangeWtitle("$SubCompany  今日新单");
$funFrom="supplier_today";

$From=$From==""?"read":$From;
$sumCols="10,11,12";			//求和列,需处理
$MergeRows=5;

$Th_Col="选项|30|NO.|30|采购日期|60|采购单号|60|备注|30|序号|30|配件ID|45|配件名称|200|配件图档|50|图档日期|80|QC图|40|品检<br>报告|40|单位|30|采购数量|60|含税价|60|金 额|60|收货数|60|欠数|60|出货日期|70|送货</br>楼层|40|需求单流水号|100";//历史订单|60|

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 200;							//每页默认记录数量,13
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
$SearchRows=" and M.CompanyId='$myCompanyId' ";

if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (A.StuffCname LIKE '%$StuffCname%' OR A.StuffId LIKE '%$StuffCname%')";
	$List1="&nbsp;&nbsp;<input type='button' id='cancelQuery' value='取消查询'  class='ButtonH_25' onclick='document.form1.submit();'/>";
       }
else{
		$numSql=mysql_query("SELECT M.Id 
					FROM $DataIn.cg1_stockmain M 
					LEFT JOIN $DataIn.cg1_stockreview R ON R.Mid=M.Id
					WHERE   M.CompanyId='$myCompanyId' AND R.Id IS NULL AND EXISTS(SELECT S.Id FROM $DataIn.cg1_stocksheet S WHERE S.Mid=M.Id)",$link_id);
		if (mysql_num_rows($numSql)>0){
			 $SearchRows.=" AND NOT EXISTS (SELECT R.Mid FROM $DataIn.cg1_stockreview R WHERE  R.Mid=M.Id ) ";
			 $SearchSign="1";
			 echo "请查看以下新采购单，无误后请选择并点击<input name='OkBtn' id='OkBtn' type='button' value='确   认' onclick='OkBtnClick(this)'>";
		}
		else{
			 $toDay=date("Y-m");
			$checkDay=$checkDay==""?$toDay:$checkDay;
		?>
		下单月份：<input name='checkDay' class="Wdate"  type='text' id='checkDay' size='10' maxlength='10' value='<?php echo $checkDay;?>'   onclick="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true,onpicked:function(dp){document.form1.submit();}})">&nbsp;
		<?php
		    $SearchRows.=" AND DATE_FORMAT(M.Date,'%Y-%m')='$checkDay' ";
		}
		
		 $List1="&nbsp;&nbsp;<input name='StuffCname' type='text' id='StuffCname' size='20' value='配件名称或ID'  onchange='CnameChanged(this)'  oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件名称或ID'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件名称或ID' : this.value;\" style='color:#DDD;'><input type='button' id='stuffQuery' value='查询'  class='ButtonH_25' onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;document.form1.submit();\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}

echo $List1;
echo $CencalSstr;
//步骤5：

include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
//步骤6：需处理数据记录处理
$i=1;$n=1;$k=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];

$mySql="SELECT M.Date,M.PurchaseID,M.Remark,S.Mid,S.StuffId,A.StuffCname,S.StockId,S.PorderId,(S.FactualQty+S.AddQty) AS Qty,S.Price,A.Picture,A.Gfile,A.Gstate,A.Gremark,A.GfileDate,S.Id,A.TypeId ,U.Name AS UnitName,F.Remark as SendFloor,S.DeliveryDate,YEARWEEK(S.DeliveryDate,1) AS Weeks  
FROM  $DataIn.cg1_stockmain M 
LEFT JOIN  $DataIn.cg1_stocksheet S ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.base_mposition F ON  F.Id=A.SendFloor 
WHERE 1 and S.Mid>0  $SearchRows   ORDER BY M.PurchaseID DESC  ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$tempStuffId="";
$Locks=1;
$Keys=4;
$DefaultBgColor=$theDefaultColor;
$midDefault="";
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$PurchaseID=$myRow["PurchaseID"];
		$Remark=$myRow["Remark"];
		$Remark=$Remark==""?"&nbsp":"<img src='../images/remark.gif' title='$Remark' width='16' height='16'>";
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		//$PurchaseIDStr="<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$PurchaseIDStr="<a href='../public/PurchaseToDownload.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		//....获得旧系统的配件ID，方面供应商和仓库比对资料
		include "../model/subprogram/get_oldStuffCname_StuffId.php"; 
		
		$UnitName=$myRow["UnitName"];
		$SendFloor=$myRow["SendFloor"];
		$StockId=$myRow["StockId"];
		$PorderId=$myRow["PorderId"];
		$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
		$Picture=$myRow["Picture"];
        $TypeId=$myRow["TypeId"];
		$Gfile=$myRow["Gfile"];
		$Gremark=$myRow["Gremark"];
		$DeliveryDate=$myRow["DeliveryDate"];
		$SetDate=CountDays($DeliveryDate,5);
		if($SetDate>-1){		//离交期不大于一天，为红色
			$DeliveryDate="<span class='redB'>".$DeliveryDate."</span>";
			}
		else{
			if($SetDate>-5){
				$DeliveryDate="<span class='yellowB'>".$DeliveryDate."</span>";
				}
			else{
				$DeliveryDate="<span class='greenB'>".$DeliveryDate."</span>";
				}
		 }
			
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		//加密
		$Gstate=$myRow["Gstate"];

		$OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
		$ComeFrom="Supplier"; //说明来自供应商，则只已审核的图片.
		include "../model/subprogram/stuffimg_model.php";			
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
                //配件QC检验标准图
                include "../model/subprogram/stuffimg_qcfile.php";
                //配件品检报告qualityReport
                include "../model/subprogram/stuff_get_qualityreport.php";

			//显示加急色彩
			//$PorderId=substr($StockId,0,12); 
			
			//echo "StockId:$StockId:PorderId:$PorderId";
			if(($PorderId!=$old_PorderId) || $theDefaultColor=="") {  //当前配件已存在加急，是不再查询
				$theDefaultColor=$DefaultBgColor;
				//echo "123StockId:$StockId:PorderId:$PorderId";
				$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId ='$PorderId' AND  Type='7' ",$link_id);
				//echo "SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId ='$PorderId' AND  Type='7'";
					if($checkExpressRow = mysql_fetch_array($checkExpress)){
							$Type=$checkExpressRow["Type"];
							switch($Type){
								/*
								case 1:$ColbgColor="bgcolor='#0066FF'";break;	//自有产品标识
								case 2:	$LockRemark="未确定产品";
										$OrderSignColor="bgcolor='#FF0000'";
										$theDefaultColor=$DefaultBgColor;   //如果是非确定产品，则不用加急色彩. ORDER BY Type DESC 表示先处理 case 7,所以才正确。
										break;		//未确定产品
								*/		
								case 7: $old_PorderId=$PorderId;
										$theDefaultColor="#FFA6D2";
										break;		//加急
								}
						}		
			}
		
			$Qty=$myRow["Qty"];
		    $Price=$myRow["Price"];
			$Amount=$Qty*$Price;
		
			////////////////////////////////////////////////////
			//首行或新行开始
			if($midDefault=="" ||  $midDefault!=$Mid){
			  if ($midDefault!="") echo "</td></tr></table>";
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				$Choose=$SearchSign==1?"<input name='checkid[]' type='checkbox' id='checkid$i' value='$Mid' >":"<img src='../images/lock.png' width='15' height='15'>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$Choose</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$n</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";//下单日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseIDStr</td>";//下单日期
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];
				$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
                $n++;
			}
			
			//收货情况				
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
            $Mantissa=$Qty-$rkQty;
            $rkColor=$Mantissa==0?"class='greenB'":"";
			$BGcolor=$Mantissa>0?"class='redB'":"";
			
			 $Weeks=$myRow["Weeks"];
			 if ($Weeks>0){
		            $WeekName="Week " . substr($Weeks, 4, 2);
			        if ($Weeks<$curWeeks && $Mantissa>0)	 $WeekName="<span class='redB'>$WeekName</span>";
			 }
			 else{
				   $WeekName="<div class='blueB'>待 定</div>";
			 }
				
				$m=11;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";		//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";		//图档
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$GfileDate</td>";		//图档日期
				$m=$m+2;
				//echo"<td class='A0001' width='$Field[$m]' align='center'>$OrderQtyInfo</td>";  //历史订单
                //$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
				$m=$m+2;		
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";	//实购数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";	//单价
				$m=$m+2;	
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//金额
				$m=$m+2;	
				echo"<td  class='A0001' width='$Field[$m]' align='right' ><div $rkColor>$rkQty</div></td>";		//收货数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";		//欠数数量
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$WeekName</td>";		//交货日期
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='right'>$SendFloor</td>";		//送货楼层
				$m=$m+2;
			
				echo"<td  class='A0000' width='' align='center'><a href='../public/ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</td>";//出货日期
				
				echo"</tr></table>";
				$k++;$i++;
			
			
		/*	
			$ValueArray=array(
				array(0=>$StuffId,		1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$Gfile, 		1=>"align='center'"),
				array(0=>$GfileDate, 	1=>"align='center'"),
				array(0=>$OrderQtyInfo, 1=>"align='center'"),
               array(0=>$QCImage, 	1=>"align='center'"),
				array(0=>$qualityReport, 1=>"align='center'"),
				array(0=>$UnitName,		1=>"align='center'"),
				array(0=>$Qty,		1=>"align='right'"),
				array(0=>$Price, 		1=>"align='right'"),
				array(0=>$Amount,		1=>"align='right'"),
				array(0=>$SendFloor),
				array(0=>"<a href='../public/ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</div></td>", 1=>"align='center'"),
				);
	
			$checkidValue=$i."-".$StuffId;
			include "../model/subprogram/read_model_6.php";
			echo $HideTableHTML;		
			*/
		}while ($myRow = mysql_fetch_array($myResult));
		echo "</td></tr></table>";
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
//$ActioToS="65";
include "../model/subprogram/read_model_menu.php";
?>

<script   language="javascript">    
    document.body.style.backgroundColor="#FFFFFF";//改变背景色  
  
function CnameChanged(e){
	var StuffCname=e.value;
	if (StuffCname.length>=1){
	   e.style.color='#000';
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  e.style.color='#DDD';
	  document.getElementById("stuffQuery").disabled=true;
	}
}
  
function OkBtnClick(btn){
   var check=document.getElementsByName("checkid[]");
   var upSign=0;
	for (var i=0;i<check.length;i++){
			var e=check[i];
			if(e.checked){upSign=1;break; } 
	}
	
	if (upSign==0){
		alert("未选择采购单！");
	}
	else{
			document.form1.action="supplier_today_updated.php?ActionId=17";	
			document.form1.submit();		
			document.form1.target="_self";
			document.form1.action="";
	}
}
    
</script>  