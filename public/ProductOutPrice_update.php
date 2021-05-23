<?php 
//电信---yang 20120801

include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新产品资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.productdata WHERE Id='$Id' ORDER BY Id LIMIt 1",$link_id));
$upCompanyId=$upData["CompanyId"];
$ProductId=$upData["ProductId"];
$cName=$upData["cName"];
$eCode=$upData["eCode"];
$Description=$upData["Description"];
$TypeId=$upData["TypeId"];
$Price=$upData["Price"];
$OutPrice=$upData["OutPrice"];
$Unit=$upData["Unit"];
$TestStandard=$upData["TestStandard"];
//$Img_H=$upData["Img_H"];  
$Remark=$upData["Remark"];
$pRemark=$upData["pRemark"];
$bjRemark=$upData["bjRemark"];
$LoadQty=$upData["LoadQty"];
$PackingUnit=$upData["PackingUnit"];
$Code=$upData["Code"];
$Moq=$upData["Moq"];
$Weight=$upData["Weight"];
$MisWeight=$upData["MisWeight"];
$MainWeight=$upData["MainWeight"];
$SubClientId=$upData["SubClientId"];
$dzSign=$upData["dzSign"];
if($dzSign==1){$selected1="selected"; $selected0="";}
else{$selected1=""; $selected0="selected";}

$buySign=$upData["buySign"];
$buyStr = "buySign".$buySign;
$$buyStr="selected";

$InspectionSign=$upData["InspectionSign"];
$InspectionStr = "Inspection".$InspectionSign;
$$InspectionStr="selected";

//获取itf和lotto码--对应companyId = 100024/1004/1059
if($upCompanyId === '1004' || $upCompanyId === '1059' || $upCompanyId === '100024' || $CompanyId == '2668'){
    $hasProductParameterSql = "Select * From $DataIn.productprintparameter Where productId = '$ProductId' and Estate = 1 Order by Id Limit 1";
    $hasProductParameterResult = mysql_query($hasProductParameterSql);
    $hasProductParameterRow = mysql_fetch_assoc($hasProductParameterResult);
    if($hasProductParameterRow){
        $lotto = $hasProductParameterRow["Lotto"];
        $itf = $hasProductParameterRow["itf"];
    }

    if($lotto != ''){
        $lottoColor = "class='redB'";
    }
    else{
        if($CompanyId == '100024'){
            $lotto = "ART01";
        }
        else if($CompanyId == '2668'){
            $lotto = "LOP01";
        }
        else{
            $lotto = "ASH01";
        }
    }

    if($itf != ''){
         $itfColor = "class='redB'";
    }else{
         $itf = "4";
    }
}


$prateValue=1;
$prateText="汇率";
$prateStrArr=explode("}*",$bjRemark);
if(count($prateStrArr)==2){ // 6.1(汇率) 
	
	$tmpprateStr1=$prateStrArr[1];
	$tmpprateStr2=explode("(",$tmpprateStr1);
	if(count($tmpprateStr2)>=2){
		$prateTextStr=explode(")",$tmpprateStr2[1]);
		$prateText=$prateTextStr[0];
	}
	
	$prateValue=trim($tmpprateStr2[0]); //取得数字
	if(is_numeric($prateValue)==false){
		$prateValue=1;
	}
	
}

//echo "Prate:$prateValue:$prateText <br>";

$x1StrArr=explode("{",$prateStrArr[0]); //去掉左边{
$x1Str=$x1StrArr[0];
if(count($x1StrArr)>=2){  //只取右边
	$x1Str=$x1StrArr[1];
}

$pcsValue=1;
$pcsText="Pcs";
$pcsStrArr=explode("]*",$x1Str); //去掉左边{
if(count($pcsStrArr)==2){ // 6.1(汇率) 
	
	$tmppcsStr1=$pcsStrArr[1];
	$tmppcsStr2=explode("(",$tmppcsStr1);
	if(count($tmppcsStr2)>=2){
		$pcsTextStr=explode(")",$tmppcsStr2[1]);
		$pcsText=$pcsTextStr[0];
	}
	
	$pcsValue=trim($tmppcsStr2[0]); //取得数字
	if(is_numeric($pcsValue)==false){
		$pcsValue=1;
	}
	
}
//echo "Pcs:$pcsValue:$pcsText <br>";

$x1StrArr=explode("[",$pcsStrArr[0]); //去掉左边{
$x1Str=$x1StrArr[0];
if(count($x1StrArr)>=2){  //只取右边
	$x1Str=$x1StrArr[1];
}

//echo  "$x1Str <br> ";

$pValueStrArr=explode(")+",$x1Str);
$pValueLen=count($pValueStrArr);
for($pi=1; $pi<=6; $pi++){
	$tmppV="pValue"."$pi";
	$$tmppV="";
	$tmppT="pText"."$pi";
	$$tmppT="";	
}
for($pi=1; $pi<=$pValueLen; $pi++){
	
	$tmppV="pValue"."$pi";
	$$tmppV=0;
	$tmppT="pText"."$pi";
	$$tmppT="";
	
	$tmppValueStr1=$pValueStrArr[$pi-1];
	$tmppValueStr2=explode("(",$tmppValueStr1);
	if(count($tmppValueStr2)>=2){
		$pTextStr=explode(")",$tmppValueStr2[1]);
		$$tmppT=$pTextStr[0];
	}
	
	$$tmppV=trim($tmppValueStr2[0]); //取得数字
	if(is_numeric($$tmppV)==false){
		$$tmppV=0;
	}
	
	//echo "$pi:".$$tmppV.":".$$tmppT ."<br>";
	
}


//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
       <table width="820" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right">隶属客户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 380px; " disabled="disabled">
  			<?php  
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND Estate=1 AND  ObjectSign IN (1,2)  ORDER BY Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					if($upCompanyId==$myrow["CompanyId"]){
                          $Forshort=$myrow["Forshort"];
						echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
						}
					else{
						echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
						}
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select></td>
        </tr>
		<tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col">
              <input name="cName" type="text" id="cName" style="width: 380px;" value="<?php  echo $cName?>" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内" readonly="readonly">
			  </td>
		</tr>
		<tr>
            <td align="right" scope="col">英文代码<br> Product Code</td>
            <td scope="col">
              <input name="eCode" type="text" id="eCode" style="width: 380px;" value="<?php  echo $eCode?>" readonly="readonly">  
            </td>
		</tr>
		<tr>
			<td align="right" valign="top" scope="col">英文注释<br>Description</td>
			<td scope="col"><textarea name="Description" style="width: 380px;" rows="2" id="Description" readonly="readonly" ><?php  echo $Description?></textarea></td>
		</tr>
        
    <tr>
		<td align="right">参考售价</td>
        <td>
		<input name="Price" type="text" id="Price" style="width: 380px;" value="<?php  echo $Price?>" dataType="Currency" msg="错误的价格" readonly="readonly"> 
		</td>
	</tr>
    
    <tr>
		<td align="right">转发价格</td>
        <td>
		<input name="OutPrice" type="text" id="OutPrice" style="width: 380px;" value="<?php  echo $OutPrice?>" dataType="Currency" msg="错误的价格" > 
		</td>
	</tr>    

<?php
    if($upCompanyId === '1004' || $upCompanyId === '1059' || $upCompanyId === '100024'){
           echo "<tr>
            <td align='right'>lotto码</td>
            <td><input $lottoColor type='text' name='lotto' style='width: 380px;' id='lotto' value='$lotto' readonly>
            </input></td>
          </tr>
           <tr>
            <td align='right'>itf码</td>
            <td><input $itfColor type='text' name='itf' style='width: 380px;' id='itf' value='$itf' readonly>
            </input></td>
          </tr>";
    }
?>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>

function trim(str) {
  return str.replace(/(^\s+)|(\s+$)/g, "");
}


function createBj(){
	var Price=document.getElementById("Price").value;
	if(Price=="" ||  (fucCheckNUM(Price,"Price")==0)){ //必填报价规则
		alert("价格不能为空或价格无效!!");
		return false;
	}
	
	var sumPrice=0.00;
	var bjStr="";
	for(var i=1;i<=6;i++){
		var tmpValue=document.getElementById("pValue"+i).value;
		var tmpStr=document.getElementById("pText"+i).value;
		if((fucCheckNUM(tmpValue,"Price")>=0) && trim(tmpStr)!="" ){
			if(tmpValue>=0){  //大于零的才计算
				tmpValue=(tmpValue*1000)/1000
				sumPrice=(sumPrice*1000+tmpValue*1000)/1000;
				//var tmpStr=document.getElementById("pText"+i).value;
				if(bjStr==""){
					bjStr=tmpValue+"("+tmpStr+")";
				}
				else{
					bjStr=bjStr+"+"+tmpValue+"("+tmpStr+")";
				}
			}
		}
		else{
			if(trim(tmpValue)!=""){
				alert("无效的金额!");
				return false;
			}
		}
	} //end for 
	//alert (sumPrice);
	/*
	var tmpValue=document.getElementById("pcsValue").value;
	if(fucCheckNUM(tmpValue,"Price")>0){
		if(tmpValue>0 && tmpValue!=1){
			var tmpStr=document.getElementById("pcsText").value;
			if(tmpStr==""){
				tmpStr="Pcs";
			}			
			sumPrice=(tmpValue*1000*sumPrice)/1000;
			bjStr="["+bjStr+"]*"+tmpValue+"("+tmpStr+")";
		}
	}
	else{
		alert("无效的Pcs数值!");
		return false;
	}
	*/
	//alert (sumPrice);
	var tmpValue=document.getElementById("prateValue").value;
	if(fucCheckNUM(tmpValue,"Price")>0){
		if(tmpValue>0 && tmpValue!=1){
			var tmpStr=document.getElementById("prateText").value;
			if(tmpStr==""){
				tmpStr="汇率";
			}
			sumPrice=(tmpValue*1000*sumPrice)/1000;
			bjStr="{"+bjStr+"}*"+tmpValue+"("+tmpStr+")";
		}
	}
	else{
		alert("无效的汇率数值!");
		return false;
	}
	
	if(sumPrice.toFixed(3)!=(Price*1)){
		alert("报价规则的价格("+sumPrice+")与实际价格不符("+Price+")!")
		return false;		
	}
	document.getElementById("bjRemark").value=bjStr;
	
}

function clearBj(){
	document.getElementById("bjRemark").value="";
}



//删除指定行
function deleteRow(rowIndex){
	uploadTable.deleteRow(rowIndex);
	ShowSequence(uploadTable);
	}
function deleteImg(Img,rowIndex){
	var message=confirm("确定要删除原图片 "+Img+" 吗?");
	if (message==true){
	var	myurl="productdata_delcer.php?ImgName="+Img;	
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					  if(BackData=="Y"){
					      ReOpen("productdata_update");
					     }
					}
				}
			ajax.send(null); 
		}
	}  
function ShowSequence(TableTemp){
	//原档个数
	var oldNum=document.getElementsByName("OldImg[]").length;
	for(i=1;i<TableTemp.rows.length;i++){ 
		var j=i-1;
		if(j<oldNum){
			var ImgLink=document.getElementsByName("OldImg[]")[j].value;
			TableTemp.rows[i].cells[1].innerHTML="<a href='../download/productcer/"+ImgLink+"' target='_black'><div class='redB'>"+i+"</div></a>";
			}
		else{
			TableTemp.rows[i].cells[1].innerHTML=i;//如果原序号带连接、带CSS的处理是？
			}
		document.getElementsByName("Picture[]")[j].Row=i;
		}
	}   
function AddRow(){
	oTR=uploadTable.insertRow(uploadTable.rows.length);
	tmpNum=oTR.rowIndex;
	//第一列:序号
	oTD=oTR.insertCell(0);
	oTD.innerHTML="";
	oTD.align="center";
	
	//第二列:操作
	oTD=oTR.insertCell(1);
	oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.align="center";
	oTD.height="25";
				
	//第三列:序号
	oTD=oTR.insertCell(2);
	oTD.innerHTML=""+tmpNum+"";
	oTD.align="center";
				
	//四、说明
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' DataType='Filter' Accept='pdf' Msg='格式不对,请重选' Row='1' Cel='5'>图档备注<input name='rzRemark[]' type='text' id='rzRemark[]' size='25' DataType='Require' Msg='请填写图档备注'>";
}

function showTable(){
  var dzSign=document.getElementById("dzSign").value;
  if(dzSign==1)document.getElementById("pictureTable").style.display="";
  else document.getElementById("pictureTable").style.display="none";
}
</script>