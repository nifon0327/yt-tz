<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php

$path = $_SERVER["DOCUMENT_ROOT"];
include_once($path.'/factoryCheck/checkSkip.php');
$Th_Col="序号|30|退换日期|70|退换单号|75|图片说明|55|序号|40|配件Id|60|配件名称|280|退换数量|55|单位|30|原因|100|图片|40|审 核|60|供应商审核|65|操作|60";
//$factoryCheck = "yes";
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
$nowInfo="当前: 物料退换数据";
$funFrom="item5_7";
$addWebPage=$funFrom . "_add.php";
$updateWebPage=$funFrom . "_update.php";
	$SearchRows="";
	$date_Result = mysql_query("SELECT Date FROM $DataIn.ck2_thmain WHERE 1 GROUP BY DATE_FORMAT(Date,'%Y-%m') ORDER BY Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		$GysList.="<select name='thDate' id='thDate'  onchange='ResetPage(1,5)'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$thDate=$thDate==""?$dateValue:$thDate;
			if($thDate==$dateValue){
				$GysList.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				$GysList.="<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		$GysList.="</select>&nbsp;";
		}
	$providerSql = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.ck2_thmain M,$DataIn.trade_object P WHERE M.CompanyId=P.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		$GysList.= "<select name='CompanyId' id='CompanyId'  onchange='ResetPage(1,5)'>";
		$GysList.="<option value='' selected>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			if($CompanyId==$thisCompanyId){
				$GysList.="<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				$GysList.="<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		$GysList.="</select>&nbsp;";
		}
//有权限
$addBtnDisabled=$SubAction==31?"":"disabled";
	$GysList1="<span class='ButtonH_25' id='addBtn' onclick=\"openWinDialog(this,'$addWebPage',1040,560,'center')\" $addBtnDisabled>新 增</>";


//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr >
	<td colspan='5' height='40px' class=''>$GysList </td><td colspan='2' class=''>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT M.BillNumber,M.Date,M.Attached,M.CompanyId,S.Id,S.Mid,S.StuffId,S.Qty,S.Remark,S.Estate,S.Locks,D.StuffCname,D.Picture,U.Name AS UnitName,S.Picture AS thPicture,S.Id AS thisId
FROM $DataIn.ck2_thsheet S
LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
WHERE 1 $SearchRows ORDER BY M.Date DESC,M.Id DESC";
$mainResult = mysql_query($mySql,$link_id);
$ImgDir=anmaIn("../download/thimg/",$SinkOrder,$motherSTR);
if($mainRows = mysql_fetch_array($mainResult)){
	$newMid="";
	do{
		$m=1;
		//主单信息
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		/******************验厂过滤********************/
		$groupLeaderSql = "SELECT GroupLeader From $DataIn.staffgroup WHERE GroupId = 701 ";
		$groupLeaderResult = mysql_query($groupLeaderSql);
		$groupLeaderRow = mysql_fetch_assoc($groupLeaderResult);
		$Leader = $groupLeaderRow['GroupLeader'];
		$skip = false;
		if($FactoryCheck == 'on' and skipData($Leader, $Date, $DataIn, $DataPublic, $link_id)){
			continue;
		}else if($FactoryCheck == 'on'){
			$Date = substr($Date, 0, 10);
		}
		/***************************************/

     $upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='showRkWin($Mid,2)' src='../images/edit.gif' title='更新退换主单资料' width='13' height='13'>";
		$BillNumber=$mainRows["BillNumber"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$BillNumberStr="<a href='../public/ck_th_view.php?f=$MidSTR' target='_blank'>$BillNumber</a>";

		$Attached=$mainRows["Attached"];

		$Dir=anmaIn("download/thimg/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="M".$Mid.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId>0){
			$checkidValue=$mainRows["Id"];
			$StuffCname=$mainRows["StuffCname"];
			$UnitName=$mainRows["UnitName"];
			$Qty=$mainRows["Qty"];
			$Remark=trim($mainRows["Remark"]);
			$Locks=$mainRows["Locks"];
			//检查是否有图片
			$Picture=$mainRows["Picture"];
		    $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	        include "../model/subprogram/stuffimg_model.php";
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);



        $thPicture=$mainRows["thPicture"];
        $thisId=$mainRows["thisId"];
		$Dir=anmaIn("download/thimg/",$SinkOrder,$motherSTR);
		if($thPicture==1){
			$thPicture="T".$thisId.".jpg";
			$thPicture=anmaIn($thPicture,$SinkOrder,$motherSTR);
			$thPicture="<span onClick='OpenOrLoad(\"$Dir\",\"$thPicture\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$thPicture="-";
			}

	        $Estate=$mainRows["Estate"];
	        $BillNumberStr=$Estate==2?$BillNumber:$BillNumberStr;
			//输出主单信息
		if ($newMid!=$Mid){
			   $newMid=$Mid;$j=1;
			   if ($i!=1) {echo"</table></td></tr></table>";}
		       echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center' >$upMian</td>";//编号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";	//日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumberStr</td>";		//单号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Attached</td>";		//说明
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				//echo"<td width='$unitWidth' class='A0101'>";
				echo"<td width='' class='A0101'>";
				$i++;
		       }
			else{
				$m=9;
			}
				//检查权限
		     $UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
		     //审核后不能修改

			 if($SubAction==31 && $Locks==0 && $Estate>0){//有权限
		       $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
			   $UpdateClick="onclick=\"openWinDialog(this,'$updateWebPage?Id=$checkidValue',405,300,'left')\" ";
				}
			else{//无权限
				if($SubAction==1){
					$UpdateClick="";
					if ($Estate>0) $UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
					}
				}

				$EstateColor=$Estate==2?"bgcolor='#FF0000'":"";
				switch($Estate){
				   case 1:$EstateSTR="<div class='redB'>未审</div>";break;
				   case 2:$EstateSTR="<div class='redB'>退回</div>";break;
				   case 0:$EstateSTR="<div class='greenB'>已审</div>";break;
				}

			//供应商审核
			$CompanyId=$mainRows["CompanyId"];

			if ($OldCompanyId!=$CompanyId && $CompanyId!='2270'){
			 	   $checkCpSql=mysql_query("SELECT DISTINCT B.CompanyId 
			 	   FROM $DataIn.UserTable A 
				   LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
				   WHERE A.Estate=1 and A.uType=3 and B.CompanyId='$CompanyId'",$link_id);
				  if($checkCpRows = mysql_fetch_array($checkCpSql)){
				      $AuditSign=1;
				   }
				   else{
					   $AuditSign=0;
				   }
				   $OldCompanyId=$CompanyId;
		  }

		  if ($AuditSign==1){
                    $thEstateSTR="<div class='redB'>未审核</div>";
					$checkThSql=mysql_query("SELECT R.Estate,R.Remark  FROM $DataIn.ck2_threview R WHERE R.Mid='$checkidValue' LIMIT 1",$link_id);
					if($checkThRows = mysql_fetch_array($checkThSql)){
					      $thEstate=$checkThRows["Estate"];
					      $thRemark=$checkThRows["Remark"];
					     if ($thEstate==2){
						    $thEstateSTR="有异议 &nbsp;&nbsp;<img src='../images/remark.gif' title='$thRemark' width='18' height='18'>";

					     }
					     else{
						     $thEstateSTR="<div class='greenB'>已审核</div>";$LockRemark.="供应商已审核通过";$UpdateClick=""; $UpdateIMG="&nbsp;";
					     }
					}
		}
		else{
			  $thEstateSTR="<div>-</div>";$LockRemark.="供应商未开系统";
		}

				if ($LockRemark=="")
			   //输出明细信息
			    $tabbgColor=($j+1)%2==0?"bgcolor='#FFFFFF'":"bgcolor='#EEEEEE'";
			   	echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i$j' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tabbgColor>";
				echo "<tr height='30'>";
				$unitFirst=$Field[$m]-1;
			    echo"<td class='A0001' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center' >$StuffId</td>";	//配件ID
					$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";	//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'  align='right'>$Qty</td>";				//退换数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'  align='right'>$UnitName</td>";		//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Remark</td>";	//原因
	            $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$thPicture</td>";	//图片
	            $m=$m+2;
	            echo"<td class='A0001' width='$Field[$m]' align='center'>$EstateSTR</td>";	//状态
	            $m=$m+2;
	              echo"<td class='A0001' width='$Field[$m]' align='center'>$thEstateSTR</td>";	//状态
	            $m=$m+2;
				echo"<td  class='A0000'  align='center' width=''  $UpdateClick $EstateColor> $UpdateIMG</td>";
				echo "</tr>";
				$j++;
		   }
		}while($mainRows = mysql_fetch_array($mainResult));
	 echo"</table></td></tr></table>";
  }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='10' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
	?>
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script language = "JavaScript">

function CheckForm(){
	var Message=""
	if(ListTable.rows.length<1){
		Message="没有设置退换配件的数据!";
		}
	var qtyInput=document.getElementsByTagName("input");
	for (var i=0;i<qtyInput.length;i++){
		  var e=qtyInput[i];
		  var NameTemp=e.name;
		  var Name=NameTemp.search("thQTY") ;
		  if(Number(e.value)<=0 && Name!=-1){
			Message="退换数量不能为空!";
			break;
			}
	}
	if(Message!=""){
		alert(Message);return false;
		}
	else{
		return true;
		}
}

function CheckUpdata(){
	var Message="";
	var Operators=Number(document.getElementById("Operators").value);
	var changeQty=document.getElementById("changeQty").value;			//新退换数量
	var MantissaQty=Number(document.getElementById("MantissaQty").value);	//未补数量
	var tStockQty=Number(document.getElementById("tStockQty").value);		//库存数量
	var oldQty=Number(document.getElementById("oldQty").value);				//原退换数量
	var CheckSTR=fucCheckNUM(changeQty,"");
	if(CheckSTR==0 || changeQty==0){
		Message="不是规范或不允许的值！";
		}
	else{
		changeQty=Number(changeQty);
		if(Operators>0){//增加数量:在库要大于
			if(changeQty>tStockQty){
				Message="超出在库!";
				}
			}
		else{			//减少数量：
			if(changeQty>MantissaQty || changeQty==oldQty){
				Message="超出未补数量的范围!";
				}
			}
		}

	if(Message!=""){
		alert(Message);
		document.getElementById("changeQty").value="";
		return false;
		}
	else{
		return true;
		}
	}

function viewStuffdata() {
	var diag = new Dialog("live");
	var CompanyId=document.getElementById("TempCompanyId").value;
	diag.Width = 820;
	diag.Height = 600;
	diag.Title = "配件资料";
	diag.URL = "viewStuffdata_th.php?Action=9&selModel=2&Cid="+CompanyId;
	diag.ShowMessageRow = false;
	diag.MessageTitle ="";
	diag.Message = "";
	diag.ShowButtonRow = true;
	diag.selModel=2; //1只选一条；2多选；
	diag.OKEvent=function(){
		var backData=diag.backValue();
		if (backData){
			editTabRecord(backData);
		    diag.close();
		   }
		};
	diag.show();
}

function editTabRecord(BackStuffId){
  		var Rowstemp=BackStuffId.split(",");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldArray=Rowstemp[i].split("^^");//$StuffId."^^".$StuffCname."^^".$tStockQty;
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var StuffIdtemp=ListTable.rows[j].cells[0].data;//隐藏ID号存于操作列
				if(FieldArray[0]==StuffIdtemp){//如果流水号存在
					Message="配件: "+FieldArray[1]+"的资料已在列表!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
				oTD.data=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50px";
				oTD.height="20";

				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40px";

				//三、配件ID
				oTD=oTR.insertCell(2);
				//oTD.innerHTML=""+FieldArray[0]+"";
				oTD.innerHTML="<input type='text' name='thStuffId[]' id='thStuffId' size='6' style='border:0;background:none;' value='"+FieldArray[0]+"' readonly>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="80px";

				//四：配件名称
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="278px";

				//五:退换数量
				oTD=oTR.insertCell(4);
				oTD.innerHTML="<input type='text' name='thQTY[]' id='thQTY' size='6' class='I0000L' value='' onblur='Indepot(this,"+FieldArray[2]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="80px";

				//六:原因
				oTD=oTR.insertCell(5);
				oTD.innerHTML="<input type='text' name='thRemark[]' id='thRemark' class='I0000L' size='20' value=''>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="183px";

				//六:图片
				oTD=oTR.insertCell(6);
				oTD.innerHTML="<input type='file' name='Picture[]' id='Picture' class='I0000L'  >";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="300px";

				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
}

function toTempValue(textValue){
	document.getElementById("TempValue").value=textValue;
	}

function Indepot(thisE,SumQty){
	var oldValue=document.getElementById("TempValue").value;
	var thisValue=thisE.value;
	if(thisValue!=""){
		var CheckSTR=fucCheckNUM(thisValue,"");
		if(CheckSTR==0){
			alert("不是规范的数字！");
			thisE.value=oldValue;
			return false;
			}
		else{
			if((thisValue>SumQty) || thisValue==0){
				alert("不在允许值的范围！在库:"+SumQty);
				thisE.value=oldValue;
				return false;
				}
			}
		}
	}
//删除指定行
function deleteRow(rowIndex){
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j;
		}
	}


function showRkWin(Id,Flag){
	document.getElementById("divShadow").innerHTML="";
	switch(Flag){
		case 2:
		  var url="item5_7_th.php?Mid="+Id;
		  break;
		default:
		  return false;
		  break;
	}
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
	divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth;
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
	}
</script>