<?php
//EWEN 2013-02-18 OK
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
 ?>
 <style type="text/css">
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
//步骤2：
ChangeWtitle("$SubCompany 新增非bom配件资料");//需处理
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
	<table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
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
            <input name="NameRule" id="NameRule"  style="width:380px;border:0px;color:red" readonly/>
          </td>
         </tr>
         <tr>
			<td height="22" valign="middle" scope="col" align="right">配件名称</td>
			<td valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName" title="必填项,2-100个字节的范围"  style="width: 380px;" maxlength="100" datatype="LimitB" max="100" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'GoodsName','nonbom4_goodsdata','1','')" onblur="LoseFocus()" autocomplete="off">
			<span class="redB">治工具</span><input name='ToolsId' type='text' id='ToolsId'  onclick="linksTools(this)" size="6" readonly><a onclick="clearTools()"><span class="redB">清除</span></a>
            </td>
		</tr>
           <tr>
            <td align="right" height="30">配件属性</td>
            <td>
            <?php
               $x=0;
			    $checkResult = mysql_query("SELECT * FROM $DataPublic.nonbom4_propertytype  WHERE Estate=1 ORDER BY Id",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $PropertyId=$checkRow["Id"];
                    $TypeName=$checkRow["TypeName"];
                    $TypeColor=$checkRow["TypeColor"];
                    if ($x>0 && $x%8==0) echo "<br>";
                    echo "<input name='Property[]' type='checkbox' value='$PropertyId' onclick='Chooseproperty(this)'><span style='color:$TypeColor;'>$TypeName</span>&nbsp;&nbsp;&nbsp;&nbsp;";
                    $x++;
                    }
			?>
          </td>
        </tr>
        <tr>
           <td align="right">默认供应商</td>
           <td> <input name="Forshort" type="text" id="Forshort" style="width:380px;"  dataType="Require"   msg="未填写" onclick="ChooseCompany()" onblur="LoseFocus()" >
      <input name='CompanyId' type='hidden' id='CompanyId' ><input  type="checkbox" id="DefaultCompany"  name="DefaultCompany" onclick="SetDefaultCompany(this,1)">
          </td>
         </tr>
       <tr style="display:none" id="ShowCompany"><td > &nbsp;</td><td id="CompanyDiv"></td></tr>

<!-- 模具类配件需要收回款项，显示，不需要的隐藏-->
		<tr style="display:none" id='GetSignHidden'>
		  <td height="30" align="right" scope="col">款项是否收回</td>
		  <td scope="col"><input name="GetSign[]" type="checkbox" value="1"  onclick="checkSign(1)" ><span class="redB">是</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input name="GetSign[]"  type="checkbox" value="0"  onclick="checkSign(0)" ><span class="blueB">否</span>
	    </tr>
        <tr style="display:none" id='GetQtyHidden'>
          <td height="22" valign="middle" scope="col" align="right">款项收回条件</td>
          <td valign="middle" scope="col"><input name="GetQty" type="text" id="GetQty" style="width: 380px;"  value="" /><span class="redB">(产品达到多少PCS)</span></td>
        </tr>

	    </tr>
       <!-- <tr style="display:none" id='BomCompanyHidden'>
          <td height="22" valign="middle" scope="col" align="right">关联BOM采购供应商</td>
          <td valign="middle" scope="col"><input name="BomCompany" type="text" id="BomCompany" style="width: 380px;"  value="" readonly onclick="CheckBomCompany(this,2)" /></td>
        </tr>-->



<!- - ---------------- ---- -->

        <tr>
          <td height="22" valign="middle" scope="col" align="right">配件单价</td>
          <td valign="middle" scope="col"><input name="Price" type="text" id="Price" style="width: 380px;"  value="0" title="必填项" datatype="Price" msg="没有填写或格式不对" /></td>
        </tr>
        <tr>
			<td height="22" valign="middle" scope="col" align="right">配件单位</td>
			<td valign="middle" scope="col"><input name="Unit" type="text" id="Unit" style="width: 380px;"  title="必填项,2-4个字节的范围" maxlength="4" datatype="LimitB" max="4" min="2" msg="没有填写或超出字节的范围" >
			</td>
		</tr>
        <tr>
		  <td height="22" valign="middle" scope="col" align="right">配件图片</td>
		  <td valign="middle" scope="col"><input name="Attached" type="file" id="Attached"  style="width: 380px;" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="8" Cel="1"></td>
	    </tr>


           <tr>
            <td align="right">资产类型</td>
            <td><select name="AssetType" id="AssetType" style="width: 380px;"  dataType="Require"  msg="未选择资产类型" onchange="ShowDepreciation(this)">
             <option value='' selected>请选择</option>
			<?php
	          $mySql="SELECT Id,Name FROM $DataPublic.nonbom0_assettype  WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $AssetTypeId=$myrow["Id"];
				 $AssetTypeName=$myrow["Name"];
				 echo "<option value='$AssetTypeId'>$AssetTypeName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
			</td>
       </tr>


       <tr style="display:none" id='DepreciationHidden'>
          <td height="22" valign="middle" scope="col" align="right">折旧期</td>
          <td valign="middle" scope="col">
	          <select name="DepreciationId" id="DepreciationId" style="width: 380px;" >
             <option value='' selected>请选择</option>
			<?php
	          $mySql="SELECT Id,Depreciation,ListName FROM $DataPublic.nonbom6_depreciation  WHERE Estate=1 order by Id";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $DepreciationId=$myrow["Id"];
				 $DepreciationName=$myrow["ListName"];
				 echo "<option value='$DepreciationId'>$DepreciationName</option>";
			   }while ($myrow = mysql_fetch_array($result));
		    }
			?>
			</select>
          </td>
        </tr>

      <tr>
          <td height="22" valign="middle" scope="col" align="right">残值率</td>
          <td valign="middle" scope="col"><input name="Salvage" type="text" id="Salvage" style="width: 380px;"  value="0.05" datatype="Price" msg="没有填写或格式不对" /></td>
        </tr>



      <tr>
            <td align="right">入库地点</td>
            <td><select name="CkId" id="CkId" style="width: 380px;"  dataType="Require"  msg="未选择入库地点">
             <option value='' selected>请选择</option>
			<?php
	          $mySql="SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1) order by  Remark";
	          $result = mysql_query($mySql,$link_id);
             if($myrow = mysql_fetch_array($result)){
	   	     do{
			     $FloorId=$myrow["Id"];
				 $FloorRemark=$myrow["Remark"];
				 $FloorName=$myrow["Name"];
				 echo "<option value='$FloorId'>$FloorName</option>";
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

				   echo "<option value='$thisId'>$Frequency--$days(days)</option>";
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
				   echo "<option value='$pdId'>$pdFrequency--$pddays(days)</option>";
			   }while ($pdmyrow = mysql_fetch_array($pdresult));
		    }
			?>
			</select>
			</td>
       </tr>


        <tr >
            <td align="right" >内部维修人</td>
            <td> <input name="WxName" type="text" id="WxName" style="width:380px;"  disabled>
                <input name='WxNumber' type='hidden' id='WxNumber' >
			</td>
       </tr>

        <tr>
            <td align="right" >外部维修公司</td>
            <td> <input name="WxForshort" type="text" id="WxForshort" style="width:380px;" disabled >
                <input name='WxCompanyId' type='hidden' id='WxCompanyId' >
			</td>
       </tr>

       <tr >
            <td align="right"  width="152">内部保养人</td>
            <td> <input name="ByName" type="text" id="ByName" style="width:380px;"   disabled >
                <input name='ByNumber' type='hidden' id='ByNumber' >
			</td>
       </tr>

        <tr>
            <td align="right"  >外部保养公司</td>
            <td> <input name="ByForshort" type="text" id="ByForshort" style="width:380px;"   disabled >
                <input name='ByCompanyId' type='hidden' id='ByCompanyId' >
			</td>
       </tr>

        <tr>
          <td align="right">最低库存</td>
          <td><input name="mStockQty" type="text" id="mStockQty" style="width: 380px;"  title="必填项,输入正整数" dataType='Number' value="0" msg="没有填写或格式错误" /></td>
        </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Remark" cols="51" rows="3" id="Remark" ></textarea></td>
		</tr>
	</table>
</td></tr></table>
<?php
//步骤5：
echo"<div id='Jp' style='position:absolute;width:400px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div>
	</div>";

  $CompanySql = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
	while ($CompanyRow = mysql_fetch_array($CompanySql)){
		     $sCompanyId=$CompanyRow["CompanyId"];
                $sForshort=$CompanyRow["Forshort"];
		        $sLetter=$CompanyRow["Letter"];
                $subCompanyId[]=$sCompanyId;
                $subName[]=$sForshort;
};
$BymySql="SELECT S.Number,S.Name FROM $DataPublic.staffmain S WHERE 1 and Estate=1 ORDER BY Number ";
$Byresult = mysql_query($BymySql,$link_id);
 if($Bymyrow = mysql_fetch_array($Byresult)){
do{
       $thisNumber=$Bymyrow["Number"];
      $thisName=$Bymyrow["Name"];
      $subNumber[]=$thisNumber;
      $subthisName[]=$thisName;
     // echo "<option value='$thisNumber'>$thisName</option>";
	 }while ($Bymyrow = mysql_fetch_array($Byresult));
 }

include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
 window.onload = function(){
        var subName=<?php  echo json_encode($subName);?>;
        var subCompanyId=<?php  echo json_encode($subCompanyId);?>;
        var subNumber=<?php  echo json_encode($subNumber);?>;
        var subthisName=<?php  echo json_encode($subthisName);?>;
	/*	var sinaSuggest = new InputSuggest({
			input: document.getElementById('Forshort'),
			poseinput: document.getElementById('CompanyId'),
			data: subName,
            id:subCompanyId,
			width: 290
		});    */
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
      //var BomCompanyHidden=document.getElementById("BomCompanyHidden");
      var  GetSignArray=document.getElementsByName("GetSign[]");
       GetSignArray[0].checked=false;
       GetSignArray[1].checked=false;
        document.getElementById("GetQty").value="";
       if(mainType==8){
                  GetSignHidden.style.display="";
                  GetQtyHidden.style.display="";
                  //BomCompanyHidden.style.display="";
                  if(GetSign==0)GetSignArray[1].checked=true;
                  if(GetSign==1)GetSignArray[0].checked=true;
               }
        else{
                  GetSignHidden.style.display="none";
                  GetQtyHidden.style.display="none";
                  //BomCompanyHidden.style.display="none";
                }
}

function ShowDepreciation(e){

	var DepreciationHidden = document.getElementById("DepreciationHidden");
	if(e.value ==2){
		DepreciationHidden.style.display="";
	}else{
		DepreciationHidden.style.display="none";
	}

}
function  checkSign(tempvalue){
     var getSign=document.getElementsByName("GetSign[]");
      if(tempvalue==0) getSign[0].checked="";
      if(tempvalue==1) getSign[1].checked="";

}


function  Chooseproperty(e){
      var PropertyObj=  document.getElementsByName("Property[]");
               if(e.value==2 || e.value==3){
                         if(e.value==2)PropertyObj[2].checked=false;
                            if(e.value==3)PropertyObj[1].checked=false;
                   }
           if(e.value==4 || e.value==5){
                         if(e.value==4)PropertyObj[4].checked=false;
                            if(e.value==5)PropertyObj[3].checked=false;
                   }


    if(e.value==2){
              document.getElementById("WxName").disabled=false;
              document.getElementById("WxForshort").disabled=true;
           }

       if(e.value==3){
              document.getElementById("WxName").disabled=true;
              document.getElementById("WxForshort").disabled=false;
           }
     if(e.value==4){
              document.getElementById("ByName").disabled=false;
              document.getElementById("ByForshort").disabled=true;
           }
     if(e.value==5){
              document.getElementById("ByName").disabled=true;
              document.getElementById("ByForshort").disabled=false;
           }
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
		               			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE 1 AND Estate='1' AND  ObjectSign IN (1,3) order by Letter";
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

function  linksTools(e){
    var ToolsId  = document.getElementById("ToolsId");
    var GoodsName  = document.getElementById("GoodsName");
    var r=Math.random();
	var BackData=window.open("../public/fixturetool_s1.php?r="+r+"&tSearchPage=fixturetool&fSearchPage=fixturetool&SearchNum=1"+"&Action=1","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
	   var CL=BackData.split("^^");
	   ToolsId.value = CL[0];
	   GoodsName.value = CL[1];
	}
}

function clearTools(){
	var ToolsId  = document.getElementById("ToolsId");
	ToolsId.value = "";
}


</script>