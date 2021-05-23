<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的
//步骤2：
ChangeWtitle("$SubCompany 新增配件资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,StuffType,$StuffType";
//步骤3：
$tableWidth=950;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="850" border="0" cellspacing="5" id="NoteTable">
      
        <tr>
        	<td align="right" width="150"  scope="col">配件类型</td>
        	<td><select name="TypeId" id="TypeId" style="width:480px" dataType="Require"  msg="未选择分类" onchange="getRule(this)">
				<option value='' selected>请选择</option>
			  	<?php 
				$result = mysql_query("SELECT A.TypeId,A.TypeName,A.Letter,A.ForcePicSign,A.PicJobid,
				A.PicNumber,A.GicJobid,A.jhDays,M.Name as PstaffName 
                FROM $DataIn.stufftype  A
				LEFT JOIN $DataPublic.staffmain M ON M.Number=A.PicNumber
				WHERE A.Estate='1'  ORDER BY A.Letter",$link_id);
				
				while ($StuffType = mysql_fetch_array($result)){
					$Letter=$StuffType["Letter"];
					$TypeId=$StuffType["TypeId"];
					$TypeName=$StuffType["TypeName"];
					$ForcePicSign[]=$StuffType["ForcePicSign"];
					$PicJobid[]=$StuffType["PicJobid"];
					$PicNumber[]=$StuffType["PicNumber"];
					$PstaffName[]=$StuffType["PstaffName"]==""?"(未定)":$StuffType["PstaffName"];
					$GicJobid[]=$StuffType["GicJobid"];
					$JhDays[]=$StuffType["jhDays"];
					echo"<option value='$TypeId'>$Letter-$TypeName</option>";
					}
				?>
				</select>
		<input name="NameRule" id="NameRule" value="" style="width:480px;border:0px;color:red" readonly/>  
			</td>
        </tr>  
		<tr>
            <td align="right">配件名称</td>
            <td width="660" scope="col"><input name="StuffCname" type="text" id="StuffCname" style='width:580px;' dataType="LimitB" min="3" max="100"  msg="必须在2-100个字节之内" title="必填项,2-100个字节内" onkeyup="showResult(this.value,'StuffCname','stuffdata','2')" onblur="LoseFocus()" autocomplete="off"></td>
		</tr>

        <tr>
            <td align="right">配件规格</td>
            <td><input name="Spec" type="text" id="Spec" size="53">
              (配件为外箱时必填)</td>
        </tr>
      <tr>
            <td align="right">主产品重</td>
            <td><input name="Weight" type="text" id="Weight" size="53" dataType="Currency" value="0.0000" msg="错误的重量">
              (单位:克[g],配件为外箱时必填)</td>
        </tr>
         <tr>
            <td align="right">参考价</td>
            <td><input name="NoTaxPrice" type="text" id="NoTaxPrice"  size="53"   onblur="changePrice(3)" ><input name="taxRate" type="hidden" id="taxRate" value="0.00" ><input name="chooseSign" type="hidden" id="chooseSign" value="0" ><input type="hidden" id='PriceDetermined' name="PriceDetermined" value="0"><input type="checkbox" onclick="checkPriceDetermined(this)"><span class="redB">(价格待定)</span></td>
        </tr>   
            
        <tr>
            <td align="right">含税价</td>
            <td><input name="Price" type="text" id="Price"  style='width:480px;'  dataType="Currency" msg="错误的价格" onblur="changePrice(1)" ></td>
        </tr>
        
  
		<tr>
          <td align="right">单&nbsp;&nbsp;&nbsp;&nbsp;位</td>
          <td><select name="Unit" id="Unit" style="width: 480px;"  dataType="Require"  msg="未选择">
              <?php 
	          $mySql="SELECT Id,Name FROM $DataPublic.stuffunit WHERE Estate=1 order by Name";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $unitId=$myrow["Id"];
			     $unitName=$myrow["Name"];
				   echo "<option value='$unitId'>$unitName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
            </select>
          </td>
        </tr>
        <tr>
            <td align="right">箱/pcs</td>
            <td><input name="BoxPcs" type="text" id="BoxPcs"  style='width:480px;'  value="0"></td>
        </tr>
           <tr>
            <td align="right" height="30">配件属性</td>
            <td>
              <!--<input name="Property[]" type="checkbox" value="0" >默认&nbsp;&nbsp;&nbsp;&nbsp;-->
            <?php 
               $x=0;
			    $checkResult = mysql_query("SELECT * FROM $DataIn.stuffpropertytype  WHERE Estate=1 AND Id<>10 ORDER BY Id",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $PropertyId=$checkRow["Id"];
                    $TypeName=$checkRow["TypeName"];
                    $TypeColor=$checkRow["TypeColor"];
                    if ($x>0 && $x%10==0) echo "<br>"; 
                    echo "<input name='Property[]' type='checkbox' value='$PropertyId' onclick='CheckProperty(this)'/><span style='color:$TypeColor;'>$TypeName</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $x++;
                    }
			?>
          </td>
          </td>
        </tr>
              <tr>
            <td align="right">英文代码</td>
            <td><input name="StuffEname" type="text" id="StuffEname" style='width:480px;'></td>
        </tr>
         <tr>
            <td align="right">开发状态</td>
            <td><select name="DevelopState" size="1" id="DevelopState" style="width:480px"  onchange="developStateChange(this)">
              <option value="0" >否</option>
              <option value="1" >是</option>
            </select>
            </td>
          </tr>     
		        <tr id='Client_TR' style='display:none;'>
		            <td align="right">需求客户</td>
		            <td><select name="ClientCompanyId" id="ClientCompanyId" style="width: 480px;"  dataType="Require"  msg="未选择需求客户" disabled="disabled">
					<option value=''>请选择</option>
					<?php 
			       $result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate=1 AND  ObjectSign IN (1,2)  ORDER BY Id",$link_id);
				   if($myrow = mysql_fetch_array($result)){
					do{
		              if($CompanyId==$myrow["CompanyId"]){
		                  $Forshort=$myrow["Forshort"];
		                   echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
		                 }
					else	echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
						} while ($myrow = mysql_fetch_array($result));
					}
					?>		 
					</select>
					</td>
		        </tr>
		       <!--
		        <tr id='TargetWeek_TR' style='display:none;'>
		            <td align="right">完成日期</td>
		            <td> <input name="TargetWeek" type="text" id="TargetWeek" value="<?php  echo $Targetdate?>" style="width:480px"  align='absmiddle'    onclick='set_weekdate(this)' readonly>
		                    <input name="Targetdate" type="hidden" id="Targetdate" value="<?php  echo $Targetdate?>" >
					</td>
		        </tr>
		        -->
		        <tr id='Remark_TR' style='display:none;'>
		            <td align="right">开发说明</td>
		            <td><textarea name="developRemark" style="width:480px" rows="4" id="developRemark"></textarea></td>
		        </tr>
		        
		        <tr  id='Developfile_TR'  style='display:none;'>
		            <td align="right">开发文档</td>
		            <td><input name="developfile" type="file" id="developfile" size="60" DataType="Filter"  msg="非法的文件格式" accept="pdf,psd,eps,jpg,ai,cdr,rar,zip"></textarea>
		            </td>
		        </tr>
   
           <tr>
            <td align="right">下单需求</td>
            <td><select name="ForcePicSpe" size="1" id="ForcePicSpe" style="width:480px" >
              <option value="-1" >(系统默认)</option>
              <option value="0" >无图需求</option>
              <option value="1" >需要图片</option>
              <option value="2" >需要图档</option>
              <option value="3" >图片/图档</option>

            </select>
            </td>
           <!--
              <option value="3" >图片/图档</option>
              <option value="4" >强行锁定</option>         
           --> 
          </tr>     
 
       <!--
         <tr>
            <td align="right">图片上传</td>
            <td><select name="Pjobid" id="Pjobid" style="width: 480px;"  dataType="Require"  msg="未选择图片上传职位">     
              <option value="" >请选择 </option>
               
			<?php 
	          $mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34,35) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $jobId=$myrow["Id"];
			     $jobName=$myrow["GroupName"];
				 $Number=$myrow["Number"];
				 $staffname=$myrow["staffname"];
				 
				   echo "<option value='$jobId|$Number'>$jobName-$staffname</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>	
              <option value="-1|0" >(系统默认) </option>
            <option value='0|0'>不需传图片</option>	
			</select>
			</td>
        </tr>
                     
        
        <tr>
            <td align="right">图档上传</td>
            <td><select name="Jobid" id="Jobid" style="width: 480px;"  dataType="Require"  msg="未选择图档上传职位">     
              <option value="-1|0" >(系统默认)</option>	 
			<?php 
	           $mySql="SELECT j.Id,j.GroupName,m.Number,m.Name as staffname FROM $DataIn.staffgroup  j
			          LEFT JOIN $DataPublic.staffmain M on J.GroupId=M.GroupId
	                  WHERE  J.Id in(5,27,34) AND M.Estate>0 And M.cSign = '7' order by j.Id,j.GroupName";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $jobId=$myrow["Id"];
			     $jobName=$myrow["GroupName"];
				 $Number=$myrow["Number"];
				 $staffname=$myrow["staffname"];
				 
				   echo "<option value='$jobId|$Number'>$jobName-$staffname</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
		    ?>	
           <option value='0|0'>不需传图档</option>
			</select>
			</td>
        </tr>
      -->
         
        <tr>
            <td align="right">图档审核</td>
            <td><select name="GcheckNumber" id="GcheckNumber" style="width: 480px;"  dataType="Require"  msg="未选择图档审核人">     
              <option value="-1|-1" >(系统默认) </option>	 
			<?php 
	          $mySql="SELECT j.Id,j.Name,m.Number,m.Name as staffname FROM $DataPublic.jobdata  j
			          LEFT JOIN $DataPublic.staffmain M on J.Id=M.JobId
	                  WHERE  J.Id in(3,4,6,7,32) AND M.Estate>0 order by j.Id,j.Name";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $jobId=$myrow["Id"];
			     $jobName=$myrow["Name"];
				 $Number=$myrow["Number"];
				 $staffname=$myrow["staffname"];
				 
				   echo "<option value='$jobId|$Number'>$jobName-$staffname</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>	
           <option value='0|0'>不需图档审核</option>
			</select>
			</td>
        </tr>       
        
        <tr>
            <td align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="65" rows="4" id="Remark"></textarea></td>
        </tr>
        <tr>
            <td height="22" align="right">设定关系：</td>
            <td>&nbsp;</td>
        </tr>
 
        <tr>
            <td align="right">采购</td>
            <td><select name="BuyerId" id="BuyerId" style="width: 480px;" dataType="Require"  msg="未选择采购">
			<option value=''>请选择</option>
			<option value='0'>-----</option>
			<?php 
	        $checkStaff="SELECT M.Number,M.Name as staffname FROM  
			          $DataIn.staffmain M 
	                  WHERE   M.Estate>0 AND M.BranchId IN (". $APP_CONFIG['STUFF_BUYER_BRANCHID'] .")  order by M.Number";
	        			 
			$staffResult = mysql_query($checkStaff); 
			while ( $staffRow = mysql_fetch_array($staffResult)){
				$pNumber=$staffRow["Number"];
				$PName=$staffRow["staffname"];	
				$GroupName=$staffRow["GroupName"];
				if ($BuyerId==$pNumber){
					echo "<option value='$pNumber' selected>$PName</option>";
				}
				else{
					echo "<option value='$pNumber'>$PName</option>";
				}
				
			} 
			?>		 
			</select>
			</td>
        </tr>
  
        <tr>
            <td align="right">供应商</td>
            <td><select name="CompanyId" id="CompanyId" style="width: 480px;" dataType="Require"  msg="未选择供应商" onchange="changePrice(2)">
			<option value=''>请选择</option>
			<option value='0'>-----</option>
            <?php 
			//供应商
			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE  Estate='1' AND  ObjectSign IN (1,3) order by Letter";			
			$checkResult = mysql_query($checkSql); 
			while ( $checkRow = mysql_fetch_array($checkResult)){
				$CompanyId=$checkRow["CompanyId"];
				$Forshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
				echo "<option value='$CompanyId'>$Forshort</option>";
				} 
			?>
            </select>
			</td>
    	</tr>
        <!--
    	<tr>
            <td align="right">交货周期(天)</td>
            <td><input name="jhDays" type="text" id="jhDays" style="width:480px" maxlength="6" ></td><!-- dataType="Number" msg="错误的交货周期">
        </tr>
        -->

        <tr>
            <td align="right">库位编号</td>
            <td><select name="SeatId" id="SeatId" style="width: 480px;" dataType="Require"  msg="未选择库位编号">
                    <option value=''>请选择</option>
                    <option value='0'>-----</option>
                    <?php
                    $checkStaff="SELECT SeatId,WareHouse,ZoneName FROM  $DataIn.wms_seat  order by SeatId DESC ";
                    $staffResult = mysql_query($checkStaff);
                    while ( $staffRow = mysql_fetch_array($staffResult)){
                        $SeatId=$staffRow["SeatId"];
                        $WareHouse=$staffRow["WareHouse"];
                        $ZoneName=$staffRow["ZoneName"];
                        echo "<option value='$SeatId'>$WareHouse - $ZoneName - $SeatId</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>

            <tr>
            <td align="right">送货楼层</td>
            <td><select name="SendFloor" id="SendFloor" style="width: 480px;"  dataType="Require"  msg="未选择默认送货楼层" onchange="getCheckSign(this)">     
             <option value='' selected>请选择</option>
			<?php  
	          $mySql="SELECT Id,Name,Remark,CheckSign FROM $DataIn.base_mposition  
	                  WHERE Estate=1 order by  Remark";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $FloorId=$myrow["Id"];
				 $FloorRemark=$myrow["Remark"];
				 $FloorName=$myrow["Name"];
				 $CheckSign=$myrow["CheckSign"];
				 echo "<option value='$FloorId'>$FloorName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
			</td>
        </tr>
        
        <tr>
            <td align="right">品检方式</td>
            <td><select name="CheckSign" id="CheckSign" style="width: 480px;" dataType="Require"  msg="未选择品检要求">
		<option value=''>请选择</option>
                <option value='99'>-----</option>
                <option value='0'>抽  检</option>
                <option value='1'>全  检</option>
            </select>
			</td>
    	</tr>
          
        <tr>
          <td>&nbsp;</td>
          <td><div class="redB">注：外箱配件规格的写法：长*宽*高CM+其它规格说明,尺寸一定要按格式写在前面,这影响出货装箱设置</div></td>
        </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";

?>
<script src='../model/weekdate.js' type=text/javascript></script>
<script language="javascript">
//var xmlHttp;
var ForcePicSign=<?php echo json_encode($ForcePicSign);?>;
var PicJobid=<?php echo json_encode($PicJobid);?>;
var PicNumber=<?php echo json_encode($PicNumber);?>;
var PstaffName=<?php echo json_encode($PstaffName);?>;
var GicJobid=<?php echo json_encode($GicJobid);?>;
var JhDays=<?php echo json_encode($JhDays);?>;

function getRule(e){
     var TypeId=e.value;
	 var NameRule=document.getElementById("NameRule");
    //var SeatId=document.getElementById("SeatId");
     var url="stuffdata_rule.php?TypeId="+TypeId+"&do="+Math.random();
	 var ajax=InitAjax();
　	 ajax.open("GET",url,true);
     ajax.onreadystatechange =function(){
     if(ajax.readyState==4){//&& ajax.status ==200
			var BackData=ajax.responseText;
			//alert(ajax.responseText);
			if(BackData!=""){
			      var dataArray=BackData.split("|");
			       NameRule.value=dataArray[0];
			       if (dataArray[1] >0){
				        var SendFloor=document.getElementById("SendFloor");
				         var index=getSOptionValueIndex(dataArray[1],SendFloor);
				         SendFloor.selectedIndex=index;
				         getCheckSign(SendFloor);
			       }
			       if (dataArray[1] >3){
				         var BuyerId=document.getElementById("BuyerId");
				         var index=getSOptionValueIndex(dataArray[3],BuyerId);
				         BuyerId.selectedIndex=index;
				         getCheckSign(BuyerId);
			       }
                if (dataArray[1] >4){
                    var SeatId=document.getElementById("SeatId");
                    var index=getSOptionValueIndex(dataArray[4],SeatId);
                    SeatId.selectedIndex=index;
                    getCheckSign(SeatId);
                }
			     }
			else{
			     NameRule.value="";
			    }
		 }	
	   }
	 ajax.send(null)	
	 
      defaultType(TypeId);

	 var  selIndex=e.selectedIndex;  //取得索引 
	 selIndex=selIndex-1; 
	 
	 var  ForcePicSpe=document.getElementById("ForcePicSpe");
	 if(selIndex>=0) {   //采购需要图
		 var  NForcePicSpe=ForcePicSign[selIndex]*1;  //取得数组值
		 var textForcePicSpe=ForcePicSpe.options[NForcePicSpe+1].text; //取得相应的选项内容 
		 ForcePicSpe.options[0].text=textForcePicSpe+"（系统默认）";
	 }
	 else {
		 ForcePicSpe.options[0].text="（系统默认）"; 
	 }
	 
}
function getCheckSign(e){
	var FloorId=e.value;
	if (FloorId>0){
	 var url="stuffdata_checksign.php?FloorId="+FloorId+"&do="+Math.random();
	 var ajax=InitAjax();
　	 ajax.open("GET",url,true);
     ajax.onreadystatechange =function(){
     if(ajax.readyState==4){//&& ajax.status ==200
			var BackData=ajax.responseText;
			if(BackData!=""){
			      var  CheckSign=document.getElementById("CheckSign");
			      var index=getSOptionValueIndex(BackData,CheckSign);
			      CheckSign.selectedIndex=index;
			}
		 }	
	   }
	 ajax.send(null);
	}
}


function  getSOptionValueIndex(Value,e){
	
	for(i=0;i<e.length;i++){
		if(e.options[i].value==Value){
			return i;
		}
	}
	return -1;
}




function  CheckProperty(e){
      var PropertyObj=  document.getElementsByName("Property[]");
      var CheckSign=0;
      for(var k=0;k<PropertyObj.length;k++){
         if(PropertyObj[k].value==2 && PropertyObj[k].checked  ){
                   CheckSign=1;break;
          }
       }
     if(CheckSign==1){
          if(e.value!=2){
	           for(var k=0;k<PropertyObj.length;k++){
	                   if(k!=1 && k!=2 && e.value!=2 && k!=7){
	                            if(PropertyObj[k].checked) PropertyObj[1].checked=false;
	                          }
	                      }
	               }
	         else{
	           for(var k=0;k<PropertyObj.length;k++){
	                   if(k!=1 && k!=2 && k!=7){
	                            if(PropertyObj[k].checked) PropertyObj[k].checked=false;
	                          }
	                      }
	               }
        } 
}


function developStateChange(e){
      var TRArray=Array("Client_TR","Remark_TR","Developfile_TR");//"TargetWeek_TR",
      for (var i=0;i<TRArray.length;i++)
	   if (e.value=='1'){
			  document.getElementById(TRArray[i]).style.display ="";
			  document.getElementById(TRArray[i]).style.disabled ="";
		  }
	      else{
	          document.getElementById(TRArray[i]).style.display='none';
	          document.getElementById(TRArray[i]).style.disabled ="disabled";
	     }
	     document.getElementById("ClientCompanyId").disabled =e.value=='1'?"":"disabled";
	     
}


function defaultType(TypeId){

  var typeIdArray = Array(9124,9122,8000,9117,9101,9118,7050,7080,9121,7100);
  var PropertyObj=  document.getElementsByName("Property[]");
  if(TypeId==9124){
        for(var k=0;k<PropertyObj.length;k++){
                  if(k==3)PropertyObj[k].checked=true;
                  else {
                          PropertyObj[k].checked=false;  PropertyObj[k].disabled=true;
                        }
              }
            document.getElementById("Price").value=0.00;
            document.getElementById("Price").readOnly=true;
        }
      else{
        for(var k=0;k<PropertyObj.length;k++){
                  PropertyObj[k].checked=false;PropertyObj[k].disabled=false;
              }
            document.getElementById("Price").value="";
             document.getElementById("Price").readOnly=false;
         }
         
       
     var BuyerId= document.getElementById("BuyerId");
     var CompanyId= document.getElementById("CompanyId");
     if(contains(typeIdArray,TypeId)){
	     for ( var i=0; i< CompanyId.options.length; i++){
	          var CompanyIdObj=CompanyId.options[i];
	              if("0"==CompanyIdObj.value){
	                    CompanyIdObj.selected=true; break;
	                  break;
	               }
	           }
        for ( var i=0; i< BuyerId.options.length; i++){
          var BuyerIdObj=BuyerId.options[i];
              if("0"==BuyerIdObj.value){
                    BuyerIdObj.selected=true; break;
               }
           }
      }
else{
        CompanyId.value="";   
      }
}

var weekdate=new WeekDate();
 function set_weekdate(el){
	  var saveFun=function(){
			     if (weekdate.Value>0){
					       var tempWeeks=weekdate.Value.toString();
					       tempWeeks="Week "+tempWeeks.substr(4, 2);
					       el.value=tempWeeks;
						   var tempDate=weekdate.getFriday("-");
						  document.getElementById("Targetdate").value=tempDate;
				   }
		};
	   
	   weekdate.show(el,1,saveFun,"");
}


function changePrice(Action){
	
	if(Action==1){    
	    var Price  = parseFloat(document.getElementById("Price").value);
		var taxRate = parseFloat(document.getElementById("taxRate").value); 
		var newTaxRate = (1 + taxRate/100);
		var NoTaxPrice = Price /newTaxRate;
		
		document.getElementById("NoTaxPrice").value = NoTaxPrice.toFixed(4);
		document.getElementById("chooseSign").value  = 1;
	}else if (Action==3){
		
		var NoTaxPrice  = parseFloat(document.getElementById("NoTaxPrice").value);
		var taxRate = parseFloat(document.getElementById("taxRate").value); 
		var newTaxRate = (1 + taxRate/100);
		var Price = NoTaxPrice *newTaxRate;
		
		document.getElementById("Price").value = Price.toFixed(4);
		document.getElementById("chooseSign").value  = 2;
	}
	else{
		    var Price  = parseFloat(document.getElementById("Price").value);
		    var NoTaxPrice  = parseFloat(document.getElementById("NoTaxPrice").value);
		    var chooseSign = document.getElementById("chooseSign").value ;
		    
		    var CompanyId = document.getElementById("CompanyId").value;
			var url="stuffdata_getTax_ajax.php?CompanyId="+CompanyId;
			var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
			　　if(ajax.readyState==4 && ajax.status ==200){
			　　　	 
			        var taxRate = parseFloat(ajax.responseText);
  
				        document.getElementById("taxRate").value = taxRate;
				        var newTaxRate = (1 + taxRate/100);
				        
				        if(chooseSign ==2){
				       
				           var newPrice = NoTaxPrice * newTaxRate;
			                document.getElementById("Price").value = newPrice.toFixed(4);
					       
				        }else if (chooseSign ==1){
					        
					        var newNoTaxPrice = Price /newTaxRate;
			                document.getElementById("NoTaxPrice").value = newNoTaxPrice.toFixed(4);
				        }      
				  }
			  }
		　	ajax.send(null);	
	  }
}


  function contains(a, obj) {
        for (var i = 0; i < a.length; i++) {
            if (a[i] == obj) {    
                return true;
         }
      }
   }


function  checkPriceDetermined(e){
    var oldPrice = document.getElementById("Price").value;
    if(e.checked){
	    document.getElementById("PriceDetermined").value=1;
	    document.getElementById("Price").value = "0.0000";
    }else{
	    document.getElementById("PriceDetermined").value =0 ;
	    document.getElementById("Price").value = oldPrice;
    }
}
</script>