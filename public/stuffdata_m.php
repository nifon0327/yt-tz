<?php
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
//步骤2：需处理
$ColsNumber=26;
$tableMenuS=500;
ChangeWtitle("$SubCompany 配件名称审核列表");
$funFrom="stuffdata";
$From=$From==""?"m":$From;
$Th_Col="选项|55|序号|40|配件类型|80|配件Id|50|配件名称|280|图档|30|图档日期|70|开发|40|历史<br>订单|40|QC图|40|认证|40|品检</br>方式|40|状态|30|含税价|60|单位|40|默认供应商|100|交货<br>周期|40|送货</br>楼层|40|采购|50|规格|120|主产品<br>重(g)|50|最低库存|60|备注|30|更新日期|80|下单需求|80|图片职责|85|图档职责|85|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="17,162";
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows=" AND A.Estate=2";
	$result = mysql_query("SELECT T.TypeId,T.Letter,T.TypeName,T.NameRule
	FROM $DataIn.stufftype T
	LEFT JOIN $DataIn.stuffdata A ON A.TypeId=T.TypeId
	WHERE 1 $SearchRows
	group by T.TypeId order by T.Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND   A.TypeId='$theTypeId'";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
 $TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
//步骤5：
include "../model/subprogram/read_model_5.php";

if($NameRule!=""){
  echo "<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#FFFFFF'><tr ><td height='15' class='A0011' width='$tableWidth' >
       <span style='color:red'>命名规则:</span>$NameRule
	   </td></tr></table>";
  }
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
	SELECT 
	A.Id,A.StuffId,A.StuffCname,A.StuffEname,A.TypeId,A.Gfile,A.Gstate,A.Picture,
	IF(A.Pjobid=-1,G.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.GroupName,K.GroupName) as PJobname,
	IF(A.Jobid=-1,G.GicNumber ,A.GicNumber) as GicNumber,IF(A.Jobid=-1,N.GroupName,F.GroupName) as GJobname,
	A.Gremark,A.Estate,A.Price,A.SendFloor,A.jhDays,E.Forshort,B.BuyerId,C.Name,A.Spec,A.Remark,A.Weight,A.Date,B.CompanyId,
	A.GfileDate,A.ForcePicSpe,A.Operator,A.Locks,A.CheckSign,H.mStockQty,G.TypeName,G.ForcePicSign,G.jhDays AS TypeJhDays,D.Name AS UnitName,A.DevelopState,A.NoTaxPrice,A.Price,A.CostPrice,A.PriceDetermined 
	FROM $DataIn.stuffdata A 
	LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
	LEFT JOIN $DataPublic.staffmain C ON C.Number=B.BuyerId 
	LEFT JOIN  $DataPublic.stuffunit D ON D.Id=A.Unit
	LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
	LEFT JOIN $DataIn.staffgroup K ON K.Id=A.Pjobid 
	LEFT JOIN $DataIn.staffgroup F ON F.Id=A.Jobid 
	LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId  
	LEFT JOIN $DataIn.staffgroup M ON M.Id=G.Picjobid 
	LEFT JOIN $DataIn.staffgroup N ON N.Id=G.GicJobid 
	LEFT JOIN $DataIn.ck9_stocksheet H ON H.StuffId=A.StuffId
	WHERE 1 $SearchRows  
	ORDER BY A.Estate DESC,A.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$tmpStuffCname=$StuffCname;
		$TypeName=$myRow["TypeName"];
		$StuffEname=$myrow["StuffEname"]==""?"&nbsp;":$myrow["StuffEname"];
		$Price=$myRow["Price"];
		$CostPrice =$myRow["CostPrice"];
		$NoTaxPrice =$myRow["NoTaxPrice"];
		$PriceDetermined =$myRow["PriceDetermined"];
		$Spec=$myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];
		$Forshort=$myRow["Forshort"];
		//交货周期
		$jhDays=$myRow["jhDays"]==0?$myRow["TypeJhDays"]:$myRow["jhDays"];
		$jhDays=$jhDays==$myRow["TypeJhDays"]?$jhDays:"<span class='yellowB'>$jhDays</span>";
		
		$ForcePicSpe=$myRow["ForcePicSpe"];
		$ForcePicSign=$myRow["ForcePicSign"];
		if ($ForcePicSpe>=0){  //-1表示用stufftype用的，否则用它指定
			$ForcePicSign=$ForcePicSpe;  
		}
		
		$DevelopState=$myRow["DevelopState"];
		include "../model/subprogram/stuff_developstate.php";

		switch($ForcePicSign){
			case 0: 
				$ForcePicSign="--";
			break;
			case 1: 
				$ForcePicSign="图片";
			break;
			case 2: 
				$ForcePicSign="图档";
			break;
			case 3: 
				$ForcePicSign="图片/图档";
			break;
			case 4: 
				$ForcePicSign="强行锁定";
			break;			
		}		
		
		
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Picture=$myRow["Picture"];
                $TypeId=$myRow["TypeId"];
                //配件QC检验标准图
               $QCImage="";
               include "../model/subprogram/stuffimg_qcfile.php";
               $QCImage=$QCImage==""?"&nbsp;":$QCImage;
               
                $CheckSign=$myRow["CheckSign"];
                
        switch($CheckSign){
	        case 1:  $CheckSign="<div style='color:#E00;' >全检</div>";break;
	        case 99:$CheckSign="-----";break;
	        default:$CheckSign="抽检";break;
        }
		include "../model/subprogram/stuffreach_file.php";	
		$mStockQty=$myRow["mStockQty"];
		$mStockQty=$mStockQty==0?"&nbsp;":$mStockQty;
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
        include"../model/subprogram/stuff_Property.php";//配件属性
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
			case 2://配件名称审核中
			    $changeResult=mysql_query("SELECT A.oldStuffCname,A.oldPrice,A.oldCompanyId,A.Reason,B.Forshort 
			                 FROM $DataIn.stuffchange A
						     LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.oldCompanyId 
						     WHERE A.StuffId='$StuffId' AND A.Estate=1",$link_id);
			    $changeRemark="";$OrderSignColor="";
				if($changeRow=mysql_fetch_array($changeResult)){
				    do{
					     $oldStuffCname=$changeRow["oldStuffCname"];
					     
					     if ($oldStuffCname!=$tmpStuffCname){
						     $changeRemark.="原名称: $oldStuffCname";
					     }
					     
					     $oldPrice=$changeRow["oldPrice"];
					     if ($oldPrice!=$Price){
						     $changeRemark.="原价格: $oldPrice";
					     }
					     
					     $oldForshort=$changeRow["Forshort"];
					     if ($oldForshort!=$Forshort){
						      $changeRemark.="原供应商: $oldForshort";
					     }
					     
					     $Reason=$changeRow["Reason"];
					     $changeRemark=$Reason . "; " . $changeRemark;
				    }while($changeRow=mysql_fetch_array($changeResult));
				    
				     $Estate="<div class='yellowB' title='$changeRemark'>√.</div>"; 
					 $OrderSignColor=" bgcolor='#FFCC00'";
				}
				else{
				      $Estate="<div class='yellowB' title='配件名称审核中'>√.</div>";
				}
				break;
				case 3:
				{
					$returnReasonSql = mysql_query("Select * From $DataPublic.returnreason Where tableId = '$Id' and targetTable = '$DataIn.stuffdata' order by DateTime Desc Limit 1");
					$returnReasonRows = mysql_fetch_assoc($returnReasonSql);
					$returnReason = substr($returnReasonRows["DateTime"],0,16).":".$returnReasonRows["Reason"];
					$Estate="<div class='blueB' title='退回-$returnReason'>√.</div>";
				}
				break;
			}
		
		$Date=substr($myRow["Date"],0,10);
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		
		$PicNumber=$myRow["PicNumber"];
		$PicName="";
		if ($PicNumber!=0 ){  //说明指定的人
			$Operator=$PicNumber;
			include "../model/subprogram/staffname.php";	
			$PicName=$Operator;
			$Operator="";
		}
		
		$PJobname=$myRow["PJobname"];
		if ($PJobname!=""){
			$PJobname=$PicName==""?"$PJobname-(未指定人)":$PJobname."-$PicName";
		}
		else {
			$PJobname="&nbsp;";
		}
		
		$GicNumber=$myRow["GicNumber"];
		$GicName="";
		if ($GicNumber!=0 ){  //说明指定的人
			$Operator=$GicNumber;
			include "../model/subprogram/staffname.php";	
			$GicName=$Operator;
			$Operator="";
		}
		
		$GJobname=$myRow["GJobname"];	
		if ($GJobname!=""){
			$GJobname=$GicName==""?"$GJobname-(未指定人)":$GJobname."-$GicName";
		}
		else {
			$GJobname="&nbsp;";
		}
			
		//$GJobname=$GJobname==""?"&nbsp;":$GJobname;
		$Weight=$myRow["Weight"];
		if ($Weight>0){
			$Weight=number_format($Weight, 3, '.', '');
		}
		else{
			$Weight="&nbsp;";
		}
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Buyer=$myRow["Name"];
		//登录人员不是属于采购者锁定操作
		$BuyerId=$myRow["BuyerId"];
		
		$CheckTaxRow  = mysql_fetch_array(mysql_query("SELECT InvoiceTax FROM $DataIn.providersheet WHERE CompanyId = '$CompanyId'",$link_id));
		$taxRate = $CheckTaxRow["InvoiceTax"];
		if($taxRate>0){
		      $taxRate = "<span class='greenB'>".$taxRate."%</span>";
		 }else{ 
			 $taxRate ="&nbsp;";
		}
		if($PriceDetermined==1 && $Price==0.00){
			$Price = "<span class='redB'>价格待定</span>";
		}
         //历史订单
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="StuffId=$StuffId";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ValueArray=array(
		    array(0=>$TypeName),
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'"),
			array(0=>$GfileDate, 	1=>"align='center'"),
			array(0=>$DevelopState, 1=>"align='center'"),
            array(0=>$OrderQtyInfo, 1=>"align='center'"),
            array(0=>$QCImage, 	    1=>"align='center'"),
			array(0=>$ReachImage, 	1=>"align='center'"),
            array(0=>$CheckSign, 	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			//array(0=>$NoTaxPrice,	1=>"align='center'"),
			//array(0=>$taxRate,	1=>"align='center'"),
			array(0=>$Price,		1=>"align='center'"),
			//array(0=>$CostPrice,	1=>"align='center'"),
			array(0=>$UnitName,		1=>"align='center'"),
			
			array(0=>$Forshort),
			array(0=>$jhDays . "天",1=>"align='center'"),
			array(0=>$SendFloor, 	1=>"align='center'"),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$Spec),
			array(0=>$Weight, 		1=>"align='center'"),
			array(0=>$mStockQty, 		1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$ForcePicSign,1=>"align='center'"),
			array(0=>$PJobname, 		1=>"align='center'"),
			array(0=>$GJobname, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
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