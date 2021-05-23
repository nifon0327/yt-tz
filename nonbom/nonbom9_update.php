<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新非bom配件转入资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.GoodsId,A.Qty,A.Remark,A.Locks,B.GoodsName,B.BarCode,B.Unit,C.wStockQty,C.oStockQty,D.TypeName
	FROM $DataIn.nonbom9_insheet A
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
$wStockQty=$upData["wStockQty"];
$oStockQty=$upData["oStockQty"];
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
         $CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
   }
//步骤4：
$tableWidth=920;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,GoodsId,$GoodsId,oldQty,$Qty,PropertySign,$PropertySign";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%"  border="0" align="center" cellspacing="0" >
		<tr>
			<td align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $GoodsName.$Info;?><input name="TempFixed" type="hidden" id="TempFixed"></td>
		</tr>
        <tr>
		  <td align="right">类型</td>
		  <td class="blueB"><?php echo $TypeName?></td>
	    </tr>
		<tr>
		  <td align="right">编号</td>
		  <td class="blueB"><?php echo $GoodsId?></td>
	    </tr>
		<tr>
		  <td align="right">单位</td>
		  <td class="blueB"><?php echo $Unit;?></td>
	    </tr>
		<tr>
		  <td align="right">实物库存</td>
		  <td class="blueB"><?php echo $wStockQty;?></td>
	    </tr>
		<tr>
		  <td align="right">订单库存</td>
		  <td class="blueB"><?php echo $oStockQty;?></td>
	    </tr>
        <tr>
			<td align="right" valign="middle" scope="col">转入数量</td>
			<td valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;" onblur="CheckQty(this)"  value="<?php echo $Qty;?>" ></td>
		</tr>
        <tr>
          <td align="right" valign="top">转入备注</td>
          <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" ><?php echo $Remark;?></textarea></td>
        </tr>
	  </table>
</td></tr></table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td width="30" class="A0010">&nbsp;</td><td colspan="7" height="40"><span class='redB'>固定资产配件入库资料输入</span></td> <td width="30" class="A0001">&nbsp;</td></tr>
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="30" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="60" align="center">操作</td>
		 <td class="A1101" width="40" align="center">序号</td>
          <td class="A1101" width="140" align="center">条码</td>
          <td class="A1101" width="150" align="center">资产编号</td>
          <td class="A1101" width="120" align="center">入库地点</td>
          <td class="A1101" width="280" align="center">上传图片</td>
          <td class="A1101" width="80" align="center">转入时间</td>
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
                       	  WHERE rkId=$Id AND GoodsId=$GoodsId AND TypeSign=2",$link_id);
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
                                               $echoInfo="<select name='CkId[]' id='CkId' style='width: 100px;' ><option value='' 'selected'>请选择</option>'";
		                                         $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 order by  Remark";
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
                                               echo"<tr bgcolor='$theDefaultColor'><td  align='center' height='25' width='58' class='A0101'><a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex,$CodeId)' title='删除当前行'>×</a></td>";
                                               echo"<td  align='center' width='40' class='A0101'>$i</td>";
                                               echo"<td  align='center' width='140' class='A0101'><input  type='text' name='BarCode[]' id='BarCode' size='15' value='$BarCode' readonly></td>";
                                               echo"<td  align='center' width='150' class='A0101'><input  type='text' name='GoodsNum[]' id='GoodsNum' size='18' value='$GoodsNum'></td>";
                                               echo"<td   align='center' width='120' class='A0101'>$echoInfo</td>";
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
function CheckQty(e){
      var oldQty=document.getElementById("oldQty").value;
      var thisQty=e.value;
      var wStockQty=<?php echo $wStockQty?>;
       if(thisQty<oldQty){
              var  tempQty= parseInt(thisQty)-parseInt(oldQty);
               if(tempQty>wStockQty){
                        alert("减少的数量超出实物库存!");
                        e.value=oldQty;
                    }
           }
}

function CheckForm(){
	        var Message="";
           var DataSTR="";
           var ShowValues="";
           var endSign=0;   
            var Qty=document.getElementById("Qty").value;
            var Remark=document.getElementById("Remark").value;
           var PropertySign=document.getElementById("PropertySign").value; 
           if(GoodsId==""){
                  Message="请选择配件!";
                 }
           if(Qty<=0){
                  Message="请输入数量!";
                 }
           if(Remark==""){
                  Message="请输入原因!";
                 }
         if(NoteTable.rows.length!=Qty){
                    Message="输入的数量和固定资产明细不符!";
                    }
            if(Message!=""){
                alert(Message);return false;
                }
           if(NoteTable.rows.length>0){
        		   var BarCodeobj=document.getElementsByName("BarCode[]");        
        		   var GoodsNumobj=document.getElementsByName("GoodsNum[]");        
        		   var CkIdobj=document.getElementsByName("CkId[]");        
        		   //var Pictureobj=document.getElementsByName("Picture[]");     
                    	  for(k=0;k <NoteTable.rows.length;k++){ 
                                if(GoodsNumobj[k].value==""){endSign=1;break;}
                               if(CkIdobj[k].value==""){endSign=2;break;}
                             }
               }
               if(endSign==1){
                      alert("此配件为固定资产，未填写完固定资产编号!");return false;
                  }
               if(endSign==2){
                      alert("请选择入库地点!");return false;
                  }
		    document.form1.action="nonbom9_updated.php";
		    document.form1.submit();

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
		                  $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 order by  Remark";
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