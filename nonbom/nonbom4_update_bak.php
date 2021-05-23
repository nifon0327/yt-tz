<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)}
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)}
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#EEEEEE;margin:10px auto;}
.in {
    background:#FFFFFF;
   border:2px solid #555;
   padding:10px 5px;
   position:relative;
   top:-5px;
   left:-5px; 
 border-color: #B03060;
 }  
</style>
<?php 
//EWEN 2013-02-18 OK
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
<script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";

//步骤2：
ChangeWtitle("$SubCompany 更新非bom配件资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.GoodsId,A.GoodsName,A.Price,A.Unit,A.TypeId,A.Attached,A.CkId,A.nxId,A.pdDate,
B.CompanyId,B.mStockQty,C.NameRule,A.Remark,R.Forshort,A.WxNumber,A.WxCompanyId,A.ByNumber,A.ByCompanyId,A.GetSign,C.mainType,A.GetQty,A.AssetType,A.DepreciationId,A.Salvage
FROM $DataPublic.nonbom4_goodsdata A
LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain  R ON R.CompanyId=B.CompanyId
LEFT JOIN $DataPublic.nonbom2_subtype C ON C.Id=A.TypeId
WHERE A.Id='$Id' LIMIT 1",$link_id));

$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$TypeId=$upData["TypeId"];
$Attached=$upData["Attached"];
$Price=$upData["Price"];
$Unit=$upData["Unit"];
$CompanyId=$upData["CompanyId"];
$Forshort=$upData["Forshort"];
$mStockQty=$upData["mStockQty"];
$Remark=$upData["Remark"];
$CkId=$upData["CkId"];
$nxId=$upData["nxId"];
$AssetType=$upData["AssetType"];
if($AssetType==1){
	$showDepreciation = "style='display:none'";
}
$DepreciationId=$upData["DepreciationId"];

$Salvage=$upData["Salvage"];
$pdDate=$upData["pdDate"];
$NameRule=$upData["NameRule"]==""?"未设置":$upData["NameRule"];
$GetSign=$upData["GetSign"];
$GetStr="GetSign".$GetSign;
$$GetStr="checked";
$GetQty=$upData["GetQty"];
$upmainType=$upData["mainType"];
if($upmainType!=8){
$HiddenStr="style='display:none'";
}
else{
    $HiddenStr="";
}

$WxNumber=$upData["WxNumber"];
$WxCompanyId=$upData["WxCompanyId"];
$ByNumber=$upData["ByNumber"];
$ByCompanyId=$upData["ByCompanyId"];
if($WxNumber>0){
          $CheckWxNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$WxNumber",$link_id));
           $WxName=$CheckWxNumberResult["Name"];
     }
if($WxCompanyId>0){
        $CheckWxCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$WxCompanyId",$link_id));
        $WxForshort=$CheckWxCompanyResult["Forshort"];
     }
if($ByNumber>0){
          $CheckByNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$ByNumber",$link_id));
          $ByName=$CheckByNumberResult["Name"];
     }
if($ByCompanyId>0){
        $CheckByCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$ByCompanyId",$link_id));
        $ByForshort=$CheckByCompanyResult["Forshort"];
    }

$BomCompany="";
$CheckMainResult=mysql_fetch_array(mysql_query("SELECT   B.CompanyId,T.Forshort  FROM  $DataPublic.nonbom4_bomcompany  B  
LEFT JOIN $DataIn.trade_object  T ON T.CompanyId=B.CompanyId  WHERE   B.GoodsId=$GoodsId ",$link_id));
$mcCompanyId=$CheckMainResult["CompanyId"];
$mcForshort=$CheckMainResult["Forshort"];
if($mcCompanyId>0)$BomCompany=$mcCompanyId."~".$mcForshort."~7";


$CheckToolsResult=mysql_fetch_array(mysql_query("SELECT  ToolsId  FROM $DataPublic.fixturetool  WHERE   GoodsId=$GoodsId ",$link_id));
$ToolsId = $CheckToolsResult["ToolsId"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,GoodsId,$GoodsId,OldGoodsName,$GoodsName";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" height="95" border="0" align="center" cellspacing="5" id="NoteTable">
              <tr>
           <td width="152" height="22" align="right">配件类别</td>
           <td>
            <?php 
			include "../model/subselect/nonbom_sType.php";
			?>
          </td>
         </tr>
         <tr>
           <td width="152" height="22" align="right">命名规则</td>
           <td>
            <input name="NameRule" id="NameRule" value="<?php echo $NameRule;?>" style="width:380px;border:0px;color:red" readonly/>
          </td>
         </tr>
           <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">配件名称</td>
			<td valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName" title="必填项,2-100个字节的范围"  value="<?php echo $GoodsName;?>" style="width: 380px;" maxlength="100" datatype="LimitB" max="100" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'GoodsName','nonbom4_goodsdata','1','')" onblur="LoseFocus()" autocomplete="off">
			<span class="redB">治工具</span><input name='ToolsId' type='text' id='ToolsId'  onclick="linksTools(this)" size="6" value="<?php echo $ToolsId?>" readonly><a onclick="clearTools()"><span class="redB">清除</span></a>
            </td>
		</tr>
		<tr>
            <td align="right" height="30">配件属性</td>
            <td> 
            <?php 
               $x=0;
               $PropertyResult=mysql_query("SELECT Property FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId='$GoodsId'",$link_id);
				while($PropertyRow=mysql_fetch_array($PropertyResult)){
				      $Property=$PropertyRow["Property"];
				      $ProStr="Property".$Property;
				      $$ProStr="checked";
				 }
			    $checkResult = mysql_query("SELECT * FROM $DataPublic.nonbom4_propertytype  WHERE Estate=1 ORDER BY Id",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $PropertyId=$checkRow["Id"];
                    $TypeName=$checkRow["TypeName"];
                    $TypeColor=$checkRow["TypeColor"];
                    $ProStr="Property". $PropertyId;
                    $CheckedValue=$$ProStr;
                    
                    if ($x>0 && $x%8==0) echo "<br>"; 
                    echo "<input name='Property[]' type='checkbox' value='$PropertyId'  $CheckedValue onclick='Chooseproperty(this)'><span style='color:$TypeColor;'>$TypeName</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $x++;
                    }
			?>
          </td>
        </tr>

        <tr>
           <td align="right">默认供应商</td>
           <td>  <input name="Forshort" type="text" id="Forshort" style="width:380px;"  dataType="Require"   msg="未填写" value="<?php echo $CompanyId."-".$Forshort?>"  onclick="ChooseCompany()" onblur="LoseFocus()" ><input name='CompanyId' type='hidden' id='CompanyId' value="<?php echo $CompanyId?>" ><input  type="checkbox" id="DefaultCompany"  name="DefaultCompany" onclick="SetDefaultCompany(this,1)"> </td>
         </tr>

         </tr>
       <tr id="ShowCompany"><td > &nbsp;</td><td id="CompanyDiv">
      <?php
              $CheckDefaultCompanyResult=mysql_query("SELECT D.CompanyId,D.Price, M.Forshort  FROM $DataPublic.nonbom4_defaultcompany  D 
             LEFT JOIN $DataPublic.nonbom3_retailermain  M  ON D.CompanyId=M.CompanyId WHERE D.GoodsId=$GoodsId",$link_id);
             while($CheckDefaultCompanyRow=mysql_fetch_array($CheckDefaultCompanyResult)){
                   $DefaultCompanyId=$CheckDefaultCompanyRow["CompanyId"];
                   $DefaultPrice=$CheckDefaultCompanyRow["Price"];
                   $DefaultForshort=$CheckDefaultCompanyRow["Forshort"];
                   echo    "<DIV style='height:25px;'>$DefaultForshort &nbsp;&nbsp;<input type='hidden' id='checkdefaultId' name='checkdefaultId[]' size='12' value='$DefaultCompanyId'><input type='text' id='checkdefaultPrice' name='checkdefaultPrice[]' size='12' value='$DefaultPrice'><br></DIV>";
             }
        ?>
    </td></tr>
        <tr>





		<tr  <?php  echo $HiddenStr?> id='GetSignHidden'>
		  <td height="30" align="right" scope="col">款项是否收回</td>
		  <td scope="col"><input name="GetSign[]" type="checkbox" value="1"  onclick="checkSign(1)" <?php echo $GetSign1 ?>><span class="redB">是</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input name="GetSign[]"  type="checkbox" value="0"  onclick="checkSign(0)" <?php echo $GetSign0 ?> ><span class="blueB">否</span>
	    </tr>
        <tr  <?php  echo $HiddenStr?> id='GetQtyHidden'>
          <td height="22" valign="middle" scope="col" align="right">款项收回条件</td>
          <td valign="middle" scope="col"><input name="GetQty" type="text" id="GetQty" style="width: 380px;"  value="<?php echo $GetQty?>" /><span class="redB">(下单数量多少PCS)</span></td>
        </tr>

	    </tr>
        <!--<tr id='BomCompanyHidden'>
          <td height="22" valign="middle" scope="col" align="right">关联BOM采购供应商</td>
          <td valign="middle" scope="col"><input name="BomCompany" type="text" id="BomCompany" style="width: 380px;"  value="" readonly onclick="CheckBomCompany(this,2)" /></td>
        </tr>-->

      
        <tr>
          <td height="22" valign="middle" scope="col" align="right">配件单价</td>
          <td valign="middle" scope="col"><input name="Price" type="text" id="Price" style="width: 380px;" value="<?php echo $Price;?>" title="必填项" datatype="Price" msg="没有填写或格式不对" /></td>
        </tr>
        <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">配件单位</td>
			<td valign="middle" scope="col"><input name="Unit" type="text" id="Unit" style="width: 380px;" value="<?php echo $Unit;?>" title="必填项,2-4个字节的范围" maxlength="4" datatype="LimitB" max="4" min="2" msg="没有填写或超出字节的范围" >
			</td>
		</tr>
        <tr>
		  <td height="23" valign="middle" scope="col" align="right">配件图片</td>
		  <td valign="middle" scope="col"><input name="Attached" type="file" id="Attached"  style="width: 380px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="8" Cel="1"></td>
	    </tr>
         <?php 
		  if($Attached==1){
		  	echo"<tr><td height='40' scope='col'>&nbsp;</td>
			  <td scope='col'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'> 删除已传图片</LABEL></td></tr>";
			}
		  ?>
		  
        <tr>
            <td align="right">资产类型</td>
            <td><select name="AssetType" id="AssetType" style="width: 380px;"  dataType="Require"  msg="未选择资产类型" onchange="ShowDepreciation(this)">     
             <option value='' selected>请选择</option>
			<?php  
	          $mySql="SELECT Id,Name FROM $DataPublic.nonbom0_assettype  WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $thisAssetTypeId=$myrow["Id"];
				 $thisAssetTypeName=$myrow["Name"];
				 if($thisAssetTypeId == $AssetType){
					 echo "<option value='$thisAssetTypeId' selected>$thisAssetTypeName</option>"; 
				 }else{
					 echo "<option value='$thisAssetTypeId'>$thisAssetTypeName</option>"; 
				 }
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
			</td>
       </tr>
      
      
       <tr <?php echo $showDepreciation?> id='DepreciationHidden'>
          <td height="22" valign="middle" scope="col" align="right">折旧期</td>
          <td valign="middle" scope="col">
	          <select name="DepreciationId" id="DepreciationId" style="width: 380px;" >     
             <option value='' selected>请选择</option>
			<?php  
	          $mySql="SELECT Id,Depreciation,ListName FROM $DataPublic.nonbom6_depreciation  WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $thisDepreciationId=$myrow["Id"];
				 $thisDepreciationName=$myrow["ListName"];
				 if($thisDepreciationId == $DepreciationId){
					 echo "<option value='$thisDepreciationId' selected>$thisDepreciationName</option>"; 
				 }else{
					 echo "<option value='$thisDepreciationId'>$thisDepreciationName</option>"; 
				 }
				 
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
          </td>
        </tr>
      
      <tr>
          <td height="22" valign="middle" scope="col" align="right">残值率</td>
          <td valign="middle" scope="col"><input name="Salvage" type="text" id="Salvage" value="<?php echo $Salvage ?>" style="width: 380px;"  value="0.05" datatype="Price" msg="没有填写或格式不对" /></td>
        </tr>
       
		  
           <tr>
            <td align="right">入库地点</td>
            <td><select name="CkId" id="CkId" style="width: 380px;"  dataType="Require"  msg="未选择入库地点">     
             <option value='' selected>请选择</option>
			<?php  
	          $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1)  order by  Remark";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $FloorId=$myrow["Id"];
				 $FloorRemark=$myrow["Remark"];
				 $FloorName=$myrow["Name"];
				 if ($FloorId==$CkId){
				   echo "<option value='$FloorId' selected>$FloorName</option>";
				 }
				 else{
				   echo "<option value='$FloorId'>$FloorName</option>"; 
				 }
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
			</td>
       </tr>
<tr>
            <td align="right">使用年限</td>
            <td><select name="nxId" id="nxId" style="width: 380px;"    >     
             <option value='0' selected>请选择</option>
			<?php  
	          $nxmySql="SELECT * FROM $DataPublic.nonbom6_nx  WHERE Estate=1 ORDER BY days";
	          $nxresult = mysql_query($nxmySql,$link_id);
             if($nxmyrow = mysql_fetch_array($nxresult)){
	   	     do{
			     $thisId=$nxmyrow["Id"];
				 $Frequency=$nxmyrow["Frequency"];
				 $days=$nxmyrow["days"];
				 if ($thisId==$nxId){
				   echo "<option value='$thisId' selected>$Frequency--$days(days)</option>"; 
                   }
               else{
				   echo "<option value='$thisId'>$Frequency--$days(days)</option>"; 
                      }
			   }while ($nxmyrow = mysql_fetch_array($nxresult));
		    }
			?>
			</select>
			</td>
       </tr>


           <tr>
            <td align="right">盘点时间</td>
            <td><select name="pdDate" id="pdDate" style="width: 380px;"    >     
             <option value='0' selected>请选择</option>
			<?php  
	          $pdmySql="SELECT * FROM $DataPublic.nonbom6_nx  WHERE Estate=1 ORDER BY days ";
	          $pdresult = mysql_query($pdmySql,$link_id);
             if($pdmyrow = mysql_fetch_array($pdresult)){
	   	     do{
			       $pdId=$pdmyrow["Id"];
				   $pdFrequency=$pdmyrow["Frequency"];
				   $pddays=$pdmyrow["days"];
				 if ($pdId==$pdDate){
				   echo "<option value='$pdId' selected>$pdFrequency--$pddays(days)</option>"; 
                   }
               else{
				   echo "<option value='$pdId'>$pdFrequency--$pddays(days)</option>"; 
                      }
			   }while ($pdmyrow = mysql_fetch_array($pdresult));
		    }
			?>
			</select>
			</td>
       </tr>
        <tr >
            <td align="right" >内部维修人</td>
            <td> <input name="WxName" type="text" id="WxName" style="width:380px;" value="<?php echo $WxName?>"  >
                <input name="WxNumber" type="hidden" id="WxNumber"  value="<?php echo $WxNumber?>"  >
			</td>
       </tr>

        <tr>
            <td align="right" >外部维修公司</td>
            <td> <input name="WxForshort" type="text" id="WxForshort" style="width:380px;" value="<?php echo $WxForshort?>"  >
                <input name="WxCompanyId" type="hidden" id="WxCompanyId" value="<?php echo $WxCompanyId?>" >
			</td>
       </tr>

       <tr >
            <td align="right"  width="152">内部保养人</td>
            <td> <input name="ByName" type="text" id="ByName" style="width:380px;"  value="<?php echo $ByName?>"  >
                <input name="ByNumber" type="hidden" id="ByNumber" value="<?php echo $ByNumber?>" >
			</td>
       </tr>

        <tr>
            <td align="right"  >外部保养公司</td>
            <td> <input name="ByForshort" type="text" id="ByForshort" style="width:380px;"   value="<?php echo $ByForshort?>"  >
                <input name="ByCompanyId" type="hidden" id="ByCompanyId" value="<?php echo $ByCompanyId?>">
			</td>
       </tr>
                 <tr>
          <td align="right">最低库存</td>
          <td><input name="mStockQty" type="text" id="mStockQty" style="width: 380px;"  title="必填项,输入正整数" value="<?php echo $mStockQty;?>" dataType="Double" msg="没有填写或格式错误" /></td>
        </tr>

		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Remark" cols="51" rows="3" id="Remark" ><?php echo $Remark;?></textarea></td>
		</tr>
        </table>
</td></tr></table>
<?php 
$subCompanyId[]=0;
$subName[]="清空";
  $CompanySql = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
	while ($CompanyRow = mysql_fetch_array($CompanySql)){
		     $sCompanyId=$CompanyRow["CompanyId"];
                $sForshort=$CompanyRow["Forshort"];
		        $sLetter=$CompanyRow["Letter"];
                $subCompanyId[]=$sCompanyId;
                $subName[]=$sForshort;
	};
$subNumber[]=0;
$subthisName[]="清空";
	           $BymySql="SELECT S.Number,S.Name FROM $DataPublic.staffmain S WHERE 1 and Estate=1 ORDER BY Number ";
	           $Byresult = mysql_query($BymySql,$link_id);
               if($Bymyrow = mysql_fetch_array($Byresult)){
	   	       do{
                    $thisNumber=$Bymyrow["Number"];
                    $thisName=$Bymyrow["Name"];
                   $subNumber[]=$thisNumber;
                   $subthisName[]=$thisName;
                   	echo "<option value='$thisNumber'>$thisName</option>"; 
			         }while ($Bymyrow = mysql_fetch_array($Byresult));
		        }
//步骤5：
echo"<div id='Jp' style='position:absolute;width:400px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div>
	</div>";
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
 window.onload = function(){
        var subName=<?php  echo json_encode($subName);?>;
        var subCompanyId=<?php  echo json_encode($subCompanyId);?>;
        var subNumber=<?php  echo json_encode($subNumber);?>;
        var subthisName=<?php  echo json_encode($subthisName);?>;
	/*	var sinaSuggest = new InputSuggest({
			input: document.getElementById("Forshort"),
			poseinput: document.getElementById("CompanyId"),
			data: subName,
            id:subCompanyId,
			width: 290
		});          */
	var sinaSuggestByMan= new InputSuggest({
			input: document.getElementById('ByName'),
			poseinput: document.getElementById('ByNumber'),
			data: subthisName,
            id:subNumber,
			width: 290
		});        
		var sinaSuggestByCompany = new InputSuggest({
			input: document.getElementById('ByForshort'),
			poseinput: document.getElementById('ByCompanyId'),
			data: subName,
            id:subCompanyId,
			width: 290
		});        
		var sinaSuggestWxMan= new InputSuggest({
			input: document.getElementById('WxName'),
			poseinput: document.getElementById('WxNumber'),
			data: subthisName,
            id:subNumber,
			width: 290
		});   
		var sinaSuggestWxCompany = new InputSuggest({
			input: document.getElementById('WxForshort'),
			poseinput: document.getElementById('WxCompanyId'),
			data: subName,
            id:subCompanyId,
			width: 290
		});  
		
		ChecBoxSign();              	
	}

function ChecBoxSign(){
      var PropertyObj=  document.getElementsByName("Property[]");
        for(k=0;k<PropertyObj.length;k++){
           if(PropertyObj[1].checked){
                          document.getElementById("WxName").disabled=false;
                   }
           if(PropertyObj[2].checked){
                          document.getElementById("WxForshort").disabled=false;
                   }
           if(PropertyObj[3].checked){
                          document.getElementById("ByName").disabled=false;
                   }
           if(PropertyObj[4].checked){
                          document.getElementById("ByForshort").disabled=false;
                   }
           }

}

function getRule(e){
	var TypeId=e.value;
	 var NameRule=document.getElementById("NameRule");
     var url="nonbom4_rule.php?TypeId="+TypeId+"&do="+Math.random();
	 var ajax=InitAjax();
　	 ajax.open("GET",url,true);
     ajax.onreadystatechange =function(){
     if(ajax.readyState==4){//&& ajax.status ==200
			var BackData=ajax.responseText;
            var tempArray=BackData.split("|");
            var tempNameRule=tempArray[0];
            var tempmainType=tempArray[1];
            var tempGetSign=tempArray[2];
			if(tempNameRule!=""){
			         document.getElementById("NameRule").value=tempNameRule;
			     }
			else{
			         document.getElementById("NameRule").value="未设置";
			      }
                 //***************如果是开发费用，模具类配件，显示款项收回项目
                   showGet(tempmainType,tempGetSign);
		 	}	
	   	}
	 	ajax.send(null);
	}

function showGet(mainType,GetSign){
      var   GetSignHidden=document.getElementById("GetSignHidden");
      var   GetQtyHidden=document.getElementById("GetQtyHidden");
      var  GetSignArray=document.getElementsByName("GetSign[]");
      //var BomCompanyHidden=document.getElementById("BomCompanyHidden");
      GetSignArray[0].checked=false;      GetSignArray[1].checked=false;
      document.getElementById("GetQty").value="";
       if(mainType==8){
                  GetSignHidden.style.display="";
                  GetQtyHidden.style.display="";
                 // BomCompanyHidden.style.display="";
                  if(GetSign==0)GetSignArray[1].checked=true;
                  if(GetSign==1)GetSignArray[0].checked=true;
               }
        else{
                  GetSignHidden.style.display="none";
                  GetQtyHidden.style.display="none";
                 // BomCompanyHidden.style.display="none";
                }
}
function  checkSign(tempvalue){
     var getSign=document.getElementsByName("GetSign[]");
      if(tempvalue==0) getSign[0].checked="";
      if(tempvalue==1) getSign[1].checked="";

}

function ShowDepreciation(e){
	
	var DepreciationHidden = document.getElementById("DepreciationHidden");
	if(e.value ==2){
		DepreciationHidden.style.display="";
	}else{
		DepreciationHidden.style.display="none";
	}
	
}

function  Chooseproperty(e){
      var PropertyObj=  document.getElementsByName("Property[]");
      var CheckSign=0;
        for(k=0;k<PropertyObj.length;k++){
           if(PropertyObj[k].value==2 || PropertyObj[k].value==3 || PropertyObj[k].value==4 || PropertyObj[k].value==5){
                    if(PropertyObj[k].checked==true)CheckSign=1;
                   }
           }
       if(CheckSign==0){
              document.getElementById("ByName").disabled=true;
              document.getElementById("ByForshort").disabled=true;
              document.getElementById("WxName").disabled=true;
              document.getElementById("WxForshort").disabled=true;
           }
}


function ChooseCompany(){
           document.getElementById("Forshort").value="";
           document.getElementById("CompanyId").value="";
           var TypeId=document.getElementById("TypeId").value;
           if(TypeId!=""){
                	xmlHttp=GetXmlHttpObject();
               	if(xmlHttp==null){
	          	  alert ("Browser does not support HTTP Request");
                	return;
                	}
                 var url="nonbom4_companyajax.php?TypeId="+TypeId+"&ActionId=1";
	               xmlHttp.onreadystatechange=function(){stateChanged("Forshort")};
                  xmlHttp.open("GET",url,true);
                  xmlHttp.send(null);
                 }
      }

function ChooseCompanyName(index){
   var returnValue=document.getElementById("TempName"+index).innerHTML;
    var returnArray=returnValue.split("-");
   	document.getElementById("Forshort").value=returnValue;
    document.getElementById("CompanyId").value=returnArray[0];
   	livesearch.style.display="none";
   	iframe2.style.display="none";
	}

function SetDefaultCompany(e,toObj){
        var Y=event.clientY;
        var TypeId=document.getElementById("TypeId").value;
        if(TypeId!=""){
                      var url="nonbom4_companyajax.php?TypeId="+TypeId+"&ActionId=2";
                	   var ajax=InitAjax();
　                	 ajax.open("GET",url,true);
                        ajax.onreadystatechange =function(){
                        if(ajax.readyState==4){//&& ajax.status ==200
                                     BackData(Y,toObj,ajax.responseText);
		                      	  }	
	                      	}
                	ajax.send(null);
             }
}
 function CheckBomCompany(e,toObj){
        var Y=event.clientY;
         BackData(Y,toObj);
}


function BackData(Y,toObj,RebackDate){
	var InfoSTR="";
	var buttonSTR="";
	var runningNum="";
	var BackData="";
	var theDiv=document.getElementById("Jp");
	var infoShow=document.getElementById("infoShow");
	
	var ObjId=document.form1.ObjId.value;
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId ){
		document.form1.ObjId.value=toObj;
          switch(toObj){
               case 1:
                var RebackDateArray=RebackDate.split("@");
				infoShow.style.width=238;
				 infoShow.style.height=RebackDateArray[1]*25+20;
				 
				 theDiv.style.width=238;
				 theDiv.style.height=RebackDateArray[1]*25+20;			 
				 InfoSTR=RebackDateArray[0];
		  var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确  定' onclick=' setValue("+toObj+")'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取  消' onclick='CloseDiv()'>";

		  infoShow.innerHTML=InfoSTR+buttonSTR;
		  theDiv.style.top=Y+10+'px';
	      theDiv.style.left= document.body.scrollLeft+610+'px';
		  theDiv.style.visibility = "";
		  theDiv.style.display="";

              var  checkdefaultId=document.getElementsByName("checkdefaultId[]");
               if(checkdefaultId!=undefined || checkdefaultId.length>0){
	                  var CompanyCheckId=document.getElementsByName("CompanyCheckId[]");
                        for(n=0;n<CompanyCheckId.length;n++){
                                var TempIdArray=CompanyCheckId[n].value.split("|");
                                 for(k=0;k<checkdefaultId.length;k++){
                                       if(TempIdArray[0]==checkdefaultId[k].value)CompanyCheckId[n].checked=true;
                                 }
                           }
                     }
                   break;
                  case 2:
                          infoShow.style.width=238;
                          infoShow.style.height=140;
                          InfoSTR="<table width='237'><tr><td height='40'>包装供应商：<select name='mainCompanyId' id='mainCompanyId' style='width: 150px;' ><option value='0' selected>请选择</option>";
			           	<?PHP 
		               			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE 1 AND Estate='1'   AND  ObjectSign IN (1,3) order by Letter";			
			          			$checkResult = mysql_query($checkSql); 
		          				while ( $checkRow = mysql_fetch_array($checkResult)){
			          				$themainCompanyId=$checkRow["CompanyId"];
			          				$themainForshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];				
                                   $mcInfo.= "<option value='$themainCompanyId'>$themainForshort</option>";
			          		} 
			           	?>
                         InfoSTR+="<?PHP echo $mcInfo; ?>"+"</select></td></tr>";


                          InfoSTR+="<tr><td height='40'>皮套供应商：<select name='ptsubCompanyId' id='ptsubCompanyId' style='width: 150px;' ><option value='0' selected>请选择</option>";
			           	<?PHP 
			                	$checkptsubSql = "SELECT CompanyId,Forshort,Letter FROM $DataOut.providerdata WHERE 1 AND Estate='1' order by Letter";			
			           	        $checkptsubResult = mysql_query($checkptsubSql); 
			                	while ( $checkptsubRow = mysql_fetch_array($checkptsubResult)){
			                  	      $theptsubCompanyId=$checkptsubRow["CompanyId"];
				           				$theptsubForshort=$checkptsubRow["Letter"].'-'.$checkptsubRow["Forshort"];
                                       $ptInfo.= "<option value='$theptsubCompanyId' >$theptsubForshort</option>";
						           	} 
			           	?>
                         InfoSTR=InfoSTR+"<?PHP echo $ptInfo; ?>"+"</select></td></tr>";

                          var buttonSTR="<tr><td align='right' height='50'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确  定' onclick=' setValue("+toObj+")'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取  消' onclick='CloseDiv()'></td></tr></table>";
                          infoShow.innerHTML=InfoSTR+buttonSTR;
                          theDiv.style.top=Y+10+'px';
                          theDiv.style.left= document.body.scrollLeft+610+'px';
                          theDiv.style.visibility = "";
                          theDiv.style.display="";
                     break;
               }
		}
}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
     document.getElementById("DefaultCompany").checked=false;
	}
	
function setValue(toObj){
    switch(toObj){
	    case 1:
	       var returnId="";
           var returnName="";
           var CheckSign=0;
           var  CompanyDiv=document.getElementById("CompanyDiv");
	       var CompanyCheckId=document.getElementsByName("CompanyCheckId[]");
                var DivStr="";
	        for(var j=0;j<CompanyCheckId.length;j++){
	            if (CompanyCheckId[j].checked){
                     CheckSign++;
                     if(CheckSign==1) document.getElementById("ShowCompany").style.display="";  
                      var tempArray=CompanyCheckId[j].value.split("|");
                     DivStr+="<DIV style='height:25px;'>"+tempArray[1]+"   <input type='hidden' id='checkdefaultId' name='checkdefaultId[]' size='12' value='"+tempArray[0]+"'><input type='text' id='checkdefaultPrice' name='checkdefaultPrice[]' size='12' value='0.00'><br></DIV>";
	            }
	        }
           //DivStr="<DIV>"+DivStr+"</DIV>";
           CompanyDiv.innerHTML=DivStr;
	       break;
           case 2:
                  var mainCompanyId=document.getElementById("mainCompanyId");
                  var ptsubCompanyId=document.getElementById("ptsubCompanyId");    
                  var ReturnValue="";
                   if(mainCompanyId.value>0){
                           ReturnValue=mainCompanyId.value+"~"+mainCompanyId.options[mainCompanyId.selectedIndex].text+"~7";
                       }
                   if(ptsubCompanyId.value>0){
                        if(ReturnValue=="")ReturnValue=ptsubCompanyId.value+"~"+ptsubCompanyId.options[ptsubCompanyId.selectedIndex].text+"~3";
                        else  ReturnValue=ReturnValue+"@"+ptsubCompanyId.value+"~"+ptsubCompanyId.options[ptsubCompanyId.selectedIndex].text+"~3";
                       }     
                    document.getElementById("BomCompany").value=ReturnValue;
              break;
    }
     CloseDiv();
}

function  checkSign(tempvalue){
     var getSign=document.getElementsByName("GetSign[]");
      if(tempvalue==0) getSign[0].checked="";
      if(tempvalue==1) getSign[1].checked="";

}

function  linksTools(e){
    var ToolsId  = document.getElementById("ToolsId");
    var GoodsName  = document.getElementById("GoodsName");
    var r=Math.random();
	var BackData=window.showModalDialog("../public/fixturetool_s1.php?r="+r+"&tSearchPage=fixturetool&fSearchPage=fixturetool&SearchNum=1"+"&Action=1","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
	   var CL=BackData.split("^^");
	   ToolsId.value = CL[0];
	   //GoodsName.value = CL[1];
	}
}

function clearTools(){
	var ToolsId  = document.getElementById("ToolsId");
	ToolsId.value = "";
}
</script>