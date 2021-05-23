<?php 
//$DataIn.电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/functions.php";
include "../basic/Parameter_CSS.inc";
?>
<html>
<head>
<?php 
include "../model/characterset.php";
?>
<META content="MSHTML 6.00.2900.2722" name=GENERATOR>
<link rel="stylesheet" href="../model/style/ReadCss.css">
<script src="../basic/functions.js" type=text/javascript></script>
<title>预付订金列表</title>
</head>
<body >
<form name="form1" method="post" action="productdata_read.php?Page=1">
<table border="0" cellpadding="0" cellspacing="0" width="1060">
  <tr>
   <td width="7" ><img name="maintable_r1_c1" src="../images/maintable_r1_c1.gif" width="7" height="26" /></td>
   <td width="900" background="../images/maintable_r1_c2.gif"></td>
   <td width="35"><img name="maintable_r1_c3" src="../images/maintable_r1_c3.gif" width="35" height="26"/></td>
   <td width="90" align="center" background="../images/maintable_r1_c4.gif" >
		<table border="0" align="center" cellspacing="0">
   			<tr>
				<td class="readlink" >
			   <?php 	
					//权限设定
					echo"<nobr>";
					if($Keys & mADD){						
						echo"<span onClick='Addproduct(\"subscription\",\"subscription_Read\")' style='CURSOR: pointer;color:#FF6633'>新增</span>&nbsp;";}	
					if($Keys & mUPDATE){
						echo"<span onClick='UpdataThisId(\"subscription\",\"\")' style='CURSOR: pointer;color:#FF6633'>更新附档</span>&nbsp;";}
					if($Keys & mLOCK){
						echo"<span onClick='DelIds(\"subscription\",\"\",\"\")' style='CURSOR: pointer;color:#FF6633'>删除</span>&nbsp;";
						echo"<span onClick='SignUdates(\"subscription\",\"Lock\",\"\")' style='CURSOR: pointer;color:#FF6633'>锁定</span>&nbsp;
						<span onClick='SignUdates(\"subscription\",\"unLock\",\"\")' style='CURSOR: pointer;color:#FF6633'>解锁</span>&nbsp;";}
					if(($Keys & mUPDATE)&&($Keys & mLOCK)){
						echo"<span onClick='checkall(this.form)' style='CURSOR: pointer;color:#FF6633''>全选</span>&nbsp;
							<span onClick='ReChoose(this.form)' style='CURSOR: pointer;color:#FF6633''>反选</span>&nbsp;";
							}
					echo "<a href='M_subscription_print.php' target='_blank'>列 印</a>&nbsp;";
						echo"</nobr>";
			   		$Th_Col="|选项|30|Id|序号|30|CompanyId|供应商Id|60|Company|供应商简称|100|Money|订金金额|90|ok|已抵货款|90|Date|订金日期|150|Remark|备注|250|Auditing|审核|60|Operator|操作|80|Payee|汇款单|60|Receipt|签收单|60";
			   ?>
				</td>
			</tr>
	 </table>
   </td>
   <td width="36" ><img name="maintable_r1_c5" src="../images/maintable_r1_c5.gif" width="34" height="26"/></td>
   <td width="100" background="../images/maintable_r1_c6.gif" >&nbsp;</td>
   <td width="7"><img name="maintable_r1_c7" src="../images/maintable_r1_c7.gif" width="7" height="26"/></td>
  </tr>
  <tr>
   <td background="../images/maintable_r2_c1.gif"></td>
   <td colspan="5">
   
<?php 
//记录列表
?>
<table width="100%" border="0" align="center" cellspacing="1" >
	  <?php 
echo"<tr bgcolor=$Row1_bgcolor>";
	$result = mysql_query("SELECT * FROM $DataIn.subscription order by Id DESC",$link_id);

//表格表头排序处理，调用表头通用函数
Table_th($Th_Col,$OrderKey,$OrderImg,"1",$Row1Over_bgcolor,$Row1_bgcolor);
$i=1;
if ($myrow = mysql_fetch_array($result)) {
	do {
		$Id=$myrow["Id"];
		$Payment=$myrow["Payment"];
		$CompanyId=$myrow["CompanyId"];
		$Company=$myrow["Company"];
		$Money=$myrow["Money"];
		$Date=$myrow["Date"];
		$Remark=$myrow["Remark"]==""?"&nbsp":$myrow["Remark"];
		$Auditing=$myrow["Auditing"]==1?"<div align='center' class='redB'>未通过</div>":"<div align='center' class='greenB'>已通过</div>";
		$Payee=$myrow["Payee"]==""?"-":"<span onClick='View(\"cwdocument\",\"$myrow[Payee]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
		$Receipt=$myrow["Receipt"]==""?"-":"<span onClick='View(\"cwdocument\",\"$myrow[Receipt]\")' style='CURSOR: pointer;color:#FF6633'>预览</span>";
		$Operator=$myrow["Operator"];
		$Locks=$myrow["Locks"];
		if($Locks==0){//锁定状态
			if($Keys & mLOCK){
				$Choose="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Id'><img src='../images/lock.png' width='8' height='13'>";
				}
			else{
				$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' alt='此记录已锁定操作!' width='8' height='13'>";}
			}
		else{
			if(($Keys & mUPDATE)||($Keys & mDELETE)|| ($Keys & mLOCK)){//有权限
				$Choose="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Id'><img src='../images/unlock.png' width='8' height='13'>";
				}
			else{//无权限
				$Choose="";
				}
			}
		$Choose="<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$Id'><img src='../images/unlock.png' width='8' height='13'>";

		echo"<tr bgcolor='#FFFFFF' onMouseOver='this.bgColor=\"$MouseOver_bgcolor\"' onMouseOut='this.bgColor=\"#FFFFFF\"'>";
		echo"<td class='tb'>$Choose</td>";
		echo"<td  class='tb'><div align='center'>$i</div></td>
		<td  class='tb'><div align='center'>$CompanyId</div></td>
			<td  class='tb'><div align='center'>$Company</div></td>";
			echo"<td  class='tb'><div align='right'>$Money</div></td>";
			if($Payment==0){
				echo"<td  class='tb'><div align='right'>$Money&nbsp;&nbsp;</div></td>";}
			else{
				echo"<td  class='tb'><div align='right'>0.00&nbsp;&nbsp;</div></td>";
				}
			
			echo"<td  class='tb'><div align='left'>$Date</div></td>";
			echo"<td  class='tb'><div align='left'>$Remark</div></td>";
			echo"<td  class='tb'>$Auditing</td>";
			echo"<td  class='tb'><div align='center'>$Operator</div></td>";
			echo"<td  class='tb'><div align='center'>$Payee</div></td>";
			echo"<td  class='tb'><div align='center'>$Receipt</div></td>";
		 echo"</tr>";
		  $i++;
		}while ($myrow = mysql_fetch_array($result));
		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
	}
  else
  {
	  echo"<tr bgcolor='#FFFFFF'>
			<td colspan='9' scope='col' height='60'><p>暂时还没有资料。</td>
	  </tr>";
	 	}

//$i=Table_List($result,$Th_Col,$MouseOver_bgcolor,"","","");
Table_th($Th_Col,$OrderKey,$OrderImg,"0",$Row1Over_bgcolor,$Row1_bgcolor);
?>
</table>
<?php 
//表尾
$Form="Yes";
Tabletail($i-1,$Page,$Page_count,$Form,$TypeSTR);
WinTitle("预付订金列表");
?>
<script  type=text/javascript>
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function ChangeThis(ID,textID,Itemp){//记录ID/行数/字段
	var ID=ID;
	var Field=Itemp;
	var cName=eval("document.form1.cName"+textID+".value");
	var eCode=eval("document.form1.eCode"+textID+".value");
	var Description=eval("document.form1.Description"+textID+".value");
	var Price=eval("document.form1.Price"+textID+".value");
	var Code=eval("document.form1.Code"+textID+".value");
	var Remark=eval("document.form1.Remark"+textID+".value");
	var oldValue=document.form1.TempValue.value;//改变前的值
	switch(Itemp){
	case "cName":
			if(cName==""){
				alert("产品的名称不能为空");
				eval("document.form1.cName"+textID).value=oldValue;
				eval("document.form1.cName"+textID).select();
				return;
				}
			else{
			//检查是否存在相同记录
			myurl="productdata_updatad.php?Id="+Itemp+"&Text="+cName+"&Field="+ID; 
			retCode=openUrl(myurl);
			if (retCode!=-2){alert("产品的名称已更新！");}else{alert("产品的名称更新失败！");eval("document.form1.cName"+textID).value=oldValue;eval("document.form1.cName"+textID).select();
				return;}
			}
			break;
	case "eCode":
			if(eCode==""){
				alert("Product Code不能为空");
				eval("document.form1.eCode"+textID).value=oldValue;
				eval("document.form1.eCode"+textID).select();
				return;
				}
			else{
			myurl="productdata_updatad.php?Id="+Itemp+"&Text="+eCode+"&Field="+ID; 
			retCode=openUrl(myurl);
			if (retCode!=-2){alert("Product Code已更新！");}else{alert("Product Code更新失败！");eval("document.form1.eCode"+textID).value=oldValue;eval("document.form1.eCode"+textID).select();
				return;}
			}
			break;
	case "Description":
			if(Description==""){
				alert("Description不能为空");
				eval("document.form1.Description"+textID).value=oldValue;
				eval("document.form1.Description"+textID).select();
				return;
				}
			else{
			myurl="productdata_updatad.php?Id="+Itemp+"&Text="+Description+"&Field="+ID; 
			retCode=openUrl(myurl);
			if (retCode!=-2){alert("Description已更新！");}else{alert("Description更新失败！");eval("document.form1.Description"+textID).value=oldValue;eval("document.form1.Description"+textID).select();
				return;}
			}
			break;			
	case "Price":
			if(Price==""){
				alert("产品的售价不能为空");
				eval("document.form1.Price"+textID).value=oldValue;
				eval("document.form1.Price"+textID).select();
				return;
				}
			else{
			//检查价格是否符合格式
			var Result=fucCheckNUM(Price,'Price');
			if(Result==0){
				alert("输入不正确的产品售价:"+Price+",重新输入!");
				eval("document.form1.Price"+textID).value=oldValue;
				eval("document.form1.Price"+textID).select();
				return;
				}
			else{
				myurl="productdata_updatad.php?Id="+Itemp+"&Text="+Price+"&Field="+ID; 
				retCode=openUrl(myurl);
				if (retCode!=-2){alert("产品的价格已更新！");}else{alert("产品的价格更新失败！");eval("document.form1.Price"+textID).value=oldValue;eval("document.form1.Price"+textID).select();
				return;
				}
			}
			}
			break;
	case "Code":
	if(Code!=""){
		//检查CODE的格式
		//先检查整个字串是否条码,如果不是,则查找有没有"|"分隔开,有再检查有没有条码存在
		//先检查字串长度,如果!=13则要分解
		if(Code.length<13){
			alert("条码资料错误!");
			eval("document.form1.Code"+textID).value=oldValue;
			eval("document.form1.Code"+textID).select();
			return;
			}
		if(Code.length==13){
			var Result0=fucCheckNUM(Code,'');
			if(Result0==0){
			//不是条码
			alert("条码资料错误!");
			eval("document.form1.Code"+textID).value=oldValue;
			eval("document.form1.Code"+textID).select();
			return;
			}
		}
		else{//超13个字符串,则分解
		
			var CodeArray=Code.split("|");
			
			if(CodeArray.length!=2){
				alert("条码资料错误1!");
				eval("document.form1.Code"+textID).value=oldValue;
				eval("document.form1.Code"+textID).select();
				return;
				}
			var Code0=CodeArray[0];
			var Code1=CodeArray[1];
			
			if(Code0.length==13){
				var Result0=fucCheckNUM(Code0,'');
				if(Result0==0){//不是条码
					if(Code1.length==13){
						var Result0=fucCheckNUM(Code1,'');
						if(Result0==0){//不是条码							
							var Message="条码资料错误";
							}			
						else{
							var Message="";
							}	
						}
					}			
				else{
					var Message="";
					}	
				}
			else{
				if(Code1.length==13){
					var Result0=fucCheckNUM(Code1,'');
					if(Result0==0){//不是条码							
						var Message="条码资料错误";
						}			
					else{
						var Message="";
						}
					}
				else{
					var Message="条码资料错误";
					}
				}
			}
			//}//if(length(Code)==13)
		if(Message!=""){
			alert(Message);
			eval("document.form1.Code"+textID).value=oldValue;
			eval("document.form1.Code"+textID).select();
			return;
			}
		else{
			myurl="productdata_updatad.php?Id="+Itemp+"&Text="+Code+"&Field="+ID; 
			retCode=openUrl(myurl);
			if (retCode!=-2){alert("外箱标签条码资料已更新！");}else{alert("外箱标签条码资料更新失败！");eval("document.form1.Code"+textID).value=oldValue;eval("document.form1.Code"+textID).select();
				return;}
			}
		}//if(Code!="")	
	else{
		myurl="productdata_updatad.php?Id="+Itemp+"&Text="+Code+"&Field="+ID; 
		retCode=openUrl(myurl);
		if (retCode!=-2){alert("外箱标签条码资料已更新！");}else{alert("外箱标签条码资料更新失败！");eval("document.form1.Code"+textID).value=oldValue;eval("document.form1.Code"+textID).select();
			return;}
		}
		break;
	case "Remark":
			myurl="productdata_updatad.php?Id="+Itemp+"&Text="+Remark+"&Field="+ID; 
			retCode=openUrl(myurl);
			if (retCode!=-2){alert("包装说明已更新！");}else{alert("包装说明更新失败！");eval("document.form1.Remark"+textID).value=oldValue;eval("document.form1.Remark"+textID).select();
				return;}
			break;			
		}
	}
	
</script>   
