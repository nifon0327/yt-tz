<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:400px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0;} 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php 
//步骤1$DataIn.电信---yang 20120801
include "../model/modelhead.php";

echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
	
//include "../model/subprogram/stuffimg_GfileUpLoad.php";	//扫描上传图档	
//步骤2：需处理
$upFlag=$_GET["ID"];
if ($upFlag!=""){
 $JobType=$upFlag;
}
$ColsNumber=30;
$tableMenuS=1000;
ChangeWtitle("$SubCompany 配件开发审核");
$funFrom="stuffdevelop";
$From=$From==""?"m":$From;
if($StuffType!=9040){
	$Th_Col="选项|55|序号|40|配件Id|50|配件名称|320|图档|30|图档日期|70|历史<br>订单|40|QC图|40|认证|40|开发|80|品检</br>方式|40|状态|30|参考买价|60|单位|40|装箱数量|60|配件类型|60|默认供应商|100|采购|50|交货<br>周期|40|送货</br>楼层|40|规格|120|主产品<br>重(g)|50|在库|55|可用库存|55|最低库存|55|购买选择|50|备注|30|更新日期|80|下单需求|80|图片职责|80|图档职责|80|图档审核|60|操作|50";
	}
else{
	$Th_Col="选项|55|序号|40|配件Id|50|配件名称|280|图档|30|图档日期|70|历史<br>订单|40|QC图|40|认证|40|开发|80|品检</br>方式|40|状态|30|参考买价|60|单位|40|装箱数量|60|配件类型|60|默认供应商|100|采购|50|交货<br>周期|40|送货</br>楼层|40|规格|120|主产品<br>重(g)|50|承重<br>(kg)|40|在库|55|可用库存|55|最低库存|55|购买选择|50|备注|30|更新日期|80|下单需求|80|图片职责|80|图档职责|80|图档审核|60|操作|50";
	}
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
//$ActioToS="1,2,3,4,5,7,8,107,13,40,98";
$ActioToS="1,17,15";

$nowWebPage=$funFrom."_m";
//echo $nowWebPage;
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	/*
	$SearchRows="";
	$result = mysql_query("SELECT * FROM $DataIn.stufftype WHERE Estate=1 order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected >$TypeName</option>";
				$SearchRows=" AND A.TypeId='$theTypeId' ";
				$NameRule=$myrow["NameRule"];
				}
			else{
				echo "<option value='$theTypeId'  >$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	echo"<select name='PJobType' id='PJobType' onchange='ResetPage(this.name)'>";
	echo"<option value='' >--图片职责--</option>";
	$mySql="SELECT j.Id,j.GroupName FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34) AND M.Estate>0 And M.cSign = '$Login_cSign' GROUP BY J.Id order by j.Id,j.GroupName";
	$result = mysql_query($mySql,$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$jobId=$myrow["Id"];
			$jobName=$myrow["GroupName"];
			if ($jobId==$PJobType){
				echo "<option value='$jobId' selected>$jobName</option>";
				$SearchRows.=" AND (A.PJobId='$jobId' || (A.PJobId=-1 AND G.Picjobid='$jobId' )) AND A.Estate>0 ";
				}
			else{
				echo "<option value='$jobId'>$jobName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
		}
    echo "</select>&nbsp;";		
		
	echo"<select name='JobType' id='JobType' onchange='ResetPage(this.name)'>";
	echo"<option value='' >--图档职责--</option>";
	$mySql="SELECT j.Id,j.GroupName FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34) AND M.Estate>0 And M.cSign = '$Login_cSign' GROUP BY J.Id order by j.Id,j.GroupName";
	$result = mysql_query($mySql,$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$jobId=$myrow["Id"];
			$jobName=$myrow["GroupName"];
			if ($jobId==$JobType){
				echo "<option value='$jobId' selected>$jobName</option>";
				$SearchRows.=" AND (A.JobId='$jobId' || (A.JobId=-1 AND G.Gicjobid='$jobId' ) ) AND A.Estate>0 ";
				}
			else{
				echo "<option value='$jobId'>$jobName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
		}
    echo "</select>&nbsp;";
    
    //开发状态
    $DevelopStateSTR="DevelopStateStr".strval($DevelopState); 
	$$DevelopStateSTR="selected";
	echo"<select name='DevelopState' id='DevelopState' onchange='ResetPage(this.name)'>";
		echo"<option value='' $DevelopStateStr>--全部配件--</option>
		<option value='1' style= 'color:#FF00CC;' $DevelopStateStr1>需开发</option>
		<option value='2' style= 'color:#FF00CC;' $DevelopStateStr2>未分配</option>
		<option value='3' style= 'color:#FF0000;' $DevelopStateStr3>开发中</option>
		<option value='4' style= 'color:#FF6633;' $DevelopStateStr4>已开发</option>
	</select>&nbsp;";
	
	switch($DevelopState){
	   case 1:
	       $SearchRows.=" AND  S.DevelopState='1' ";
	      break;
	  case 2:
	      $SearchRows.=" AND  S.DevelopState='1' AND DP.StuffId IS NULL ";
	    break;
	  case 3:
	      $SearchRows.=" AND  S.DevelopState='1' AND DP.Estate=1 ";
	     break;
	   case 4:
	     $SearchRows.=" AND  S.DevelopState='1' AND DP.Estate=0 AND DP.StuffId>0 ";
	     break;
	}*/
}
else  {

}
	

  echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页   </option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
  $searchtable="stuffdata|A|StuffCname|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
  include "../model/subprogram/QuickSearch.php";
 // echo "<a href='../model/subprogram/stuffimg_PfileUpLoad.php' target='_blank'  title=''><font color='red'>图片自动加载</font></a>"	;
  
  //echo "&nbsp;&nbsp; <a href='../model/subprogram/stuffimg_GfileUpLoad.php' target='_blank'  title=''><font color='red'>图档自动加载</font></a>"	;
  
  //echo "&nbsp;&nbsp;<a href='stuffdata_size.php' target='_blank'  title=''><font color='red'>配件关联产品重量</font></a>"	;
  
//}	
  echo "<input name='AcceptText' type='hidden' id='AcceptText' value='$upFlag'>";
//步骤5：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";

echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";

//步骤6：需处理数据记录处理
if($NameRule!=""){
  echo "<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#FFFFFF' width='$tableWidth' ><tr ><td height='15' class='A0011' ><span style='color:red'>命名规则:</span>$NameRule</td></tr></table>";
  }
  $NowYear=date("Y");
$NowMonth=date("m");
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="
	SELECT 
	DP.Id,A.StuffId,A.StuffCname,A.StuffEname,A.TypeId,A.Gfile,A.Gstate,A.Picture,
	IF(A.Pjobid=-1,G.PicNumber ,A.PicNumber) as PicNumber,IF(A.Pjobid=-1,M.GroupName,K.GroupName) as PJobname,
	IF(A.Jobid=-1,N.GroupName,F.GroupName) as GJobname,IF(A.jobid=-1,G.GicNumber ,A.GicNumber) as GicNumber,
	A.Gremark,A.Estate,A.Price,A.SendFloor,A.jhDays,E.Forshort,B.BuyerId,C.Name,A.Spec,A.Remark,A.Weight,A.Date,
   IF(A.GcheckNumber>0,L.Name,IF(A.GcheckNumber=-1,'系统默认','不需图档审核')) as GcheckNumber,A.DevelopState,A.BoxPcs,A.GfileDate,A.ForcePicSpe,A.Operator,A.Locks,A.CheckSign,H.mStockQty,H.tStockQty,H.oStockQty,G.TypeName,
G.ForcePicSign,G.jhDays AS TypeJhDays,D.Name AS UnitName,B.CompanyId,DP.dFile as developFile,DP.GroupId as DPGroupId,DP.Number as DPNumber
	FROM $DataIn.stuffdevelop DP
	LEFT JOIN  $DataIn.stuffdata A ON DP.StuffId=A.StuffId
	LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
	LEFT JOIN $DataPublic.staffmain C ON C.Number=B.BuyerId 
	LEFT JOIN  $DataPublic.stuffunit D ON D.Id=A.Unit
	LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId  AND  E.ObjectSign IN (1,3) 
	LEFT JOIN $DataIn.staffgroup K ON K.Id=A.Pjobid 
	LEFT JOIN $DataIn.staffgroup F ON F.Id=A.Jobid 
	LEFT JOIN $DataIn.stufftype G ON G.TypeId=A.TypeId  
	LEFT JOIN  $DataIn.staffgroup M ON M.Id=G.Picjobid 
	LEFT JOIN $DataIn.staffgroup N ON N.Id=G.GicJobid 
	LEFT JOIN $DataPublic.staffmain L ON L.Number=A.GcheckNumber 
	LEFT JOIN $DataIn.ck9_stocksheet H ON H.StuffId=A.StuffId
    LEFT JOIN  $DataIn.stuffproperty  P ON P.StuffId=A.StuffId 
	WHERE DP.Estate=2 $SearchRows  $ShipMonthStr ";
	//    LEFT JOIN  $DataIn.stuffproperty  P ON P.StuffId=A.StuffId  为了查询
	
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$TypeName=$myRow["TypeName"];
		$StuffEname=$myrow["StuffEname"]==""?"&nbsp;":$myrow["StuffEname"];
		$Price=$myRow["Price"];
		$Spec=$myRow["Spec"]==""?"&nbsp;":$myRow["Spec"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];
		//交货周期
		$jhDays=$myRow["jhDays"]==0?$myRow["TypeJhDays"]:$myRow["jhDays"];
		$jhDays=$jhDays==$myRow["TypeJhDays"]?$jhDays:"<span class='yellowB'>$jhDays</span>";
		//$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"]==""?"-----":$myRow["Forshort"];
		$ForcePicSpe=$myRow["ForcePicSpe"];
		$ForcePicSign=$myRow["ForcePicSign"];
		if ($ForcePicSpe>=0){  //-1表示用stufftype用的，否则用它指定
			$ForcePicSign=$ForcePicSpe;  
		}
		
	
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
		
		$BoxPcs=$myRow["BoxPcs"]==0?"&nbsp;":$myRow["BoxPcs"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Picture=$myRow["Picture"];
        $TypeId=$myRow["TypeId"];
		if($StuffType==9040){
			//读取外箱承重
			$checkLoadBearing=mysql_fetch_array(mysql_query("SELECT IFNULL(Weight1,0) AS Weight1 FROM $DataIn.stuff_loadbearing WHERE StuffId='$StuffId' LIMIT 1",$link_id));
			$Weight1=$checkLoadBearing["Weight1"]==0?"<span class='redB'>未设置</span>":$checkLoadBearing["Weight1"];
			}
         //配件QC检验标准图
         $QCImage="";
         include "../model/subprogram/stuffimg_qcfile.php";
         $QCImage=$QCImage==""?"&nbsp;":$QCImage;
               
         $CheckSign=$myRow["CheckSign"];
         switch($CheckSign){
                  case "0":$CheckSign="抽检";break;
                  case "1":$CheckSign="<div style='color:#E00;' >全检</div>";break;
                  case "99":$CheckSign="-----";break;
              }
		include "../model/subprogram/stuffreach_file.php";	//认证
		$mStockQty=$myRow["mStockQty"];
		$mStockQty=$mStockQty==0?"&nbsp;":$mStockQty;
		$oStockQty=$myRow["oStockQty"];
		$oStockQty=$oStockQty==0?"&nbsp;":$oStockQty;
		$tStockQty=$myRow["tStockQty"];
		$tStockQty=$tStockQty==0?"&nbsp;":$tStockQty;
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
        include"../model/subprogram/stuff_Property.php";//配件属性
        
        $changeRemark="";$OrderSignColor="";
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
		$GJobname=$GJobname==""?"&nbsp;":$GJobname."-$GicName";
		
		$Weight=$myRow["Weight"];
		if ($Weight>0){
			$Weight=number_format($Weight, 3, '.', '');
		}
		else{
			$Weight="&nbsp;";
		}
		
		$GcheckNumber=$myRow["GcheckNumber"];	
		
		$SendFloor=$myRow["SendFloor"];
		include "../model/subprogram/stuff_GetFloor.php";
		$SendFloor=$SendFloor=""?"&nbsp":$SendFloor;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Buyer=$myRow["Name"]==""?"-----":$myRow["Name"];
		//登录人员不是属于采购者锁定操作
		$BuyerId=$myRow["BuyerId"];

		$DevelopState=$myRow["DevelopState"];
		$developFile=$myRow["developFile"];
		include "../model/subprogram/stuff_developstate.php";
		 $DPGroupId=$myRow["DPGroupId"];
		 $DPNumber=$myRow["DPNumber"];
		 
		 if($DPGroupId<=0 || $DPNumber<=0){
			 $LockRemark="错误，未分配！";
		 }
		 
       $BuyChoose="";
		$CheckStuffBuyResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.stuffbuy WHERE StuffId=$StuffId",$link_id));
		$CheckStuffBuyId=$CheckStuffBuyResult["Id"];
		if($CheckStuffBuyId!=""){
                  $BuyChoose="<img src='../images/ok.gif' width='30' height='30'";
		   }


         //历史订单
        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
        //最后出货日期
		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="StuffId=$StuffId";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
     if($Login_P_Number==10051 || $Login_P_Number==10871 || $Login_P_Number==10007 || $Login_P_Number==10341){
           $BuyChooseStr=" onmousedown='window.event.cancelBubble=true;'onclick='ChooseBuy($i,$StuffId)' style='CURSOR: pointer'";
       }
    else  $BuyChooseStr="";
		if($StuffType!=9040){	
			$ValueArray=array(
				array(0=>$StuffId, 		1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$Gfile, 		1=>"align='center'"),
				array(0=>$GfileDate, 	1=>"align='center'"),
				array(0=>$OrderQtyInfo, 1=>"align='center'"),
				array(0=>$QCImage, 	    1=>"align='center'"),
				array(0=>$ReachImage, 	1=>"align='center'"),
				array(0=>$DevelopState.' '.$developFile.$Develop_m,		1=>"align='center'"),
				array(0=>$CheckSign, 	1=>"align='center'"),
				array(0=>$Estate,		1=>"align='center'"),
				array(0=>$Price,		1=>"align='center'"),
				array(0=>$UnitName,		1=>"align='center'"),
				array(0=>$BoxPcs,		1=>"align='center'"),
				array(0=>$TypeName),
				array(0=>$Forshort),
				array(0=>$Buyer, 		1=>"align='center'"),
				array(0=>$jhDays . "天",1=>"align='center'"),
				array(0=>$SendFloor, 	1=>"align='center'"),
				array(0=>$Spec,1=>"align='left'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,18,$StuffId,3)' style='CURSOR: pointer'"),
				array(0=>$Weight, 		1=>"align='center'"),
				array(0=>$tStockQty, 		1=>"align='center'"),
				array(0=>$oStockQty, 		1=>"align='center'"),
				array(0=>$mStockQty, 		1=>"align='center'"),
				array(0=>$BuyChoose, 		1=>"align='center'",2=>$BuyChooseStr),
				array(0=>$Remark, 		1=>"align='center'"),
				array(0=>$Date, 		1=>"align='center'"),
				array(0=>$ForcePicSign,1=>"align='center'"),
				array(0=>$PJobname, 		1=>"align='center'"),
				array(0=>$GJobname, 		1=>"align='center'"),
				array(0=>$GcheckNumber, 		1=>"align='center'"),
				array(0=>$Operator,		1=>"align='center'")
				);
			}
		else{
			$ValueArray=array(
				array(0=>$StuffId, 		1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$Gfile, 		1=>"align='center'"),
				array(0=>$GfileDate, 	1=>"align='center'"),
				array(0=>$OrderQtyInfo, 1=>"align='center'"),
				array(0=>$QCImage, 	    1=>"align='center'"),
				array(0=>$ReachImage, 	1=>"align='center'"),
				array(0=>$DevelopState.' '.$developFile.$Develop_m,		1=>"align='center'"),
				array(0=>$CheckSign, 	1=>"align='center'"),
				array(0=>$Estate,		1=>"align='center'"),
				array(0=>$Price,		1=>"align='center'"),
				array(0=>$UnitName,		1=>"align='center'"),
				array(0=>$BoxPcs,		1=>"align='center'"),
				array(0=>$TypeName),
				array(0=>$Forshort),
				array(0=>$Buyer, 		1=>"align='center'"),
				array(0=>$jhDays . "天",1=>"align='center'"),
				array(0=>$SendFloor, 	1=>"align='center'"),
				array(0=>$Spec,1=>"align='left'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,18,$StuffId,3)' style='CURSOR: pointer'"),
				array(0=>$Weight, 		1=>"align='center'"),
				array(0=>$Weight1,  1=>"align='center' style='CURSOR: pointer'",
                              2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateLoadBearing($i,$StuffId)'"),
				array(0=>$tStockQty, 		1=>"align='center'"),
				array(0=>$oStockQty, 		1=>"align='center'"),
				array(0=>$mStockQty, 		1=>"align='center'"),
				array(0=>$Remark, 		1=>"align='center'"),
				array(0=>$BuyChoose, 		1=>"align='center'",2=>$BuyChooseStr),
				array(0=>$Date, 		1=>"align='center'"),
				array(0=>$ForcePicSign,1=>"align='center'"),
				array(0=>$PJobname, 		1=>"align='center'"),
				array(0=>$GJobname, 		1=>"align='center'"),
				array(0=>$GcheckNumber, 		1=>"align='center'"),				
				array(0=>$Operator,		1=>"align='center'")
				);
			}
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
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>   
<script language="JavaScript" type="text/JavaScript">
<!--
function ChooseBuy(index,StuffId){
      	var tabIndex="ListTable" + index;
	var url="stuffdata_choosebuy.php?StuffId="+StuffId;
	 var ajax=InitAjax();
　	ajax.open("GET",url,true);
        	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                 			var BackData=ajax.responseText;
                           if(BackData=="Y")document.getElementById(tabIndex).rows[0].cells[25].innerHTML="<img src='../images/ok.gif' width='30' height='30'>";   
                           else document.getElementById(tabIndex).rows[0].cells[25].innerHTML="";        
			}
		}
　	ajax.send(null);
}
function checkChange(obj){
	var e=document.getElementById("checkAccept");
    if (e.checked){
	  	//document.getElementById("AcceptText").value="";
		document.location.replace("../Admin/stuffdata_read.php");
		}
	}
function updateLoadBearing(index,Ids){
     var inputStr=prompt("请输入外箱承重极限");
     if(inputStr) {
        inputStr=inputStr.replace(/(^\s*)|(\s*$)/g,"");  //去除前后空格
        //数字检查
		var checkValue=fucCheckNUM(inputStr,"Price");
		if(checkValue==0){
		alert("格式不符");
		return false;}
		var url="cg_cgdsheet_updated.php?Sid="+Ids+"&ActionId=702&Weight="+inputStr; 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){// && ajax.status ==200
		// alert(ajax.responseText);
			 if(ajax.responseText=="Y"){//更新成功
             	var tabIndex="ListTable" + index;
                var TDid=document.getElementById(tabIndex).rows[0].cells[20];
                if (inputStr==0){
                	TDid.innerHTML="<span class='redB'>未设置</span>"; 
                    }
				else{
                	TDid.innerHTML=inputStr; 
                    }
			     }
			 else{
			    alert ("更新失败！"); 
			  }
			}
		 }
	   ajax.send(null); 
	 }
 }



function updateJq(TableId,RowId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	if(toObj==25){theDiv.style.left=event.clientX + document.body.scrollLeft+'px';}
	else{
		theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
	}
	//theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 3:	//订单备注
				InfoSTR="更新配件ID为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly> 的规格:<input name='Spec' type='text' id='Spec' size='20' class='INPUT0100'><br>";
				break;					
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) { 
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9; 
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	if (isIe()) {  
		theDiv.filters.revealTrans.apply();
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	closeMaskBack();    
	}

function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
		case "3":		
			var Spec=document.form1.Spec.value;
			var tempSpec=encodeURIComponent(Spec);
			myurl="stuffdata_ajax.php?StuffId="+temprunningNum+"&tempSpec="+tempSpec+"&ActionId=Spec";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' ><NOBR>"+Spec+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;				
		}
	}

</script>