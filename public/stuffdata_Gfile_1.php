<?php 
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../DatePicker/WdatePicker.js'></script></head>";
include "../model/subprogram/stuffimg_GfileUpLoad.php";	//扫描上传图档	

//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;

ChangeWtitle("$SubCompany 待审核配件图档");
$funFrom="stuffdata";
$From=$From==""?"Gfile":$From;
$Th_Col="选项|55|序号|40|配件Id|45|配件名称|280|图档|30|图档日期|70|状态|30|参考买价|60|单位|45|默认供应商|100|采购|50|最后下单时间|80|规格|30|备注|30|更新日期|70|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="1,73";	
//步骤3：
$nowWebPage=$funFrom."_Gfile";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项

$SearchRows.="  AND  (A.Gstate=2 or A.Gstate=6) AND A.Estate=1";
if($From!="slist"){
 	  $result = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter FROM $DataIn.stufftype T
	   LEFT JOIN $DataIn.stuffdata A ON A.TypeId=T.TypeId
	   WHERE T.Estate=1   $SearchRows
	   GROUP BY T.TypeId order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				       $SearchRows.=" and A.TypeId='$theTypeId'";
				     }
			else{
				       echo "<option value='$theTypeId'>$TypeName</option>";
				    }
			 }while ($myrow = mysql_fetch_array($result));
			 echo "</select>&nbsp;";
		  }
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr	";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0); 	
$mySql="SELECT A.Id,A.StuffId,A.StuffCname,A.Gfile,A.Gstate,A.Picture,A.Gremark,A.Estate,A.Price,U.Name AS UnitName,P.Forshort,M.Name,A.Spec,A.Remark,A.Date,A.GfileDate,A.Operator,A.Locks,MAX(GM.Date) AS LastDate
	FROM $DataIn.stuffdata A
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=A.StuffId
	LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=A.StuffId
    LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid 
    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
      LEFT JOIN $DataIn.productdata D ON D.ProductId=Y.ProductId
      LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
     LEFT JOIN $DataPublic.staffmain M2 ON M2.Number=C.Staff_Number
	WHERE 1 $SearchRows  Group BY A.StuffId order by A.Estate DESC,A.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		    $m=1;
		    $Id=$myRow["Id"];
		    $StuffId=$myRow["StuffId"];
		    $StuffCname=$myRow["StuffCname"];
		    $Price=$myRow["Price"];
		    $UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		    $Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Spec]' width='18' height='18'>";
		    $Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		    $StuffCname=$myRow["StuffCname"];
		    $Picture=$myRow["Picture"];
			$Gfile=$myRow["Gfile"];
			$Gstate=$myRow["Gstate"];  //状态
			$Gremark=$myRow["Gremark"];
			
			$LastDate=$myRow["LastDate"]==""?"&nbsp;":$myRow["LastDate"];  
			//加密
			/*
			if($Gfile!=""){
				$Gfile=anmaIn($Gfile,$SinkOrder,$motherSTR);
				if ($Gstate==2){					
					$Gfile="<img onClick='OpenOrLoad(\"$d\",\"$Gfile\",6)' src='../images/down.gif' style='background:#F00' title='图片未审核' width='18' height='18'>";  //显示红色
				    }
				else{					
					$Gfile="<img onClick='OpenOrLoad(\"$d\",\"$Gfile\",6)' src='../images/down.gif' title='$Gremark' width='18' height='18'>";
				    }
				}
			else{
				$Gfile="&nbsp;";
				}
		*/		
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
		/*
		//检查是否有图片
		if($Picture==2){
			$f=anmaIn($StuffId,$SinkOrder,$motherSTR);
			$StuffCname="<span onClick='OpenOrLoad(\"$d\",\"$f\",\"\",\"stuff\")' style='CURSOR: pointer;color:#0000CC' title='图片未审核'>$StuffCname</span>";
			}
		else{
			include "../model/subprogram/stuffimg_model.php";
			}
		*/	
		include "../model/subprogram/stuffimg_model.php";
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 0:
				$Estate="<div class='redB'>×</div>";
				break;
			case 1:
				$Estate="<div class='greenB'>√</div>";
				break;
			case 2://配件名称审核中
				$Estate="<div class='yellowB' title='配件名称审核中'>√.</div>";
				break;
			}
		
		$Date=substr($myRow["Date"],0,10);
		$GfileDate=$myRow["GfileDate"]==""?"&nbsp;":substr($myRow["GfileDate"],0,10);
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Forshort=$myRow["Forshort"];
		$Buyer=$myRow["Name"];
		$LockRemark="";
		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="StuffId=$StuffId";
		//echo "$theParam";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
      /* $verifyResult=mysql_query("SELECT Estate FROM stuffverify WHERE StuffId='$StuffId' AND Mid='$Id'",$link_id);
	   //echo "SELECT Estate FROM stuffverify WHERE StuffId='$StuffId' AND Mid='$Id'";
	   if (mysql_num_rows($verifyResult)>0){
	          $verify=mysql_result($verifyResult,0,"Estate");
	   }
	   else{
		   $verify="";
	   }
	   if($verify==""){
	             $verifyEstate="<div class='redB'>×</div>";
				 //$LockRemark="业务未初审";
			     }
			else{
			    $verifyEstate="<div class='greenB'>已审核</div>";			
			    }*/

		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'"),
			array(0=>$GfileDate, 	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$UnitName,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$LastDate, 		1=>"align='center'"),
			array(0=>$Spec),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
		//	array(0=>$verifyEstate, 1=>"align='center'"),
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