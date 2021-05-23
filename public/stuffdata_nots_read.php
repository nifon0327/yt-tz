<?php 
/*$DataIn.电信---yang 20120801
*/
//步骤1
include "../model/modelhead.php";
echo"<html>
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
//步骤2：需处理
include "../model/subprogram/stuffimg_GfileUpLoad.php";	//扫描上传图档
$ColsNumber=14;
$tableMenuS=500;
ChangeWtitle("$SubCompany 未审核配件图档");
$funFrom="stuffdata_nots";
$From=$From==""?"ts":$From;
$Th_Col="选项|55|序号|40|配件Id|45|配件名称|260|图档|30|图档日期|70|状态|30|参考买价|60|默认供应商|100|采购|50|规格|30|备注|30|更新日期|70|业务初审|80|操作|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,106";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter
     FROM $DataIn.stuffdata S
     LEFT JOIN $DataIn.stufftype T ON T .TypeId=S.TypeId
	 WHERE 1 $SearchRows AND (S.Gstate=2 or S.Gstate=6) GROUP BY T.TypeId order by Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--配件类型--</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows=" and S.TypeId='$theTypeId'";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
        $FristEstate=$FristEstate==""?1:$FristEstate;
        $FirstStr="FristEstate".$FristEstate;
        $$FirstStr="selected";
        echo"<select name='FristEstate' id='FristEstate' onchange='ResetPage(this.name)'>";
	    echo "<option value='1' $FristEstate1>未审核</option>";
        echo "<option value='0' $FristEstate0>已审核</option>";
       echo "</select>&nbsp;";
        if($FristEstate==1){
                  $SearchRows.="  AND V.Mid IS  NULL";
                    }
          else{
                 $SearchRows.=" AND V.Mid IS NOT NULL";
                   }
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT S.Id,S.StuffId,S.StuffCname,S.Gfile,S.Gstate,S.Picture,S.Gremark,S.Estate,S.Price,P.Forshort,M.Name,S.Spec,
S.Remark,S.Date,S.GfileDate,S.Operator,S.Locks	
	FROM $DataIn.stuffdata S
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
    LEFT JOIN $DataIn.stuffverify V ON V.Mid=S.Id
	WHERE 1 $SearchRows AND (S.Gstate=2 or S.Gstate=6) order by S.Estate DESC,S.Id DESC";
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
		$Spec=$myRow["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Spec]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];  //状态
		$Gremark=$myRow["Gremark"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示			
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
       $verifyResult=mysql_fetch_array(mysql_query("SELECT Estate FROM $DataIn.stuffverify WHERE StuffId='$StuffId' AND Mid='$Id'",$link_id));
	 //  echo "SELECT Estate FROM $DataIn.stuffverify WHERE StuffId='$StuffId' AND Mid='$Id'";
       $verify=$verifyResult["Estate"];
	   if($verify==""){
	             $verifyEstate="<img src='../images/register.gif'  width='30px'/>";
                 $verifyClick="onclick='tsPass(this,$Id,$StuffId)'";
			     }
			else{
			    $verifyEstate="<div class='redB'>已审核</div>";
                $verifyClick="";			
			    } 

	   $ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Gfile, 		1=>"align='center'"),
			array(0=>$GfileDate, 	1=>"align='center'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Price,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$Buyer, 		1=>"align='center'"),
			array(0=>$Spec,1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$verifyEstate,	1=>"align='center'", 2=>"$verifyClick"),
			array(0=>$Operator,		1=>"align='center'")
			);
		$checkidValue=$Id;
		//echo $StuffList$i;
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
<script language="javascript">
function tsPass(e,Id,Sid){
 
  if(confirm("初审通过确认?")) {
        var url="stuffdata_nots_ajax.php?StuffId="+Sid+"&Id="+Id+"&ActionId=ts";
		//alert(url); 
        var ajax=InitAjax(); 
	    ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
		 if(ajax.readyState==4){
			 if(ajax.responseText=="Y"){//更新成功
                 e.innerHTML="<div class='greenB'>√</div>";
			     e.onclick="";
			     }
			 else{
			     alert ("审核失败！"); 
			    }
			}
		 }
	   ajax.send(null); 
	 }
}
</script>