<?php   
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";

$ColsNumber=24;
$tableMenuS=1000;
ChangeWtitle("$SubCompany 来料");
$Th_Col="选项|60|序号|30|供应商|80|送货单号|80|需求单流水号|90|配件ID|50|配件名称|250|历史<br>订单|40|检讨<br>报告|40|QC图|40|REACH|50|单位|30|品检<BR>类型|40|采购总数|60|未收数量|60|收货确认|60|送货单日期|75|仓库|50|品检|50";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 500;
$ActioToS="1,2,3,4,5,7,8,107,13,40,98";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
$SearchRows=" AND S.Estate=2  AND D.CheckSign='$CheckSign' ";
if($From!="slist"){
//供应商
 $provider= mysql_query("SELECT M.CompanyId,E.Forshort
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
        LEFT JOIN $DataIn.trade_object E ON E.CompanyId=M.CompanyId 
        LEFT JOIN $DataIn.cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0 
		WHERE 1 $SearchRows AND GL.StockId IS NULL GROUP BY M.CompanyId",$link_id);// AND EX.POrderId IS NULL 
		//        LEFT JOIN $DataIn.yw2_orderexpress EX ON EX.POrderId=SUBSTRING(S.StockId,1,12) AND EX.Type=2  
	if($provideRow = mysql_fetch_array($provider)){
	echo"<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
    echo "<option value='' selected>全部</>";
		do{
			$theCompanyId=$provideRow["CompanyId"];
			$Forshort=$provideRow["Forshort"];
            //$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if ($CompanyId==$theCompanyId){
				      echo "<option value='$theCompanyId' selected>$Forshort</option>";
				      $SearchRows.=" AND M.CompanyId='$theCompanyId' ";
				    }
			else{
				      echo "<option value='$theCompanyId'>$Forshort</option>";
				    }
			}while ($provideRow = mysql_fetch_array($provider));
			echo "</select>&nbsp;";
		}
//配件类型
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE 1 $SearchRows GROUP BY T.TypeId",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND D.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
//抽检和全检
      /* $CheckSign=$CheckSign==""?2:$CheckSign;
       $checkStr="CheckSign".$CheckSign;
       $$checkStr="selected";
       echo "<select name='CheckSign' id='CheckSign' onchange='ResetPage(this.name)'>";
       echo "<option value='2' $CheckSign2>全部</>";
       echo "<option value='1' $CheckSign1>全检</>";
       echo "<option value='0' $CheckSign0>抽检</></select>&nbsp;";
       if($CheckSign<2)*/
	}
 //echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>$CencalSstr	";
$searchtable="stuffdata|S|StuffCname|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
 include "../model/subprogram/QuickSearch.php";
include "../model/subprogram/read_model_5.php";
$NowYear=date("Y");
$NowMonth=date("m");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.Picture,D.CheckSign,T.AQL,M.CompanyId,D.Price,
(G.AddQty+G.FactualQty) AS cgQty,M.Date,D.TypeId ,U.Name AS UnitName,SM.Name,E.Forshort,D.SendFloor,D.TypeId,D.Gfile,D.Gstate,D.Picture,M.BillNumber,P.cName,Y.OrderPO,Y.Qty as PQty,Y.PackRemark,Y.sgRemark,Y.ShipType,PI.Leadtime,C.Forshort AS Client,G.POrderId
FROM $DataIn.gys_shsheet S
LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
LEFT JOIN $DataPublic.staffmain SM ON SM.Number=B.BuyerId
LEFT JOIN $DataIn.trade_object E ON E.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
LEFT JOIN $DataIn.cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0 
WHERE 1   $SearchRows  AND GL.StockId IS NULL   ORDER BY E.CompanyId";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$SumQty=0;
$SumcgQty=0;
$SumnoQty=0;
$Temp_today=date("Y-m-d H:i:s");
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
         $m=1;
		$czSign=1;
		$AskDay="";
		$thisBuyRMB=0;
		$OrderSignColor="bgColor='#FFFFFF'";
		$theDefaultColor=$DefaultBgColor;
         $UnitName=$myRow["UnitName"];       
		$Id=$myRow["Id"];					//记录ID
		$Date=$myRow["Date"];				//送货单生成日期
		$StockId=$myRow["StockId"];			//配件需求流水号
		$StuffId=$myRow["StuffId"];			//配件ID
        $TypeId=$myRow["TypeId"];    //配件类型
		$StuffCname=$myRow["StuffCname"];	//配件名称
        $BillNumber=$myRow["BillNumber"];	//送货单号
        $Forshort=$myRow["Forshort"];	//送货单号
        $CheckSign=$myRow["CheckSign"];   //品检要求：0－抽检，1－全检
		$cgQty=$myRow["cgQty"];				//采购总数
		$Qty=$myRow["Qty"];					//供应商送货数量
		$Picture=$myRow["Picture"];			//配件图片
		$AQL=$myRow["AQL"];


		$OrderPO=toSpace($myRow["OrderPO"]);
		$POrderId=$myRow["POrderId"];
		$tdBGCOLOR=$POrderId==""?"bgcolor='#FFCC99'":"";
		$PQty=$myRow["PQty"];
		$PackRemark=$myRow["PackRemark"];
		$sgRemark=$myRow["sgRemark"];
		$ShipType=$myRow["ShipType"];
		$Leadtime=$myRow["Leadtime"];		
        $cName=$myRow["cName"];
		$Client=$myRow["Client"];      
		if($Picture==1){//有PDF文件
			//$StuffCname="<a href='../download/stufffile/".$StuffId.".pdf' target='_blank'>$StuffCname</a>";
			include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
			}
		//检讨报告
		$CaseReport="&nbsp;";
        $checkCaseSql=mysql_query("SELECT E.Picture,E.Title FROM $DataIn.casetostuff C
                                    LEFT JOIN $DataIn.errorcasedata  E ON E.Id=C.cId
                                    WHERE C.StuffId='$StuffId' LIMIT 1",$link_id);
         if($checkCaseRow=mysql_fetch_array($checkCaseSql)){
		       $Picture=$checkCaseRow["Picture"];
		       $f1=anmaIn($Picture,$SinkOrder,$motherSTR);
			   $d1=anmaIn("download/errorcase/",$SinkOrder,$motherSTR);
	           $CaseReport="<img onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' src='../images/warn.gif'  width='18' height='18'>";
	      }
		//配件QC检验标准图
        include "../model/subprogram/stuffimg_qcfile.php";
               
		//add by zx 2011-0427  begin
		$CompanyId=$myRow["CompanyId"];
		$SendSign=$myRow["SendSign"];
		$SignString="";
		//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品 
		switch ($SendSign){
			case 1:
				$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  
				FROM $DataIn.ck2_thmain M  					   
				LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
				WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$thQty=mysql_result($thSql,0,"thQty");

				$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  
				FROM $DataIn.ck3_bcmain M 
				LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
				WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$bcQty=mysql_result($bcSql,0,"bcQty");	
				$cgQty=$thQty-$bcQty;
				$noQty=$cgQty;
				$SignString="(补货)";
				$StockId="本次补货";
			 break;
			case 2:
			  $cgQty=0;
			  $noQty=0;
			  $SignString="(备品)";
			  $StockId="本次备品";
			 break;
			default :
				$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty 
				    FROM $DataIn.ck1_rksheet R 
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
					WHERE R.StockId='$StockId'",$link_id);
				$rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
				$noQty=$cgQty-$rkQty;				
			 break;
		   }		
             $ck_cyle="&nbsp;";$pj_cyle="&nbsp;";
             $sh_Result=mysql_fetch_array(mysql_query("SELECT shDate FROM $DataIn.gys_shdate WHERE Sid='$Id' ORDER BY shDate DESC  LIMIT 1",$link_id));
             $sh_date=$sh_Result["shDate"];
	        if($sh_date==""){
                     $ck_cyle="&nbsp;";
                      //$pj_cyle=ceil((strtotime($Temp_today)-strtotime($Date))/3600)."小时";
                      $pj_cyle="&nbsp;";
                      }
		     else{
                     $ck_cyle=ceil((strtotime($sh_date)-strtotime($Date))/3600)."小时";
                      $pj_cyle=ceil((strtotime($Temp_today)-strtotime($sh_date))/3600)."小时";
                    }
             $CheckSign=$CheckSign==1?"<div style='color:#F00;'>全检</div>":"<div style='color:#060;'>抽检</div>";
			 $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>view</a>";
		    include "../model/subprogram/stuffreach_file.php";
            $SumcgQty +=$cgQty; 
            $SumnoQty+=$noQty; 
            $SumQty +=$Qty; 
            $Date=substr($Date,5,5)."/"."<span class='greenB'>".substr($Date,11,5)."</span>";



	/*加入同一单的配件  // add by zx 2011-08-04 */
if($POrderId!=""){
		$showPurchaseorder="<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"public\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' ><br> &nbsp;PO：$OrderPO&nbsp;<span class='redB'>业务单流水号：$POrderId </span>($Client : $cName)&nbsp;<span class='redB'>数量：$PQty </span>&nbsp;订单备注：$PackRemark <span class='redB'>出货方式：$ShipType</span> 生管备注：$sgRemark <span class='redB'>PI交期：$Leadtime</span></td>
			</tr>
			
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
          }
else  {
          $showPurchaseorder="";
          $StuffListTB="";
         }
		    $ValueArray=array( 
			array(0=>$Forshort),
			array(0=>$BillNumber, 		1=>"align='center'"),
			array(0=>$StockId, 		1=>"align='center'"),
			array(0=>$StuffId, 		1=>"align='center'"),
            array(0=>$StuffCname),
            array(0=>$OrderQtyInfo, 	1=>"align='center'"),
			array(0=>$CaseReport,		1=>"align='center'"),
			array(0=>$QCImage,		1=>"align='center'"),
			array(0=>$ReachImage, 		1=>"align='center'"),
			array(0=>$UnitName, 		1=>"align='center'"),
			array(0=>$CheckSign, 		1=>"align='center'"),
			array(0=>$cgQty, 		1=>"align='right'"),
			array(0=>$noQty, 	    1=>"align='right'"),
			array(0=>$Qty, 	        1=>"align='right'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$ck_cyle, 	1=>"align='center'"),
			array(0=>$pj_cyle, 	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
        echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>		
<tr>
<td class='A0111' align='center' height='30'>总计</td>
<td class='A0101' width=60 align='right' >$SumcgQty</td>
<td class='A0101' width=60 align='right' >$SumnoQty</td>
<td class='A0101' width=60 align='right' >$SumQty</td>
<td class='A0101' width=75 >&nbsp;</td>
<td class='A0101' width=50 >&nbsp;</td>
<td class='A0101' width=50 >&nbsp;</td>
</tr></table>";
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