<?php 
//ewen 2013-03-04 OK
include "../model/modelhead.php";
include "nobom_config.inc";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 新增固定资产记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=1100;$tableMenuS=700;

include "../model/subprogram/add_model_t.php";

//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%" height="250" border="0" align="center" cellspacing="0"  id="NoteTable">
		<tr>
			<td align="right"  height="25">资产名称：</td>
			<td  scope="col"><input name="GoodsName" type="text" id="GoodsName" title="必填项,2-100个字节的范围"  style="width: 380px;" maxlength="30" datatype="LimitB" max="100" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'GoodsName','nonbom4_goodsdata','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off">
			    <input type="button" name="Search" id="Search" value="查询" onclick="checkGoods()"/></td>
		</tr>
		<tr>
		 <td align="right"  height="25">资产ID：</td>
		  <td  scope="col"><input name="GoodsId" type="text" id="GoodsId" style="width: 380px;"  title="必填项,输入正整数" datatype='Require'  msg="没有填写" readonly="readonly"/></td>
	    </tr>
	  <tr>
		  <td align="right"  height="25">类型：</td>
		  <td scope="col"><input name="TypeName" type="text" id="TypeName" style="width: 380px;"  readonly="readonly"/></td>
	    </tr>
	      <tr>
		 <td align="right"  height="25">资产编号：</td>
		  <td  scope="col"><input name="GoodsNum" type="text" id="GoodsNum" style="width: 380px;"  datatype='Require'  msg="没有填写" /></td>
	    </tr>
		<tr>
		  <td align="right"  height="25">使用部门：</td>
		  <td scope="col" >
		  <?php 
             $SelectFrom="";
             include"../model/subselect/BranchId.php";
            ?>
            </td>
	    </tr>
	    
		<tr>
		  <td align="right">使用情况：</td>
		  <td scope="col" >
		           <select name='Estate' id='Estate'  style='width:380px' dataType='Require' msg='未选择'>
		           <option value=''>请选择</option>
		        <?php
		              $EstateStrs = $APP_CONFIG['NOBOM_FIXEDASSET_ESTATE'];
		              while(list($key,$val)= each($EstateStrs))
		              {
			                echo "<option value='$key'>$val</option>";  
		             }
		          ?>
		          </select>
		    </td>
	    </tr>
	   
        <tr>
			<td align="right" valign="middle" scope="col" height="25">入帐日期：</td>
			<td valign="middle" scope="col" ><input name='PostingDate' type='text' id='PostingDate' size='12' maxlength='10'    onFocus='WdatePicker()'  dataType='Require' msg='未填写'/></td>
		</tr>
		<tr>
			<td align="right" valign="middle" scope="col" height="25">数量：</td>
			<td valign="middle" scope="col" ><input name='Qty' type='text' id='Qty' size='12' maxlength='10'  value="1" dataType='Number' msg='数量不正确'/></td>
		</tr>
		<tr>
			<td align="right" valign="middle" scope="col" height="25">单价：</td>
			<td valign="middle" scope="col" ><input name='Amount' type='text' id='Amount' size='12' maxlength='10'   dataType=' Price' msg='金额不正确'/></td>
		</tr>
        <tr>
			<td align="right" valign="middle" scope="col" height="25">增加方式：</td>
			<td valign="middle" scope="col" >
				<select name='AddType' id='AddType'  style='width:380px' dataType='Require' msg='未选择'>
				<option value=''>请选择</option>
		        <?php
		              $AddTypeStrs = $APP_CONFIG['NOBOM_FIXEDASSET_ADDTYPE'];
		              while(list($key,$val)= each($AddTypeStrs))
		              {
			                 echo "<option value='$key'>$val</option>";  
		             }
		          ?>
		          </select>
			</td>
		</tr>
        <tr>
          <td align="right" height="25">折旧方法：</td>
          <td >
	          <select name='DepreciationType' id='DepreciationType'  style='width:380px' dataType='Require' msg='未选择'>
	          <option value=''>请选择</option>
		        <?php
		              $DepreciationTypeStrs = $APP_CONFIG['NOBOM_FIXEDASSET_DEPRECIATIONTYPE'];
		              while(list($key,$val)= each($DepreciationTypeStrs))
		              {
			                echo "<option value='$key'>$val</option>";  
		             }
		          ?>
		          </select>
          </td>
        </tr>
        <tr>
          <td align="right" height="25">折旧期数：</td>
          <td >
	          <select name="DepreciationId" id="DepreciationId" style="width: 380px;" dataType='Require' msg='未填写'>     
             <option value='' selected>请选择</option>
			<?php  
	          $mySql="SELECT Id,Depreciation,ListName FROM $DataPublic.nonbom6_depreciation  WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $thisDepreciationId=$myrow["Id"];
				 $thisDepreciationName=$myrow["ListName"];
				 echo "<option value='$thisDepreciationId'>$thisDepreciationName</option>"; 
				 
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
          </td>
        </tr>
        <tr>
          <td height="22" valign="middle" scope="col" align="right" height="25">残值率</td>
          <td valign="middle" scope="col"><input name="Salvage" type="text" id="Salvage" value="<?php echo $Salvage ?>" size='12' maxlength='10'  datatype="Price" msg="没有填写或格式不对" /></td>
        </tr>
        <tr>
            <td height="25" align="right">发票文件</td>
            <td>
			<input name="InvoiceFile" type="file" id="InvoiceFile" size="40" title="可选项,pdf格式"  DataType="Filter" Accept="pdf" Msg="文件格式(限PDF)不对,请重选" Row="13" Cel="1"> 
			</td><!--FilterB-->
		</tr>
		<tr>
            <td height="25" align="right">采购合同</td>
            <td>
			<input name="ContractFile" type="file" id="ContractFile" size="40" title="可选项,pdf格式"  DataType="Filter" Accept="pdf" Msg="文件格式(限PDF)不对,请重选" Row="14" Cel="1"> 
			</td>
		</tr>
        <tr>
          <td align="right" valign="top" height="25">新增备注：</td>
          <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'><?php echo $Remark;?></textarea></td>
        </tr>
	  </table>
</td></tr></table>

<?php 
	//步骤5：
	include "../model/subprogram/add_model_b.php";
?>

<script>
function clearData(){
	
}
function checkGoods(){
	var TempName=document.getElementById("GoodsName").value;
	var url="nonbom26_ajax.php?checkName="+encodeURIComponent(TempName)+"&sid="+Math.random(); 
	//alert(url);
	var obj = document.getElementsByName("checkBack[]");
	var ajax=InitAjax(); 
	ajax.open("GET",url,true); 
	var k = 0 ;
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4 && ajax.status == 200){
			var BackData=ajax.responseText;
			//alert(BackData);
			if(BackData!=""){
				var DataArray=BackData.split("|");
				if (DataArray.length>1){
						document.getElementById("GoodsId").value=DataArray[0];
						document.getElementById("TypeName").value=DataArray[1];
						document.getElementById("Amount").value=DataArray[2];
						//document.getElementById("DepreciationId").value=DataArray[3];
						document.getElementById("Salvage").value=DataArray[4];
						
						var lens=document.getElementById("DepreciationId").length;
						for(var i=0;i<lens;i++){ 
                             if (document.getElementById("DepreciationId").options[i].value==DataArray[3]){
	                              document.getElementById("DepreciationId").options[i].selected;
	                              break;
                             }
						} 
				   }
				}
			}
		else{//清空
			   document.getElementById("GoodsId").value="";
			   document.getElementById("TypeName").value="";
			   document.getElementById("Amount").value="";
			   document.getElementById("DepreciationId").options[0].selected;
			   document.getElementById("Salvage").value="";
			}
		}
	//发送空 
	ajax.send(null);  
	}
</script>
