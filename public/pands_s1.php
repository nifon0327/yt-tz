<?php 
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

$Th_Col="选项|30|序号|30|客户|50|产品ID|50|中文名|230|Product Code|250|描述|30|参考<br>售价|60|装箱<br>单位|30|外箱<br>条码|30|所属分类|90|操作员|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
$tableMenuS=600;
$ColsNumber=17;
if($r!="" && isset($_SESSION['pandsS'])){
	unset($_SESSION['pandsS']);
	$pandsS="";
	} 
//查询条件的参数
if($Tid!=""){
	$TidSTR="and P.TypeId=$Tid";
	}
if($Action==7){
	$ProductIdSTR="and P.ProductId NOT IN (SELECT ProductId FROM $DataIn.pands GROUP BY ProductId ORDER BY Id)";
	}
else{
	$ProductIdSTR="and P.ProductId IN (SELECT ProductId FROM $DataIn.pands GROUP BY ProductId ORDER BY Id)";
	}
$Pagination=$Pagination==""?1:$Pagination;
if($Pagination==1){//分页
	$Pagination1="selected";
	$Page_Size = 100;	//每页记录数量
	// 获取当前页数
	if($Page){$Page =$Page;}else{$Page = 1;}
	$Result =mysql_query("SELECT P.Id
		FROM $DataIn.productdata P
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
		LEFT JOIN $DataIn.TypeId T ON T.TypeId=P.TypeId
		where 1 $TidSTR $pandsS $ProductIdSTR order by Id DESC",$link_id);
	$RecordToTal= mysql_num_rows($Result);
	if( $RecordToTal ){
		if( $RecordToTal < $Page_Size ){
			$Page_count = 1; 
			}              
	   	if($RecordToTal % $Page_Size ){                                  
			$Page_count = (int)($RecordToTal / $Page_Size) + 1; 
	  		}
	   	else{
			$Page_count = $RecordToTal / $Page_Size; 
	   		}
		}
	else{
		$Page_count = 0;
		}
	//分页字串
	$PageSTR=($Page-1)*$Page_Size.",".$Page_Size;
	$PageSTR="LIMIT ".$PageSTR;
	}
else{					//不分页
	$Pagination0="selected";
	$Page=1;
	$PageSTR="";
	}
?>
<html>
<head>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<?php 
include "../model/characterset.php";
echo"<link rel='stylesheet' href='../model/css/read_line.css'>";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
?>
<title></title>
</head>
<script language="javascript"> 
window.name="win_test" 
</script> 
<BASE target=_self> 
<title>产品查询</title>
</head>
<script  type=text/javascript>
function SelectData(){
	document.form1.action="pands_s2.php";
	document.form1.submit();
	}
//返回选定的采购流水号
function ReBack(Action){
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
					returnq=returnq+"|"+e.value;j++;
					}					
				} 
			}
		}
	if(Action==7 && j>2){
		alert("只能选一个产品！");
		return false;
		}
	else{
		returnValue=returnq;
		this.close();
		}
	}
</script>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" method="post" action="" target="win_test">
<input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
<input name="Tid" type="hidden" id="Tid" value="<?php  echo $Tid?>">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td <?php  echo $td_bgcolor?> class="A0100" id="menuT1" width="<?php  echo $tableMenuS?>">&nbsp;
</td>
    <td width="150" id="menuT2" align="center" class="A1100" <?php  echo $Fun_bgcolor?>>
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
			   <?php 	
				echo"<nobr>";
				echo"<span onClick='SelectData()' $onClickCSS>查询</span>&nbsp;&nbsp;";
				echo"<span onClick='ReBack($Action)' $onClickCSS>确定</span>&nbsp;";
				echo"</nobr>";
			   ?>
				</td>
			</tr>
	 </table>
   </td>
  </tr>
</table>
 <?php 
$result = mysql_query("SELECT 
	P.Id,P.ProductId,P.cName,P.eCode,P.Remark,P.Description,P.Price,P.TestStandard,P.Code,productdata.Estate,productdata.PackingUnit,
	productdata.Unit,productdata.Date,productdata.Locks,productdata.Operator,
	C.CompanyId,C.Forshort,T.TypeName
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataIn.TypeId T ON T.TypeId=P.TypeId
	where 1 $TidSTR $pandsS $ProductIdSTR order by Id DESC $PageSTR",$link_id);
List_Title($Th_Col,"1",1);
$j=($Page-1)*$Page_Size+1;	//起始序号
$i=1;						//本页记录数
if ($myrow = mysql_fetch_array($result)) {
	do {
		$m=1;
		$Id=$myrow["Id"];
		$ProductId=$myrow["ProductId"];
		$cName=$myrow["cName"];
		$eCode=$myrow["eCode"]==""?"&nbsp;":$myrow["eCode"];
		$Price=$myrow["Price"];
		switch($Action){
		case "7"://新增BOM
			$Bdata=$ProductId."-".$cName;
			break;
		case "1"://选择产品以便进行操作
			$Bdata=$ProductId;
			break;
		case"2"://下订单
			$Bdata=$ProductId."^^".$cName."^^".$eCode."^^".$Price;	
			break;
			}		
		$Remark=trim($myrow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myrow[Remark]' width='18' height='18'>";
		$Description=$myrow["Description"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myrow[Description]' width='18' height='18'>";
		$cName=$myrow["TestStandard"]==""?"$cName":"<span onClick='View(\"teststandard\",\"$myrow[TestStandard].jpg\")' style='CURSOR: pointer;color:#FF6633'>$cName</span>";
		$Code=$myrow["Code"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myrow[Code]' width='18' height='18'>";
		$Estate=$myrow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$PackingUnit=$myrow["PackingUnit"];
		$punitResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit Limit 1",$link_id);
		if($punitRow = mysql_fetch_array($punitResult)){
			$PackingUnit=$punitRow["Name"];
			}
			
		$Unit=$myrow["Unit"];
		$Date=$myrow["Date"];
		$Locks=$myrow["Locks"];
		//操作员姓名
		$Operator=$myrow["Operator"];
		$P_Result = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Operator Limit 1",$link_id);
		if($P_Row = mysql_fetch_array($P_Result)){
			$Operator=$P_Row["Name"];
			}
		$thisCId=$myrow["CompanyId"];
		$Client=$myrow["Forshort"];
		$TypeName=$myrow["TypeName"];
		
		$Choose="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Bdata' disabled>";
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		if($Locks==1){
			echo"<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			}
		else{
			if($Keys & mLOCK){
				echo"<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				}
			else{
				echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				}
			}											

		echo"<td class='A0111' width='$Field[$m]' align='center'>$Choose</td>";
		$m=$m+2;
		echo"<td  class='A0101' width='$Field[$m]' align='center'>$j</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Client</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$ProductId</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' onmousedown='window.event.cancelBubble=true;'>$cName</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]'>$eCode</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Description</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='right'>$Price</td>";		
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$PackingUnit</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Code</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]'>$TypeName</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='center'>$Operator</td>";
	  	echo"</tr>";
		echo"</tr></table>";
		$i++;
		$j++;
		}while ($myrow = mysql_fetch_array($result));
	}
	else{
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
		<tr><td class='A0111' height='60' align='center'>没有符合条件的记录</td></tr></table>";
		}
echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
List_Title($Th_Col,"0",1);
Page_Bottom($RecordToTal,$i-1,$Page,$Page_count,$timer,$TypeSTR,$Login_WebStyle,$tableWidth);
ChangeWtitle("$SubCompany 已设BOM的产品列表");
?>