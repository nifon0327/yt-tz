<?php
//电信-zxq 2012-08-01
 session_start();
$Login_WebStyle="default";
include "../model/modelhead.php";
//步骤2：需处理
if ($Estate>0 && $From!="slist"){
	echo"<meta http-equiv=\"Refresh\" content='0;url=supplierpo3_read.php?Estate=$Estate'>";
}
$tableMenuS=750;
ChangeWtitle("$SubCompany 我的送货单明细");
$funFrom="gys_sh";
$From=$From==""?"read":$From;
$ColsNumber=11;
$sumCols="6,7,8";	//求和列,需处理
$MergeRows=3;	//主单列
$Th_Col="操作|30|日期|70|送货单号|80|选项|40|序号|40|配件ID|50|配件名称|300|QC图|40|品检报告|55|采购总数|60|本次送货|60|实收|60|单位|30|状 态|50|需求单流水号|100|仓管备注|200";

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 300;							//每页默认记录数量
//if ($Login_P_Number==10868) $myCompanyId=2397;
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
$SearchRows.=" AND M.CompanyId=$myCompanyId ";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows=" AND M.CompanyId=$myCompanyId";

	//结付状态
	$TempEstateSTR="EstateSTR".strval($Estate);
	$$TempEstateSTR="selected";
	echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>";
	echo"<option value='' $EstateSTR>已出</option>";
	echo"<option value='1' $EstateSTR1>未请款</option>";
	if ($myCompanyId==2029) {
	    echo"<option value='5' $EstateSTR5>未请款(FSC)</option>";
	}
	echo"<option value='2' $EstateSTR2>请款中</option>";
	echo"<option value='3' $EstateSTR3>请款通过</option>";
	echo"<option value='4' $EstateSTR4>已结付</option>";
	echo"</select>&nbsp;";

	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.gys_shmain M  WHERE 1 $SearchRows  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				       echo"<option value='$dateValue' selected>$dateValue</option>";
                       $SearchRows.=" AND  DATE_FORMAT(M.Date,'%Y-%m')='$dateValue'";
				    }
			else{
				     echo"<option value='$dateValue'>$dateValue</option>";
				   }
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：

include "../admin/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.BillNumber,M.GysNumber,M.Date,
		S.Id,S.Mid,S.StockId,S.StuffId,S.Qty,S.SendSign,S.Locks,S.Estate,D.TypeId,D.StuffCname,D.Picture,IFNULL(G.FactualQty+G.AddQty,GM.FactualQty+GM.AddQty) AS cgQty,U.Name AS UnitName   
FROM $DataIn.gys_shsheet S
LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.cg1_stuffcombox GM ON GM.StockId = S.StockId
WHERE 1 $SearchRows  ORDER BY M.Date DESC,M.Id DESC,S.Id";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//主单信息
		$Mid=$mainRows["Mid"];
		$Id=$mainRows["Id"];
		$Date=$mainRows["Date"];
		$BillNumber=$mainRows["BillNumber"];
		$GysNumber=$mainRows["GysNumber"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$GysNumber="<a href='shorder_view.php?f=$MidSTR' target='_blank'>$GysNumber</a>";
		$upMian="&nbsp;";
		$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"Gys_sh_upmain\",$Mid)' src='../images/edit.gif' alt='更新送货单备注' width='13' height='13'>";

		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId!=""){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
		    //....获得旧系统的配件ID，方面供应商和仓库比对资料
		    include "../model/subprogram/get_oldStuffCname_StuffId.php";

			$UnitName=$mainRows["UnitName"];
			$Qty=$mainRows["Qty"];
			$cgQty=$mainRows["cgQty"];
			$StockId=$mainRows["StockId"];
			$TypeId=$mainRows["TypeId"];

			$SendSign=$mainRows["SendSign"];


			$checkRkRow = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS rkQty,Id AS rkId FROM  $DataIn.ck1_rksheet WHERE StockId='$StockId' AND gys_Id ='$checkidValue'", $link_id));


			$rkQty=$checkRkRow["rkQty"]==0?"&nbsp;":$checkRkRow["rkQty"];
			$rkBgColor=$rkQty==$Qty?" class='greenB'":" class='redB'";
			//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品
			switch ($SendSign){
				case 1:
					$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
												   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$thQty=mysql_result($thSql,0,"thQty");

					//补货的数量 add by zx 2011-04-27
					$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
												   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
					$bcQty=mysql_result($bcSql,0,"bcQty");
					$cgQty=$thQty-$bcQty;
					if ($cgQty<$Qty){  //未补总数比送货还小，说明有错，显示红色,一般情况下会。
						$cgQty="<div class='redB' title='实际未补货数为：$cgQty'>$cgQty</div>";
					}

				  	$StockId="本次补货";
				 break;
				case 2:
				  $cgQty=0;
				  $StockId="本次备品";
				 break;
				default :
				 break;
			}
			 //仓管备注
			 $cgRemark="&nbsp;";
                 $remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$checkidValue' LIMIT 1",$link_id);
                 if($remarkRow=mysql_fetch_array($remarkSql)){
                       $cgRemark=$remarkRow["Remark"];
                     }

			$Locks=$mainRows["Locks"];
			$Picture=$mainRows["Picture"];
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			//检查是否有图片
			//include "../model/subprogram/stuffimg_model.php";

        $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' AND Property IN (9,10)",$link_id);
        if($PropertyRow=mysql_fetch_array($PropertyResult)){
                $Property=$PropertyRow["Property"];
                $StuffCname=$StuffCname."<img src='../images/gys$Property.png'  width='18' height='18'>";
          }
		   $Estate= $mainRows["Estate"];
		   if ($SendSign==0 && $Estate==0){
		       $qcResult= mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.qc_cjtj WHERE Sid='$checkidValue' AND Estate=1",$link_id));
		       $qcQty=$qcResult['Qty'];
		       $Estate=$qcQty>0?3:($checkRkRow["rkId"]>0?$Estate:4);

		   }

			switch($Estate){
				case 0:
				    $EstateSTR="<div class='greenB'>已收货</div>";
				  break;
				case 1:
				    $EstateSTR="<div class='redB'>未验收</div>";
				  break;
				case 2:
				    $EstateSTR="<div class='blueB'>未品检</div>";
				  break;
			    case 3:
				    $EstateSTR="<div class='blueB'>品检中</div>";
				  break;
			  case 4:
			       $checkThResult=mysql_query("SELECT G.Id,G.Remark FROM  qc_badrecord B  INNER JOIN ck12_thsheet  G ON G.Bid=B.Id  WHERE B.Sid='$Id'",$link_id);
			       if($checkThRow=mysql_fetch_array($checkThResult)){
                       $thRemark=$checkThRow["Remark"];

				       $EstateSTR="<div class='redB' title='$thRemark'>退货</div>";
			       }else{
				       $EstateSTR="<div class='redB'>无效单</div>";
			       }

			       $Locks=1;
				  break;
			}

			if($Locks==0 || $Estate==0 || $Estate==2){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
			  $Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
			}
		else{
			$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
			}

			$ComeFrom="Supplier"; //说明来自供应商，则只已审核的图片.
			include "../model/subprogram/stuffimg_model.php";

			//配件QC检验标准图
            include "../model/subprogram/stuffimg_qcfile.php";

            //配件品检报告qualityReport
            include "../model/subprogram/stuff_get_qualityreport.php";
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			////////////////////////////////////////////////////
			if($tbDefalut==0 && $midDefault==""){//首行
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center' $Remark>$GysNumber</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				}
			if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
				$m=7;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; ' bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";	//配件
				$m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$cgQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div>$Qty</div></td>";//送货数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$rkQty</div></td>";		//入库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$EstateSTR</td>";	//状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$StockId</td>";//需求流水号
				$m=$m+2;
				echo"<td  class='A0001' width=''>$cgRemark</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}
			else{
				//新行开始
				echo"</td></tr></table>";//结束上一个表格
				//并行列
				echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";	//更新
				$unitWidth=$tableWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";	//入库日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'  $Remark>$GysNumber</td>";		//下单备注
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//并行宽
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$midDefault=$Mid;
				echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; bgcolor='#FFFFFF'>";
				echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$unitFirst=$Field[$m]-1;
				echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$j</td>";			//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";	//配件
				$m=$m+2;
                echo"<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$cgQty</td>";		//需求数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div>$Qty</div></td>";//送货数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$rkQty</div></td>";		//入库数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";	//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$EstateSTR</td>";	//状态
				$m=$m+2;
				echo"<td  class='A0001' width='$Field[$m]' align='center'>$StockId</td>";	//需求流水号
				$m=$m+2;
				echo"<td  class='A0001' width='' >$cgRemark</td>";
				echo"</tr></table>";
				$i++;
				$j++;
				}
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
$Keys=31;
$ActioToS="1,3,4";
include "../admin/subprogram/read_model_menu.php";
?>
<script>
   document.body.style.backgroundColor="#fff";
</script>
