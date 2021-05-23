<?php 
//电信---yang 20120801

include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 新增产品资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;			
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="830" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right" width="180px">隶属客户</td>
            <td><select name="CompanyId" id="CompanyId" size="1" style="width: 380px;" dataType="Require"  msg="未选择客户" onchange="document.form1.submit()">
			<option value=''>请选择</option>
  			<?php  
			$result = mysql_query("SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object 
			WHERE 1 AND  Estate=1  AND  ObjectSign IN (1,2) AND MOD(CompanySign,7)=0  ORDER BY Letter",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
				     $Letter=$myrow["Letter"];
	                 if($CompanyId==$myrow["CompanyId"]){
	                  $Forshort=$myrow["Forshort"];
	                  
	                   echo"<option value='$myrow[CompanyId]' selected>$Letter-$myrow[Forshort]</option>";
	                 }
				     else	echo"<option value='$myrow[CompanyId]'>$Letter-$myrow[Forshort]</option>";
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
      		</select>
            </td></tr>
          <tr>
            <td width="123" align="right">成品类别</td>
            <td><select name="TypeId" id="TypeId" style="width: 380px;" dataType="Require"  msg="未选择分类" onchange="getRule(this)">
			<option selected value="">请选择</option>
			<?php 
			$result = mysql_query("SELECT * FROM $DataIn.producttype  where 1  AND Estate=1 order by Letter",$link_id);
			while ($myrow = mysql_fetch_array($result)){
				$Letter=$myrow["Letter"];
				$TypeId=$myrow["TypeId"];
				$TypeName=$myrow["TypeName"];
				echo "<option value='$TypeId'>$Letter-$TypeName</option>";
				} 
			?>
           </select>	<input name="NameRule" id="NameRule" value="" style="width:380px;border:0px;color:red" readonly/>  
		   </td>
          </tr>
		  <tr>
            <td align="right">产品属性</td>
            <td>
              <select name="buySign" id="buySign"  style="width: 380px;" dataType="Require" msg="未选择分类">
               <?php    
				echo"<option value='' selected>请选择</option>";
				$result = mysql_query("SELECT Id,Name FROM $DataIn.product_property WHERE 1 AND  Estate=1 ORDER BY Id",$link_id);
				if($myrow = mysql_fetch_array($result)){
					do{
					   $thisId = $myrow["Id"];
					   $thisName = $myrow["Name"];
						echo"<option value='$thisId'>$thisName</option>";
						} while ($myrow = mysql_fetch_array($result));
					}
				?>
              </select>
			</td>
			</tr>
           <tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col"><input name="cName" type="text" id="cName" style="width: 380px;" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内" onkeyup="showResult(this.value,'cName','productdata','')" onblur="LoseFocus()" autocomplete="off"></td></tr>
			<!-- showResult()cName为productdata表中要查询的字段 -->

		  <tr>
            <td align="right" scope="col">英文代码<br>
              Product Code</td>
            <td scope="col"><input name="eCode" type="text" id="eCode" style="width: 380px;"></td>    
		   </tr>
		   <tr>
		  <td align="right" valign="top" scope="col">英文注释<br>
		  Description</td>
		  <td scope="col"><textarea name="Description" style="width: 380px;" rows="2" id="Description"></textarea>
	      </td>
		  </tr>
          <tr>
            <td align="right">参考售价</td>
            <td><input name="Price" type="text" id="Price" style="width: 380px;" dataType="Currency"  msg="错误的价格"></td>
          </tr>
          <tr>
            <td align="right">成品重(kg)</td>
            <td><input name="Weight" type="text" id="Weight" style="width: 380px;" value="0" dataType="Currency" msg="错误的重量"></td>
          </tr>
           <tr>
            <td align="right">误差值(±g)</td>
            <td><input name="MisWeight" type="text" id="MisWeight" style="width: 380px;" value="0" dataType="Currency" msg="错误的重量"></td>
          </tr>         
		  <tr>
            <td align="right">单品体积(m³)</td>
            <td><input name="MainWeight" type="text" id="MainWeight" style="width: 380px;" value="0" dataType="Currency" msg="错误的重量"></td>
          </tr>
		  <tr>
		    <td align="right">装框数量</td>
		    <td><input name="LoadQty" type="text" id="LoadQty" style="width: 380px;" value="0" datatype="Number" msg="错误的数量" /></td>
	      </tr>
          <tr><td align="right">单 位</td>
            <td><select name="Unit" id="Unit" style="width: 380px;" datatype="Require"  msg="未选择单位">
              <option value="" selected>请选择</option>
        		<?php 
			   $ptResult = mysql_query("SELECT * FROM $DataPublic.productunit WHERE Estate=1 order by Id",$link_id);
				while ($ptRow = mysql_fetch_array($ptResult)){
					$ptId=$ptRow["Id"];
					$ptName=$ptRow["Name"];
					echo"<option value='$ptId'>$ptName</option>";
					} 
				?>
            </select></td>
          </tr>  
		  <tr><td align="right">材 质</td>
            <td><select name="MaterialQ" id="MaterialQ"  style="width: 380px;" >
               <?php    
				echo"<option value='0' selected>请选择</option>";
				$MaterialQResult = mysql_query("SELECT Id,Name FROM $DataIn.productmq WHERE 1 AND  Estate=1 ORDER BY Id",$link_id);
				if($MaterialQRow = mysql_fetch_array($MaterialQResult)){
					do{
					   $thisMaterialQId = $MaterialQRow["Id"];
					   $thisMaterialQName = $MaterialQRow["Name"];
						echo"<option value='$thisMaterialQId'>$thisMaterialQName</option>";
						} while ($MaterialQRow = mysql_fetch_array($MaterialQResult));
					}
				?>
              </select></td>
		  </tr>
          <tr>
            <td align="right">用 途</td>
            <td>
              <select name="UseWay" id="UseWay"  style="width: 380px;" >
               <?php    
				echo"<option value='0' selected>请选择</option>";
				$UseWayResult = mysql_query("SELECT Id,Name FROM $DataIn.productuseway WHERE 1 AND  Estate=1 ORDER BY Id",$link_id);
				if($UseWayRow = mysql_fetch_array($UseWayResult)){
					do{
					   $thisUseWayId = $UseWayRow["Id"];
					   $thisUseWayName = $UseWayRow["Name"];
						echo"<option value='$thisUseWayId'>$thisUseWayName</option>";
						} while ($UseWayRow = mysql_fetch_array($UseWayResult));
					}
				?>
            </select></td></tr>  
		  <tr>
            <td align="right">电子类</td>
            <td><select name="dzSign" id="dzSign" onchange="showTable()" style="width: 380px;" dataType="Require" msg="未选择分类">
			  <option value="" >请选择</option>
			  <option value="1">是</option>
			  <option value="0" selected>否</option></select>
			</td>
			</tr>
 
			
			<tr style="display:none;" id="pictureTable"><td>&nbsp;</td><td >
			<table border="1"  cellspacing="5" id="uploadTable">
			<tr>
			<td align="center"><input type="hidden" value=""><a href="#" onclick="AddRow()" title="新增认证文档">新增</a></td>
			<td align="right">删除</td>
			<td align="center">序号</td>
			<td align="center"><span style="color:red; font-size:14px; font-weight:bold">认证图</span>上传(限pdf图片,可同时上传多个图片)</td>
			</tr>
				
			<tr>
			<td align="right"></td>
			<td align="center"><input name="OldImg[]" type="hidden" id="OldImg[]"><a href="#" onclick='deleteRow(this.parentNode.parentNode.rowIndex)'>×</a></td>
			<td align="center">1</td>
			<td><input name="Picture[]" type="file" id="Picture[]" DataType="Filter" Accept="pdf" Msg="格式不对,请重选" Row="1" Cel="5">图档备注<input name="rzRemark[]" type="text" id="rzRemark[]" size="25"></td>
			</tr>
			</table>
			</td>
			</tr>
	      <tr>
            <td align="right">报关方式</td>
            <td><select name="taxtypeId" id="taxtypeId" style="width: 380px;" datatype="Require"  msg="未选择报关">
              <option value="" selected>请选择</option>
        		<?php 
			   $ptResult = mysql_query("SELECT * FROM $DataPublic.taxtype WHERE Estate=1 order by Id",$link_id);
				while ($ptRow = mysql_fetch_array($ptResult)){
					$ptId=$ptRow["Id"];
					$ptName=$ptRow["Name"];
					echo"<option value='$ptId'>$ptName</option>";
					} 
				?>
            </select></td></tr>  	
          <tr>
            <td align="right">装箱单位</td>
            <td>
              <select name="PackingUnit" id="PackingUnit" style="width: 380px;" dataType="Require"  msg="未选择装箱单位">
                <option value="" selected>请选择</option>
              <?php 
			   $puResult = mysql_query("SELECT * FROM $DataPublic.packingunit WHERE Estate=1 order by Id",$link_id);
				while ($puRow = mysql_fetch_array($puResult)){
					$puId=$puRow["Id"];
					$puName=$puRow["Name"];
					echo"<option value='$puId'>$puName</option>";
					} 
				?>
              </select>
            </td>
          </tr>
           <tr>
            <td align="right">品牌授权书</td>
            <td>
              <select name="ClientProxy" id="ClientProxy" style="width: 380px;" >
                <option value="" selected>请选择</option>
              <?php 
			   $ProxyResult = mysql_query("SELECT * 
			   FROM $DataIn.yw7_clientproxy WHERE Estate=1 AND CompanyId ='$CompanyId' order by Id",$link_id);
				while ($ProxyRow = mysql_fetch_array($ProxyResult)){
					$ProxyId=$ProxyRow["Id"];
					$ProxyCaption=$ProxyRow["Caption"];
					echo"<option value='$ProxyId'>$ProxyCaption</option>";
					} 
				?>
              </select>
            </td>
          </tr>
            
          <tr>
            <td align="right">检验标准图</td>
            <td><input name="TestStandard" type="file" id="TestStandard" style="width: 380px;" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1"></td>
          </tr>
          <tr>
            <td align="right">客户验货</td>
            <td><select name="InspectionSign" id="InspectionSign"  style="width: 120px;" dataType="Require" msg="未选择分类">
			  <option value="" selected>请选择</option>
			  <option value="1">是</option>
			  <option value="0">否</option></select><span> &nbsp; &nbsp;*车间生成完成后，需客户验货合格后至待出</span>
			</td>
			</tr>
          <tr>
            <td align="right">包装说明<br></td>
            <td><input name="Remark" type="text" id="Remark" style="width: 380px;">
            </td>
          </tr>
           <tr>
            <td align="right">外箱标签条码</td>
            <td><input name="Code" type="text" id="Code" style="width: 380px;" title="注:条码的英文注释与条码数字之间用&quot;|&quot;隔开,英文注释中需换行的地方输入&quot;&lt;br&gt;&quot;"></td>
          </tr>
          <tr>
            <td align="right" valign="top">产品备注</td>
            <td><textarea name="pRemark" style="width: 380px;" rows="2" id="pRemark"></textarea></td>
          </tr>


  

          <tr>
            <td align="right" valign="top">报价计算</td>
            <td>
                <table  border="0">
                  <tr>
                	<td><input name="pValue1" type="text" id="pValue1" placeholder="金额" style="width: 40px;" title="成本1数字"  value=""/></td>
                    <td><input name="pText1"  type="text" id="pText1"  placeholder="描述" style="width: 160px;" title="成本1描述" value=""/></td>
                    <td width="" rowspan="6">金额/描述<br /><span style="color:#F00" >不要用单引(')及双引(")</span></td>
                  </tr>
                  <tr>
                	<td><input name="pValue2" type="text" id="pValue2" style="width: 40px;" title="成本2数字" value=""/></td>
                    <td><input name="pText2"  type="text" id="pText2"  style="width: 160px;" title="成本2描述" /></td>
                  </tr>
                  <tr>
                	<td><input name="pValue3" type="text" id="pValue3" style="width: 40px;" title="成本3数字" value=""/></td>
                    <td><input name="pText3"  type="text" id="pText3"  style="width: 160px;" title="成本3描述" /></td>
                  </tr>
                  
                  <tr>
                	<td><input name="pValue4" type="text" id="pValue4" style="width: 40px;" title="成本4数字" value=""/>	</td>
                    <td><input name="pText4"  type="text" id="pText4"  style="width: 160px;" title="成本4描述" /></td>
                  </tr>
                  <tr>
                	<td> <input name="pValue5" type="text" id="pValue5" style="width: 40px;" title="成本5数字" value=""/></td>
                    <td><input name="pText5"  type="text" id="pText5"  style="width: 160px;" title="成本5描述" /></td>
                  </tr>
                  <tr>
                	<td><input name="pValue6" type="text" id="pValue6" style="width: 40px;" title="成本6数字" value=""/></td>
                    <td><input name="pText6"  type="text" id="pText6"  style="width: 160px;" title="成本6描述" /></td>
                  </tr>
                  
                  <!--
                  <tr height="2" style="font-size:1px; line-height:1px; margin:0px; background-color:#666">
                    <td colspan="3">&nbsp;</td>
                  </tr> 
                  
                   <tr>
                    <td><input name="pcsValue" type="text" id="pcsValue" style="width: 40px;" title="无多个组合填1" value="1"/></td>
                    <td><input name="pcsText"  type="text" id="pcsText"  style="width: 80px;" title="Pcs"  value="Pcs"/></td>
                    <td>多少Pcs组合价</td>
                  </tr>
                  -->
                  <tr height="2" style="font-size:1px; line-height:1px; margin:0px;">
                    <td colspan="2" style="background-color:#CCC;">&nbsp;</td>
                    <td colspan="1">&nbsp;</td>
                  </tr> 
                    
                  <tr>
                    <td><input name="prateValue" type="text" id="prateValue" style="width: 40px;" title="无汇率转换填1" value="1"/></td>
                    <td><input name="prateText"  type="text" id="prateText"  style="width: 160px;" title="汇率描述" value="汇率"/></td>
                    <td>汇率</td>
                  </tr>
                   
                   <tr>
                    <td colspan="3"> &nbsp;
                    <input type="button" value="生成报价规则" onclick="createBj()" /> &nbsp;&nbsp;
                    <input type="button" onclick="clearBj()" value="清除" /> </td>
                  </tr>     
                                
                </table>            
            </td>
          </tr>          
                    
          <tr>
            <td align="right" valign="top">报价规则</td>
            <td><textarea name="bjRemark" style="width: 380px;" id="bjRemark" readonly="readonly" ></textarea></td>
          </tr>
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
		alert(Price+"价格不能为空或价格无效!!");
		return false;
	}
	
	var sumPrice=0.00;
	var bjStr="";
	for(var i=1;i<=6;i++){
		var tmpValue=document.getElementById("pValue"+i).value;
		var tmpStr=document.getElementById("pText"+i).value;
		//if(fucCheckNUM(tmpValue,"Price")>0){
		if((fucCheckNUM(tmpValue,"Price")>=0) && trim(tmpStr)!="" ){	
			//if(tmpValue>0){  //大于零的才计算
				tmpValue=(tmpValue*1000)/1000
				sumPrice=(sumPrice*1000+tmpValue*1000)/1000;
				//var tmpStr=document.getElementById("pText"+i).value;
				if(bjStr==""){
					bjStr=tmpValue+"("+tmpStr+")";
				}
				else{
					bjStr=bjStr+"+"+tmpValue+"("+tmpStr+")";
				}
			//}
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
	
	if((sumPrice*1)!=(Price*1)){
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
	//ShowSequence(uploadTable);
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

function getRule(e){
     var TypeId=e.value;
	 var NameRule=document.getElementById("NameRule");
     var url="productdata_rule.php?TypeId="+TypeId+"&do="+Math.random();
	 var ajax=InitAjax();
　	 ajax.open("GET",url,true);
     ajax.onreadystatechange =function(){
     if(ajax.readyState==4){//&& ajax.status ==200
			var BackData=ajax.responseText;
			//alert(ajax.responseText);
			if(BackData!=""){
			      NameRule.value=BackData;
			     }
			else{
			     NameRule.value="";
			    }
		 }	
	   }
	 ajax.send(null)	
}
</script>