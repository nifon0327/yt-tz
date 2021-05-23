<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非bom配件入库记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT A.cgId,A.Qty,A.GoodsId,B.GoodsName,C.Qty AS cgQty,D.wStockQty ,A.Id AS rkId
FROM $DataIn.nonbom7_insheet A 
LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId 
LEFT JOIN $DataIn.nonbom6_cgsheet C ON C.Id=A.cgId 
LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId 
WHERE A.Id='$Id' ORDER BY A.Id DESC",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$GoodsId=$upData["GoodsId"];
	$cgId=$upData["cgId"];
	$rkId=$upData["rkId"];
	$Qty=$upData["Qty"];
	$GoodsName=$upData["GoodsName"];
	$cgQty=$upData["cgQty"];
	$wStockQty=$upData["wStockQty"];
	
	//收货情况				
	$Receive_Temp=mysql_query("SELECT IFNULL(SUM(Qty),0) AS rkQty FROM $DataIn.nonbom7_insheet WHERE cgId='$cgId'",$link_id);; 
	$rkQty=mysql_result($Receive_Temp,0,"rkQty");
	$rkQty=$rkQty==""?0:$rkQty;
	$MantissaQty=$cgQty-$rkQty;       
	if($wStockQty==0){
		$wStockQtyINFO="<span class='redB'>(没有在库,不可做减少入库数量的操作.)</span>";		
		}
	else{
		$OperatorsSTR="<option value='-1'>减少</option>";
		}
	if($MantissaQty==0){
		$MantissaQtyINFO="<span class='redB'>(已全部收货,不可增加入库数量.)</span>";
		}
	else{
		$OperatorsSTR.=" <option value='1'>增加</option>";
		}
	}
$SaveSTR=$OperatorsSTR==""?"NO":"";
$CheckFormURL="thisPage";
//步骤4：
$tableWidth=930;$tableMenuS=600;
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId,TempValue,";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td width="30" rowspan="11" class="A0010">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width="30" rowspan="11" class="A0001">&nbsp;</td></tr>
	<tr>
	  <td width="92" height="30" align="right">流 水 号：</td>
	  <td><?php  echo $cgId?><input name="cgId" type="hidden" id="cgId" value="<?php  echo $cgId?>"><input name="TempFixed" type="hidden" id="TempFixed"></td>
	</tr>
	<tr>
	  <td height="30" align="right">配 件 ID：</td>
  		<td><input name="GoodsId" type="text" id="GoodsId" value="<?php  echo $GoodsId?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">配件名称：</td>
	  <td><?php  echo $GoodsName?></td>
  </tr>
	<tr>
	  <td height="30" align="right">采购数量：</td>
	  <td><?php  echo $cgQty?></td>
  </tr>
	<tr>
	  <td height="30" align="right">未收数量：</td>
	  <td><input name="MantissaQty" type="text" id="MantissaQty" value="<?php  echo $MantissaQty?>" class="I0000L" readonly><?php  echo $MantissaQtyINFO?></td>
  </tr>
	<tr>
	  <td height="30" align="right">在库：</td>
	  <td><input name="wStockQty" type="text" id="wStockQty" value="<?php  echo $wStockQty?>" class="I0000L" readonly><?php  echo $wStockQtyINFO?></td>
  </tr>
	<tr>
	  <td height="30" align="right">本次入库：</td>
	  <td><input name="oldQty" type="text" id="oldQty" value="<?php  echo $Qty?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">入库数量：</td>
	  <td>
	  <?php 
	  if($OperatorsSTR==""){
	  	echo"<div class='redB'>条件不足,不能更新.</div>";
		}
	  else{
	  	echo"<select name='Operators' id='Operators'>$OperatorsSTR</select>&nbsp;<input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='8'>";
      	}
		?>
	  </td>
	</tr>
   <tr><td colspan="2" height="30"><span class='redB'>1.固定资产配件入库资料更新</span></td></tr>
</table>


<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="30" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="60" align="center">操作</td>
		 <td class="A1101" width="40" align="center">序号</td>
          <td class="A1101" width="140" align="center">条码</td>
          <td class="A1101" width="150" align="center">机器编号</td>
          <td class="A1101" width="120" align="center">入库地点</td>
          <td class="A1101" width="280" align="center">上传图片</td>
          <td class="A1101" width="80" align="center">入库时间</td>
		<td width="30" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="30" class="A0010" height="250">&nbsp;</td>
		<td colspan="7" align="center" class="A0111" id="ShowInfo">	
                    <div style='width:870;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='NoteTable'>
                   <?php
                       	$sListResult = mysql_query("SELECT C.Id,C.BarCode,C.Id,C.GoodsNum,K.Name AS rkName,C.Picture,C.Date,C.Operator,C.CkId
                           	FROM $DataIn.nonbom7_code  C 
                          	LEFT JOIN $DataPublic.nonbom0_ck  K  ON K.Id=C.CkId
                       	  WHERE rkId=$rkId AND GoodsId=$GoodsId",$link_id);
                        $i=1;
                        while($ListRows= mysql_fetch_array($sListResult)){
                                               $BarCode=$ListRows["BarCode"];
                                               $CodeId=$ListRows["Id"];
                                               $GoodsNum=$ListRows["GoodsNum"];
                                               $rkName=$ListRows["rkName"];
                                               $Picture=$ListRows["Picture"];
                                               $Date=$ListRows["Date"];
                                               $CkId=$ListRows["CkId"];
                                               $Operator=$ListRows["Operator"];
                                               include "../model/subprogram/staffname.php";
                                               $echoInfo="<select name='CkId[]' id='CkId' style='width: 110px;' ><option value='' 'selected'>请选择</option>'";
		                                         $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1)  order by  Remark";
	                                             $result = mysql_query($mySql,$link_id);
                                                 if($myrow = mysql_fetch_array($result)){
	   	                                         do{
			                                           $FloorId=$myrow["Id"];
				                                       $FloorRemark=$myrow["Remark"];
				                                       $FloorName=$myrow["Name"];
                                                    if($CkId==$FloorId){
                                                           $echoInfo.= "<option value='$FloorId' selected>$FloorName</option>"; 
                                                          }
			     	                                  else  $echoInfo.= "<option value='$FloorId'>$FloorName</option>"; 
			                                         }while ($myrow = mysql_fetch_array($result));
		                                         }
                                               $echoInfo.="</select>";
                                               echo"<tr bgcolor='$theDefaultColor'><td  align='center' height='25' width='58' class='A0101'><a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex,\"$CodeId\")' title='删除当前行'>×</a></td>";
                                               echo"<td  align='center' width='40' class='A0101'>$i</td>";
                                               echo"<td  align='center' width='140' class='A0101'><input  type='text' name='BarCode[]' id='BarCode' size='15' value='$BarCode' readonly></td>";
                                               echo"<td  align='center' width='150' class='A0101'><input  type='text' name='GoodsNum[]' id='GoodsNum' size='18' value='$GoodsNum'></td>";
                                               echo"<td   width='120' class='A0101'>$echoInfo</td>";
                                               echo"<td  width='280' class='A0101'><input name='Picture[]' type='file' id='Picture[]' ></td>";
                                               echo"<td  align='center' width='77' class='A0100'>$Date</td>";
                                               echo"</tr>";
                                       $i++;
                                  }
                     ?>
                 </table></div>
		</td>
		<td width="30" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
	var Message="";
    var NowQty=0;
	var oldValue=document.form1.TempValue.value;						//上次输入的入库数量
	var Operators=Number(document.getElementById("Operators").value);
	var changeQty=document.form1.changeQty.value;								//新入库数量
	var MantissaQty=Number(document.form1.MantissaQty.value);					//未收数量
	var wStockQty=Number(document.form1.wStockQty.value);						//库存数量
	var oldQty=Number(document.form1.oldQty.value);								//原入库数量
	var CheckSTR=fucCheckNUM(changeQty,"Currency");
	if(CheckSTR==0 || changeQty==0){
		    // Message="不是规范或不允许的值！";		
            document.form1.changeQty.value=0;
            changeQty=0;
		}
	else{
		changeQty=Number(changeQty);
		if(Operators>0){//增加数量:不可大于未收数量或0或非法数字
			if(changeQty>MantissaQty){
				Message="超出未收货数量的范围!";
				}			
			}
		else{			//减少数量：不可大于在库数量,或大于等于本次入库的数量
			if(changeQty>wStockQty || changeQty>=oldQty){
				Message="超出在库或本次入库的数量范围!";
				}
			}
		}
       if(Operators>0){
                NowQty=parseInt(oldQty)+parseInt(changeQty);
            } 
        else{
                NowQty=parseInt(oldQty)-parseInt(changeQty);
              }

           NoteTable=document.getElementById("NoteTable");
          // var ShowValues="";
           var endSign=0;
           if(NoteTable.rows.length>0){
                 if(NowQty!=NoteTable.rows.length){
                            alert("此配件属性为固定资产，改变后改变后的数量与固定资产明细数量不符，需新加行或者删除行!");return false;
                  }
        		   var BarCodeobj=document.getElementsByName("BarCode[]");        
        		   var GoodsNumobj=document.getElementsByName("GoodsNum[]");        
        		   var CkIdobj=document.getElementsByName("CkId[]");         
                    	  for(k=0;k <BarCodeobj.length;k++){ 
                                        if(GoodsNumobj[k].value==""){endSign=1;break;}
                                        if(CkIdobj[k].value==""){endSign=2;break;}
                             }
                      if(endSign==1){
                             alert("此配件为固定资产，未填写完固定资产编号!");return false;
                         }
                      if(endSign==2){
                             alert("请选择入库地点!");return false;
                         }
               }


	if(Message!=""){
		alert(Message);
		document.form1.changeQty.value=oldValue;
		return false;
		}
	else{		
            document.form1.TempFixed.value=ShowValues;
		    document.form1.action="nonbom7_updated.php";
		   document.form1.submit();
		  }
	}

function toTempValue(textValue){
       	document.form1.TempValue.value=textValue;
	}

function AddRow(){
     var ShowInnerHTML="";
	oTR=NoteTable.insertRow(NoteTable.rows.length);
	tmpNum=oTR.rowIndex+1;
	//第一列:操作
	oTD=oTR.insertCell(0);
	oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex,0)' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.className ="A0101";
	oTD.align="center";
	oTD.height="30";
	oTD.width="58";
				
	//第二列:序号
	oTD=oTR.insertCell(1);
	oTD.innerHTML=""+tmpNum+"";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.width="40";
				
	//三、条码
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input  type='hidden' name='BarCode[]' id='BarCode' value='0' size='12' >";
	oTD.className ="A0101";
	oTD.width="140";

	//四、机器编号
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input  type='text' name='GoodsNum[]' id='GoodsNum' size='18' value=''>";
	oTD.className ="A0101";
	oTD.width="150";
	oTD.align="center";
    //五、入库地点
              ShowInnerHTML="<select name='CkId[]' id='CkId' style='width: 110px;' ><option value='0' selected>请选择</option>";
			           	<?PHP 
		                  $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1)  order by  Remark";
	                      $result = mysql_query($mySql,$link_id);
                          if($myrow = mysql_fetch_array($result)){
                           $echoInfo="";
	   	                  do{
			                    $FloorId=$myrow["Id"];
				                $FloorRemark=$myrow["Remark"];
				                $FloorName=$myrow["Name"];
			     	            $echoInfo.= "<option value='$FloorId'>$FloorName</option>"; 
			                  }while ($myrow = mysql_fetch_array($result));
		                  }
			           	?>
       ShowInnerHTML+="<?PHP echo $echoInfo; ?>"+"</select>";

	oTD=oTR.insertCell(4);
	oTD.innerHTML=ShowInnerHTML;
	oTD.className ="A0101";
	oTD.width="120";

    //六、上传图片
	oTD=oTR.insertCell(5);
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' >";
	oTD.className ="A0101";
	oTD.width="280";

    //七、
	oTD=oTR.insertCell(6);
	oTD.innerHTML="";
	oTD.className ="A0100";
	oTD.width="77";
	}

function deleteRow(rowIndex,CodeId){
            if(CodeId>0){
                 var Message="确定删除此条固定资产信息记录？";
                 if(confirm(Message)){
	                          var url="nonbom9_ajaxcode.php?Id="+CodeId+"&ActionId=1";
　                      	 var ajax=InitAjax();
　	                       ajax.open("GET",url,true);
	                           ajax.onreadystatechange =function(){
	　　                 if(ajax.readyState==4 && ajax.status ==200){
                               if(ajax.responseText=="Y"){
	                                     NoteTable.deleteRow(rowIndex);
	                                     ShowSequence(NoteTable);
                                    }
		                     	  }
	                     	}
　	                ajax.send(null);
                     }
                 }
          else{
	            NoteTable.deleteRow(rowIndex);
	            ShowSequence(NoteTable);
             }
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
        var k=i+1;
		TableTemp.rows[i].cells[1].innerText=k;//
		var j=i-1;
		}
	}  

function getPath(obj) {  
  if(obj){  
    if (window.navigator.userAgent.indexOf("MSIE")>=1){  
        obj.select();  
      return document.selection.createRange().text;  
      }  
 
    else if(window.navigator.userAgent.indexOf("Firefox")>=1){  
      if(obj.files){  
             return obj.files.item(0).getAsDataURL();  
        }  
      return obj.value;  
      }  
    return obj.value;  
    }  
}  
//参数obj为input file对象
</script>
