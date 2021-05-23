<style type="text/css">
.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:100px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
</style>
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
//步骤2：需处理
$ColsNumber=11;
$tableMenuS=500;
ChangeWtitle("$SubCompany 未审核配件图片");
$funFrom="stuffdata";
$From=$From==""?"read":$From;
$Th_Col="选项|55|序号|40|配件Id|45|配件名称|350|App图|40|状态|30|参考买价|60|单位|45|默认供应商|100|采购|50|规格|30|备注|30|更新日期|70|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="1,17";			
$nowWebPage=$funFrom."_img";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter FROM $DataIn.stufftype T
	  LEFT JOIN $DataIn.stuffdata S ON S.TypeId=T.TypeId
	  WHERE T.Estate=1  AND  S.Picture=2 GROUP BY T.TypeId order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId']' selected>$TypeName</option>";
				$SearchRows=" and A.TypeId='$theTypeId'";
				}
			else{
				echo "<option value='$theTypeId']'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	A.Id,A.StuffId,A.StuffCname,A.Picture,A.Gremark,A.Estate,A.Price,U.Name AS UnitName,P.Forshort,M.Name,A.Spec,A.Remark,A.Date,A.Operator,A.Locks	
	FROM $DataIn.stuffdata A
	LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=A.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	WHERE 1 $SearchRows AND A.Picture=2 order by A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Spec]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		$Gremark=$myRow["Gremark"];

		include "../model/subprogram/stuffimg_model.php";
		$Estate=$myRow["Estate"];
		if($Estate==1){
			$Estate = "<div class='greenB'>√</div>";
		}else if ($Estate==2){
			$Estate = "<div class='yellowB'>√.</div>";
		}else{
			$Estate = "<div class='redB'>×</div>";
		}
		$Date=substr($myRow["Date"],0,10);
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Forshort=$myRow["Forshort"];
		$Buyer=$myRow["Name"];
		
		$URL="Stuffdata_Gfile_ajax.php";
        $theParam="StuffId=$StuffId";

		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

	//app示图
		$AppFileJPGPath="../download/stuffIcon/" .$StuffId.".jpg";
		$AppFilePNGPath="../download/stuffIcon/" .$StuffId.".png";
		$AppFilePath ="";
        if(file_exists($AppFilePNGPath)){
	       $AppFilePath  = $AppFilePNGPath;
        }else{
           if(file_exists($AppFileJPGPath)){
	          $AppFilePath =  $AppFileJPGPath; 
           }
	       else{
		       $AppFilePath ="";
	       }
        }
        
		if($AppFilePath!=""){
		       $noStatue="onMouseOver=\"window.status='none';return true\"";
			   $AppFileSTR="<span class='list' >View<span><img src='$AppFilePath' $noStatue/></span></span>";
			}
        else{
	          $AppFileSTR="&nbsp;";
        }
        		
		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$AppFileSTR,		1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$UnitName,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$Spec),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$StuffId;
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