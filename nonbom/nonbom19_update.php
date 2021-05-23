<?php 
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
ChangeWtitle("$SubCompany 更新非BOM资产维修记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT D.Id,D.WxReason,D.Picture,D.Estate,D.Date,D.Operator,D.WxNumber, D.WxCompanyId,D.WxDate,
A.Attached,A.GoodsId,C.BarCode,C.GoodsNum,A.GoodsName,C.Picture AS BarPicture
FROM $DataIn.nonbom7_repair  D 
LEFT JOIN $DataIn.nonbom7_code  C  ON D.BarCode=C.BarCode
LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId
WHERE D.Id='$Id'",$link_id));
$GoodsName=$upData["GoodsName"];
$BarCode=$upData["BarCode"];
$GoodsNum=$upData["GoodsNum"];
$GoodsId=$upData["GoodsId"];
$WxNumber=$upData["WxNumber"];
$WxCompanyId=$upData["WxCompanyId"];
$WxDate=$upData["WxDate"];
$WxReason=$upData["WxReason"];

        if($WxNumber>0){
                   $CheckWxNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$WxNumber",$link_id));
                   $WxName=$CheckWxNumberResult["Name"];
         }
        else{
                $WxName="&nbsp;";
              }
        if($WxCompanyId>0){
                 $CheckWxCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$WxCompanyId",$link_id));
                  $WxForshort=$CheckWxCompanyResult["Forshort"];
             }
          else{
                $WxForshort="&nbsp;";
               }
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
  <table width="<?php  echo $tableWidth?>" border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2"   valign="bottom">&nbsp;</td>
			
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" width="200px" height="30">资产名称：</td>
            <td scope="col">
             <input name="GoodsName" type="text" id="GoodsName" value="<?PHP echo $GoodsName?>" style="width:300px;" onclick="getGoodsName('nonbom17','<?php echo $funFrom?>',1,8,event)"  dataType="Require"   msg="未填写"  readonly>
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>

		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" width="200px" height="30">资产条码：</td>
            <td scope="col">
             <input name="BarCode" type="text" id="BarCode" value="<?PHP echo $BarCode?>" style="width:300px;"  dataType="Require"   msg="未填写"  readonly>
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>

		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" width="200px" height="30">资产编号：</td>
            <td scope="col">
             <input name="GoodsNum" type="text" id="GoodsNum"  value="<?PHP echo $GoodsNum?>" style="width:300px;"  dataType="Require"   msg="未填写"  readonly>
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>

		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" width="200px" height="30">内部维修人：</td>
            <td scope="col">
             <input name="WxName" type="text" id="WxName" style="width:300px;"   value="<?php echo $WxName ?>" >
                <input name="WxNumber" type="hidden" id="WxNumber"  value="<?php echo $WxNumber ?>">   
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>

		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" width="200px" height="30">外部维修公司：</td>
            <td scope="col">
             <input name="WxForshort" type="text" id="WxForshort" style="width:300px;"   value="<?php echo $WxForshort ?>"  >
                <input name="WxCompanyId" type="hidden" id="WxCompanyId"  value="<?php echo $WxCompanyId ?>" >   
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
    
		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" height="30">维修日期：</td>
            <td scope="col"> 
               <input name="WxDate" type="text" id="WxDate" value="<?php echo $WxDate ?>" style="width:300px;" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>  
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>   


	<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" height="30">单 &nbsp;&nbsp;&nbsp;据：</td>
            <td scope="col"> 
               <input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1">  
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>   
        
 		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">维修原因：</td>
            <td scope="col">
               <textarea name="WxReason" cols="20" rows="6" id="WxReason"  style="width:300px;"><?php echo $WxReason?></textarea>
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>              
	</table>

<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
  $CompanySql = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
	while ($CompanyRow = mysql_fetch_array($CompanySql)){
		     $sCompanyId=$CompanyRow["CompanyId"];
                $sForshort=$CompanyRow["Forshort"];
		        $sLetter=$CompanyRow["Letter"];
                $subCompanyId[]=$sCompanyId;
                $subName[]=$sForshort;
	}

	           $WxmySql="SELECT S.Number,S.Name FROM $DataPublic.staffmain S WHERE 1 and Estate=1 ORDER BY Number ";
	           $Wxresult = mysql_query($WxmySql,$link_id);
               if($Wxmyrow = mysql_fetch_array($Wxresult)){
	   	       do{
                    $thisNumber=$Wxmyrow["Number"];
                    $thisName=$Wxmyrow["Name"];
                   $subNumber[]=$thisNumber;
                   $subthisName[]=$thisName;
                   	echo "<option value='$thisNumber'>$thisName</option>"; 
			         }while ($Wxmyrow = mysql_fetch_array($Wxresult));
		        }

?>
<script>
window.onload = function(){
        var subName=<?php  echo json_encode($subName);?>;
        var subCompanyId=<?php  echo json_encode($subCompanyId);?>;
        var subNumber=<?php  echo json_encode($subNumber);?>;
        var subthisName=<?php  echo json_encode($subthisName);?>;
                
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

function getGoodsName(tSearchPage,fSearchPage,SearchNum,Action,Oevent){
	var r=Math.random();
	var theName="";
	if(! window.event){  
	  event =Oevent; //处理兼容性，获得事件对象
	  theName=event.target.getAttribute('name');
	  event =""; 
	}
	else {
		theName=event.srcElement.getAttribute('name');
	}
	var e=eval("document.form1."+theName);
	var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
		var CL=BackData.split("^^");
		document.form1.GoodsName.value=CL[0];
		document.form1.BarCode.value=CL[1];
		document.form1.GoodsNum.value=CL[2];
		document.form1.WxNumber.value=CL[3];
		document.form1.WxName.value=CL[4];
		document.form1.WxCompanyId.value=CL[5];
		document.form1.WxForshort.value=CL[6];
		}
  }
</script>
