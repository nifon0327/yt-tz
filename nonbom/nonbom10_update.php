<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非bom配件报废记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.GoodsId,A.Qty,A.Remark,A.Locks,B.GoodsName,B.BarCode,B.Unit,C.wStockQty,C.mStockQty,C.oStockQty,D.TypeName
	FROM $DataIn.nonbom10_outsheet A
	LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId	
	LEFT JOIN $DataPublic.nonbom2_subtype D ON D.Id=B.TypeId
	WHERE A.Id='$Id' LIMIT 1",$link_id));
$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$Remark=$upData["Remark"];
$TypeName=$upData["TypeName"];
$Attached=$upData["Attached"];
$Qty=$upData["Qty"];
$Unit=$upData["Unit"];
$Locks=$upData["Locks"];
$BarCode=$upData["BarCode"];
$wStockQty=$upData["wStockQty"];
$mStockQty=$upData["mStockQty"];
$oStockQty=$upData["oStockQty"];
/*$BiggestQty=$mStockQty>$oStockQty?$mStockQty:$oStockQty;
$minQty=0;
if($BiggestQty==0){//没有库存时
	$maxQty=$Qty+1;
	}
else{//有库存时
	$maxQty=$Qty+1+$BiggestQty;
	}*/

if($Locks==0){
	$Info="<span class='redB'>记录锁定中.先请主管解锁后更新.</span>";
	$SaveSTR="NO";
	}

            $PropertyResult=mysql_query("SELECT Id FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId=$GoodsId AND Property=7",$link_id);  
             if($PropertyRow=mysql_fetch_array($PropertyResult)){
                   $PropertySign=1;
               }
             else  $PropertySign=0;

if($PropertySign==1){
        $CheckFormURL="thisPage";
         $CustomFun="<span onclick='AddGoods(4,\"$GoodsId\")' $onClickCSS>新加</span>&nbsp;";
   }
//步骤4：
$tableWidth=1050;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,GoodsId,$GoodsId,oldQty,$Qty,PropertySign,$PropertySign";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%" height="250" border="0" align="center" cellspacing="0" >
		<tr>
			<td align="right" valign="middle" scope="col">非BOM配件名称：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $GoodsName;?><input  id="SIdList" name="SIdList" type="hidden"></td>
		</tr>
        <tr>
		  <td align="right">类型：</td>
		  <td class="blueB"><?php echo $TypeName?></td>
	    </tr>
		<tr>
		  <td align="right">编号：</td>
		  <td class="blueB"><?php echo $GoodsId?></td>
	    </tr>
		<tr>
		  <td align="right">单位：</td>
		  <td class="blueB"><?php echo $Unit;?></td>
	    </tr>
        <tr>
			<td align="right" valign="middle" scope="col">在库：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $wStockQty;?></td>
		</tr>
        <tr>
			<td align="right" valign="middle" scope="col">采购库存：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $oStockQty;?></td>
		</tr>
        <tr>
          <td align="right">最低库存：</td>
          <td class="blueB"><?php echo $mStockQty;?></td>
        </tr>
        <tr>
			<td align="right" valign="middle" scope="col">报废数量：</td>
			<td valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;" value="<?php echo $Qty;?>" dataType="Range" min="<?php echo $minQty;?>" max="<?php echo $maxQty;?>" msg="格式不符或超出范围"/></td>
		</tr>
        <tr>
          <td align="right" valign="top">报废备注：</td>
          <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'><?php echo $Remark;?></textarea></td>
        </tr>
	  </table>
</td></tr></table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td width="30" class="A0010">&nbsp;</td><td colspan="7" height="40"><span class='redB'>固定资产配件报废资料明细</span></td> <td width="30" class="A0001">&nbsp;</td></tr>
<tr >
		<td width="30" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="60" align="center">操作</td>
		 <td class="A1101" width="40" align="center">序号</td>
          <td class="A1101" width="120" align="center">条码</td>
          <td class="A1101" width="130" align="center">资产编号</td>
          <td class="A1101" width="280" align="center">上传图片</td>
          <td class="A1101" width="280" align="center">报废原因</td>
          <td class="A1101" width="80" align="center">报废时间</td>
		<td width="30" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="30" class="A0010" height="250">&nbsp;</td>
		<td colspan="7" align="center" class="A0111" id="ShowInfo">	
                    <div style='width:980;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='ListTable'>
                   <?php
                       	$sListResult = mysql_query("SELECT B.Id, B.Picture,B.Estate,B.Date,B.Remark,B.Operator,C.BarCode,C.GoodsNum
                       	FROM  $DataIn.nonbom10_bffixed  B 
                       	LEFT JOIN $DataIn.nonbom7_code  C  ON C.BarCode=B.BarCode
                       	WHERE B.BfId=$Id AND B.GoodsId=$GoodsId",$link_id);
                        $i=1;
                        while($ListRows= mysql_fetch_array($sListResult)){
                                               $BarCode=$ListRows["BarCode"];
                                               $CodeId=$ListRows["Id"];
                                               $GoodsNum=$ListRows["GoodsNum"];
                                               $Picture=$ListRows["Picture"];
                                               $Date=$ListRows["Date"];
                                               $bfRemark=$ListRows["Remark"];
                                               $Operator=$ListRows["Operator"];
                                               include "../model/subprogram/staffname.php";
                                               echo"<tr bgcolor='$theDefaultColor'><td  align='center' height='25' width='53' class='A0101'><a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex,$CodeId)' title='删除当前行'>×</a></td>";
                                               echo"<td  align='center' width='40' class='A0101'>$i</td>";
                                               echo"<td  align='center' width='120' class='A0101'>$BarCode<input type='hidden' name='BarCode[]' id='BarCode' value='$BarCode'></td>";
                                               echo"<td  align='center' width='130' class='A0101'>$GoodsNum</td>";
                                               echo"<td  width='280' class='A0101'><input name='Picture[]' type='file' id='Picture[]' ></td>";
                                          echo"<td   width='280' class='A0101'><input name='bfRemark[]' type='text' id='bfRemark[]' size='35' value='$bfRemark' ></td>";
                                               echo"<td  align='center' width='72' class='A0100'>$Date</td>";
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
          var message="";
           var DataSTR="";
            var Qty=document.getElementById("Qty").value;
            var GoodsId=document.getElementById("GoodsId").value;
            var Remark=document.getElementById("Remark").value;
           var PropertySign=document.getElementById("PropertySign").value; 
           if(GoodsId==""){
                  message="请选择配件!";
                 }
           if(Qty<=0){
                  message="请输入报废数量!";
                 }
           if(Remark==""){
                  message="请输入报废原因!";
                 }
            if(message!=""){
                alert(message);return false;
                }
           
      if(PropertySign==1){
             if(Qty!=ListTable.rows.length){
                    alert("报废的数量与固定资产明细不一致!");return false;
                  }
           //  var ShowTable= document.getElementById("ShowTable");
            var bfRemarkArray=document.getElementsByName("bfRemark[]");
           // var PictureArray=document.getElementsByName("Picture[]");
            var endSign=0;   
	    	for(i=0;i<ListTable.rows.length;i++){
		    /*	if(DataSTR==""){
			    	    DataSTR=ListTable.rows[i].cells[2].innerText+"@"+bfRemarkArray[i].value+"@"+PictureArray[i].value;
			    	}
		    	else{
			          	DataSTR=DataSTR+"|"+ListTable.rows[i].cells[2].innerText+"@"+bfRemarkArray[i].value+"@"+PictureArray[i].value;
			    	   }*/
		        	}
              }
		  // document.form1.SIdList.value=DataSTR;
		   document.form1.action="nonbom10_updated.php";
		   document.form1.submit();
	}


function deleteRow(rowIndex,CodeId){
            if(CodeId>0){
                 var Message="确定删除此条固定资产信息记录？";
                 if(confirm(Message)){
	                          var url="nonbom10_ajaxcode.php?Id="+CodeId+"&ActionId=1";
　                      	 var ajax=InitAjax();
　	                       ajax.open("GET",url,true);
	                           ajax.onreadystatechange =function(){
	　　                 if(ajax.readyState==4 && ajax.status ==200){
                               if(ajax.responseText=="Y"){
	                                     ListTable.deleteRow(rowIndex);
	                                     ShowSequence(NoteTable);
                                    }
		                     	  }
	                     	}
　	                ajax.send(null);
                     }
                 }
          else{
	            ListTable.deleteRow(rowIndex);
	            ShowSequence(ListTable);
             }
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
        var k=i+1;
		TableTemp.rows[i].cells[1].innerText=k;//
		var j=i-1;
		}
	}  

function AddGoods(Action,GoodsId){
   document.getElementById('SafariReturnValue').value="";
	var num=Math.random();  
	BackData=window.showModalDialog("nonbom8_s1.php?r="+num+"&tSearchPage=nonbom8&fSearchPage=nonbom8&SearchNum=2&Action="+Action+"&GoodsId="+GoodsId,"BackData","dialogHeight =450px;dialogWidth=880px;center=yes;scroll=yes");
	
		if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度
		
		if(document.getElementById("TempMaxNumber")){  
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}
			  

		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[2].innerText;				
				if(FieldArray[0]==SIdtemp){//如果流水号存在
					Message="固定资产配件: "+FieldArray[0]+" 已存在!跳过继续！";
					break;
					}
				}			
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				
				//表格行数
				tmpNumQty=oTR.rowIndex;
				tmpNum=oTR.rowIndex+1;
				
				//第1列:序号
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex,0)' title='删除当前行'>×</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="53";
				oTD.height="25";

				//第2列:
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";

				//第2列:
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="120";
				//第3列
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="130";

				oTD=oTR.insertCell(4); 
				oTD.innerHTML="<input name='Picture[]' type='file' id='Picture"+tmpNumQty+"'  class='noLine' value=''>";
				oTD.className ="A0101";
				oTD.width="280";	
				//第5列:领用说明
				oTD=oTR.insertCell(5); 
				oTD.innerHTML="<input name='bfRemark[]' type='text' id='bfRemark"+tmpNumQty+"' size='35' class='noLine' value=''>";
				oTD.className ="A0101";
				oTD.width="280";	
			//第6列:
				oTD=oTR.insertCell(6); 
				oTD.innerHTML="";
				oTD.className ="A0100";
				oTD.align="center";
				oTD.width="";	
				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	}
</script>