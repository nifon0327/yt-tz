<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
///电信-zxq 2012-08-01
include "../model/livesearch/modellivesearch.php";
$Th_Col="序号|30|报废日期|60|配件|45|配件名称|250|实物<br/>库存|50|订单<br/>库存|50|报废<br/>数量|50|库位|70|单位|30|单据|40|报废原因|180|处理结果|180|分类|80|状态|30|操作员|50|操作|40";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
}

$SearchRows="";
$GysList="";
$nowInfo="当前: 物料报废/损耗数据";
$funFrom="item5_6";
$addWebPage=$funFrom . "_add.php";
$updateWebPage=$funFrom . "_update.php";
	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.ck8_bfsheet WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		$GysList.="<select name='selbfDate' id='selbfDate' onchange='ResetPage(1,5)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$selbfDate=$selbfDate==""?$dateValue:$selbfDate;
			if($selbfDate==$dateValue){
				$GysList.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and  DATE_FORMAT(F.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$GysList.= "<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		$GysList.="</select>&nbsp;";
		}
	//分类
	$Type_Result = mysql_query("SELECT B.Type,C.TypeName,C.TypeColor FROM $DataIn.ck8_bfsheet B
							   LEFT JOIN   $DataPublic.ck8_bftype  C ON C.id=B.Type 
							   WHERE 1 GROUP BY b.Type ORDER BY B.Type DESC",$link_id);
	if ($TypeRow = mysql_fetch_array($Type_Result)) {
		$GysList.="<select name='chooseType' id='chooseType' onchange='ResetPage(1,5)'>";
		$GysList.= "<option value='' selected>全部</option>";
		do{
			$TypeValue=$TypeRow["Type"];
           $TypeName=$TypeRow["TypeName"];
			$TypeColor=$TypeRow["TypeColor"];
			if($chooseType==$TypeValue){
				$GysList.=  "<option value='$TypeValue' style= 'color: $Color;font-weight: bold' selected>$TypeName</option>";
				$SearchRows.="AND F.Type='$TypeValue'";
				}
			else{
				$GysList.= "<option value='$TypeValue' style= 'color: $Color;font-weight: bold'>$TypeName</option>";
				}
			}while($TypeRow = mysql_fetch_array($Type_Result));
		$GysList.="</select>&nbsp;";
		}
//有权限
$addBtnDisabled=$SubAction==31?"":"disabled";
	$GysList1="<span class='ButtonH_25' id='addBtn' onclick=\"openWinDialog(this,'$addWebPage',450,400,'bottom')\" $addBtnDisabled>新 增</span>";


//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='5' height='40px' class=''>$GysList </td><td colspan='4'  class=''>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px'><div align='center' style='background-color: #F0F5F8'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="
SELECT F.Id,F.StuffId,F.Qty,F.Remark,F.Type,F.Date,F.Estate,F.Locks,F.Operator,D.StuffCname,K.tStockQty,K.oStockQty,D.Price,D.Picture,D.Price*F.Qty AS Amount,U.Name AS UnitName,C.TypeName,C.TypeColor ,F.Bill,F.DealResult,L.Identifier AS LocationName
FROM $DataIn.ck8_bfsheet F
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
LEFT JOIN   $DataPublic.ck8_bftype  C ON C.id=F.Type 
LEFT JOIN $DataIn.ck_location L ON L.Id = F.LocationId
WHERE 1 $SearchRows ORDER BY F.Id DESC";
$myResult = mysql_query($mySql,$link_id);
$SumQty=0;
$SumAmount=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$SumQty+=$Qty;
		$SumAmount+=$Amount;
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$LocationName=$myRow["LocationName"]==""?"&nbsp;":$myRow["LocationName"];
       $DealResult=$myRow["DealResult"]==""?"&nbsp":$myRow["DealResult"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==0?"<div class='greenB'>已核</div>":"<div class='redB'>未核</div>";
		$Operator=$myRow["Operator"];
		include "../admin/subprogram/staffname.php";
		//检查是否有图片
		$Picture=$myRow["Picture"];
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    include "../model/subprogram/stuffimg_model.php";
		$Locks=$myRow["Locks"];
		$Type=$myRow["Type"];
		$TypeName=$myRow["TypeName"];
		$TypeColor =$myRow["TypeColor"];
		$TypeName="<span style=\"color:$TypeColor \">$TypeName</span>";
		       		//检查权限
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
        if($SubAction==31){//有权限
		   if ($myRow["Estate"]>0 ){//未审核
	          $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
		      $UpdateClick=" onclick=\"openWinDialog(this,'$updateWebPage?Id=$Id',425,300,'left')\" ";
		     }else{
		         $UpdateClick = "";
			     $UpdateIMG = "<span class='blueB'>已审核</span>";
		     }
		 }
	     else{//无权限
		     if($SubAction==1){
			    $UpdateClick="";
			    $UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
		   }
	    }

       $Bill=$myRow["Bill"];
		$Dir=anmaIn("download/ckbf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="B".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}

			echo"<tr><td class='A0111' align='center' height='25' >$i</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='right'>$tStockQty</td>";
			echo"<td class='A0101' align='right'>$oStockQty</td>";	//采购总数
			echo"<td class='A0101' align='right'>$Qty</td>";	//未收货数量
			//echo"<td class='A0101' align='right'>$Price</td>";

			echo"<td class='A0101' align='center'>$LocationName</td>";
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td class='A0101' align='center'>$Bill</td>";
			echo"<td class='A0101'>$Remark</td>";
			echo"<td class='A0101'>$DealResult</td>";
			echo"<td class='A0101' align='center'>$TypeName</td>";
			echo"<td class='A0101' align='center'>$Estate</td>";
			echo"<td class='A0101' align='center'>$Operator</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			$i++;
	}while ($myRow = mysql_fetch_array($myResult));
    echo"</table>";
  }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='11' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
	?>
</form>
</body>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script language = "JavaScript">
function CnameChanged(){
	StuffCname=document.getElementById("StuffCname").value;
	if (StuffCname.length>=2){
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  document.getElementById("stuffQuery").disabled=true;
	}
}

function CheckForm(){

  var Qty=parseFloat(document.getElementById("Qty").value);

  var oStockQty=parseFloat(document.getElementById("oStockQty").value);
  if (Qty>oStockQty){
		alert ("报废数量不能大于现可用库存数！");return false;
     }
  else{
	  return true;
  }
}

function CheckUpdata(){
  var Qty=parseFloat(document.getElementById("Qty").value);
  var oStockQty=parseFloat(document.getElementById("oStockQty").value);
  var oldQty=parseFloat(document.getElementById("oldQty").value);
  var Operators=parseFloat(document.getElementById("Operators").value);
  Qty=oldQty + Qty*Operators;
  if (Qty<0){
	  alert ("错误！减少报废数量>原来的报废数量！");
	  return false;
  }
  if (Qty>oStockQty){
	 alert ("报废数量不能大于现可用库存数！");
	 return false;
   }
	 return true;
}

function viewStuffdata1111() {
	var diag = new Dialog("live");
	var StuffCname=document.getElementById("StuffCname").value;
	if (StuffCname=="") return false;
	diag.Width = 950;
	diag.Height = 600;
	diag.Title = "配件资料";
	diag.URL = "viewStuffdata.php?Action=8&selModel=1&searchSTR="+StuffCname;
	diag.ShowMessageRow = false;
	diag.MessageTitle ="";
	diag.Message = "";
	diag.ShowButtonRow = true;
	diag.selModel=1; //1只选一条；0多选；
	diag.OKEvent=function(){
		var backData=diag.backValue();
		if (backData){
			var dtemp=backData.split("^^");
			if (dtemp.length==3){
			  document.getElementById("StuffCname").value=dtemp[1];
			  document.getElementById("StuffId").value=dtemp[0];
			  document.getElementById("oStockQty").value=dtemp[2];
			  document.getElementById("listmaxQty").innerHTML="现可用库存数量："+dtemp[2];
			  document.getElementById("StuffCname").readOnly=true;
			  document.getElementById("listmaxQty").style.display="block";
			}
		    diag.close();
		   }
		};
	diag.show();
}
</script>