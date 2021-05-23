<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非bom配件申领记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_GoodsOut";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.GoodsId,A.Qty,A.WorkAdd,A.Remark,A.Locks,B.GoodsName,B.BarCode,B.Unit,C.wStockQty,
C.mStockQty,C.oStockQty,D.TypeName,A.Date ,W.Name AS WorkName,A.GetNumber,G.Name AS GetName
	FROM $DataIn.nonbom8_outsheet A
	LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId	
    LEFT JOIN $DataPublic.staffworkadd  W  ON W.Id=A.WorkAdd
	LEFT JOIN $DataPublic.nonbom2_subtype D ON D.Id=B.TypeId
    LEFT JOIN $DataPublic.staffmain G ON G.Number=A.GetNumber
	WHERE A.Id='$Id' LIMIT 1",$link_id));
$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$Remark=$upData["Remark"];
$TypeName=$upData["TypeName"];
$Attached=$upData["Attached"];
$Date=$upData["Date"];
$Qty=$upData["Qty"];
$Unit=$upData["Unit"];
$Locks=$upData["Locks"];
$wStockQty=$upData["wStockQty"];
$mStockQty=$upData["mStockQty"];
$oStockQty=$upData["oStockQty"];
$WorkName=$upData["WorkName"];
$GetName=$upData["GetName"];
$GetNumber=$upData["GetNumber"];
$PropertyResult=mysql_query("SELECT Id FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId=$GoodsId AND Property=7",$link_id);  
 if($PropertyRow=mysql_fetch_array($PropertyResult)){
      $PropertySign=1;
      $Info="<span class='redB'>属性为固定资产，必须填写相关领用记录</span>";
        }
else  {
         $PropertySign=0;
         $Info="<span class='redB'>属性为非固定资产，直接保存即可</span>";
   }

if($PropertySign==1){
        $CheckFormURL="thisPage";
         $CustomFun="<span onclick='AddGoods(3,\"$GoodsId\")' $onClickCSS>新加</span>&nbsp;";
   }
//步骤4：
$tableWidth=850;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,GoodsId,$GoodsId,oldQty,$Qty,OperatorSign,$OperatorSign,ActionId,130";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%" height="250" border="0" align="center" cellspacing="0" id="NoteTable" >
		<tr>
			<td align="right" valign="middle" scope="col" width="150">非BOM配件名称：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $GoodsName."(".$Info.")";?><input type="hidden" id="SIdList" name="SIdList"><input  id="PropertySign" name="PropertySign" type="hidden" value="<?php echo $PropertySign?>"></td>
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
		  <td  align="right">申领日期：</td>
		  <td class="blueB"><?php echo $Date?></td>
	    </tr>
         <tr >
           <td align="right">使用地点：</td>
           <td class="blueB"><?php echo $WorkName?></td>
         </tr>
		<tr>
		  <td align="right">申领人：</td>
		  <td class="blueB"><?php echo $GetName;?><input  type="hidden" id="GetNumber" name="GetNumber" value="<?php echo $GetNumber?>"></td>
	    </tr>
        <tr>
			<td align="right" valign="middle" scope="col">申领数量：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $Qty;?><input  type="hidden" id="Qty" name="Qty" value="<?php echo $Qty ?>"></td>
		</tr>
        <tr>
          <td align="right" valign="top">申领备注：</td>
          <td class="blueB"><?php echo $Remark;?></td>
        </tr>
	  </table>
</td></tr></table>
<?php
 if($PropertySign==1){
?>
   <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
     		<tr><td width="30"  height="30" class="A0010">&nbsp;</td>	<td colspan="6" ><span class='redB'>固定资产领用明细</span></td>		<td width="30" class="A0001">&nbsp;</td></tr>
   <tr >
		<td width="30"  bgcolor="#FFFFFF" height="25" class="A0010">&nbsp;</td>
		 <td class="A1111" width="50" align="center">操作</td>
		 <td class="A1101" width="30" align="center">序号</td>
          <td class="A1101" width="120" align="center">条码</td>
          <td class="A1101" width="150" align="center">资产编号</td>
          <td class="A1101" width="120" align="center">领用人</td>
          <td class="A1101" width="320" align="center">领用说明</td>
		<td width="30"  bgcolor="#FFFFFF" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="30"  height="250" class="A0010">&nbsp;</td>
		<td colspan="6" align="center" class="A0111" id="ShowInfo">	
                    <div style='width:790;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='ListTable'>
                 </table></div>
		</td>
		<td width="30" class="A0001" >&nbsp;</td>
	</tr>
</table>
<?php 
}
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
          var message="";
           var DataSTR="";
            var Qty=document.getElementById("Qty").value;
           var PropertySign=document.getElementById("PropertySign").value; 
           
      if(PropertySign==1){
             if(Qty!=ListTable.rows.length){
                    alert("领用的数量与登记的固定资产明细不一致!");return false;
                  }
            var RemarkArray=document.getElementsByName("Remark[]");
            var LyManArray=document.getElementsByName("LyMan[]");
            var endSign=0;   
	    	for(i=0;i<ListTable.rows.length;i++){
                    if(LyManArray[i].value==""){  endSign=1;break;}
		    	if(DataSTR==""){
			    	    DataSTR=ListTable.rows[i].cells[2].innerText+"@"+LyManArray[i].value+"@"+RemarkArray[i].value;
			    	}
		    	else{
			          	DataSTR=DataSTR+"|"+ListTable.rows[i].cells[2].innerText+"@"+LyManArray[i].value+"@"+RemarkArray[i].value;
			    	   }
		        	}
              }
            if(endSign==1){
                    alert("请选择完领用人");return false;
                      }
		   document.form1.SIdList.value=DataSTR;
		   document.form1.action="nonbom8_updated.php";
		   document.form1.submit();
	}


function AddGoods(Action,GoodsId){
    var GetNumber=document.getElementById("GetNumber").value;
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
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="48";
				oTD.height="25";

				//第2列:
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="30";

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
				oTD.width="150";
 
           /*    var   LyManStr="<select  name='LyMan[]' id='LyMan' style='width: 100px;' ><option value='' selected>请选择</option>";
			           	<?PHP 
		                  $mySql="SELECT M.Number,M.Name,B.Name AS Branch FROM $DataPublic.staffmain  M 
                      	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
                      WHERE M.Estate=1 ORDER BY  M.BranchId,M.GroupId,M.JobId,M.ComeIn,M.Number ";
	                      $result = mysql_query($mySql,$link_id);
                          if($myrow = mysql_fetch_array($result)){
	   	                  do{
			                    $thisNumber=$myrow["Number"];
				                $thisName=$myrow["Name"];
				                $thisBranch=$myrow["Branch"];
			     	            $echoInfo.= "<option value='$thisNumber'>$thisBranch-$thisName</option>"; 
			                  }while ($myrow = mysql_fetch_array($result));
		                  }
			           	?>
                  LyManStr=LyManStr+"<?PHP echo $echoInfo; ?>"+"</select>";*/


				oTD=oTR.insertCell(4); 
				oTD.innerHTML="<input name='LyMan[]' type='text' id='LyMan"+tmpNumQty+"' size='38' class='noLine' value='"+GetNumber+"' readOnly>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="120";	
				//第5列:领用说明
				oTD=oTR.insertCell(5); 
				oTD.innerHTML="<input name='Remark[]' type='text' id='Remark"+tmpNumQty+"' size='38' class='noLine' value=''>";
				oTD.className ="A0100";
				oTD.width="315";	
               /*  var LyMan=document.getElementsByName("LyMan[]");
                       for(n=0;n<LyMan.length;n++){
                               LyMan[n].value=GetNumber;
                              }*/
				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	}


function deleteRow(rowIndex){
	            ListTable.deleteRow(rowIndex);
	            ShowSequence(ListTable);
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
        var k=i+1;
		TableTemp.rows[i].cells[1].innerText=k;//
		var j=i-1;
		}
	}  

</script>