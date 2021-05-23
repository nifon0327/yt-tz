<?php 
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
ChangeWtitle("$SubCompany 更新非BOM资产保养记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT D.Id,D.ByReason,D.Picture,D.Estate,D.Date,D.Operator,D.ByNumber, D.ByCompanyId,D.ByDate,
A.Attached,A.GoodsId,C.BarCode,C.GoodsNum,A.GoodsName,C.Picture AS BarPicture
FROM $DataIn.nonbom7_care  D 
LEFT JOIN $DataIn.nonbom7_code  C  ON D.BarCode=C.BarCode
LEFT JOIN $DataPublic.nonbom4_goodsdata A ON C.GoodsId=A.GoodsId
WHERE D.Id='$Id'",$link_id));
$GoodsName=$upData["GoodsName"];
$BarCode=$upData["BarCode"];
$GoodsNum=$upData["GoodsNum"];
$GoodsId=$upData["GoodsId"];
$ByNumber=$upData["ByNumber"];
$ByCompanyId=$upData["ByCompanyId"];
$ByDate=$upData["ByDate"];
$ByReason=$upData["ByReason"];

        if($ByNumber>0){
                   $CheckByNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$ByNumber",$link_id));
                   $ByName=$CheckByNumberResult["Name"];
         }
        else{
                $ByName="&nbsp;";
              }
        if($ByCompanyId>0){
                 $CheckByCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$ByCompanyId",$link_id));
                  $ByForshort=$CheckByCompanyResult["Forshort"];
             }
          else{
                $ByForshort="&nbsp;";
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
             <input name="GoodsName" type="text" id="GoodsName" value="<?PHP echo $GoodsName?>" style="width:300px;" onclick="getGoodsName('nonbom17','<?php echo $funFrom?>',1,7,event)"  dataType="Require"   msg="未填写"  readonly>
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
            <td scope="col" align="right" width="200px" height="30">内部保养人：</td>
            <td scope="col">
             <input name="ByName" type="text" id="ByName" style="width:300px;"   value="<?php echo $ByName ?>" >
                <input name="ByNumber" type="hidden" id="ByNumber"  value="<?php echo $ByNumber ?>">   
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>

		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" width="200px" height="30">外部保养公司：</td>
            <td scope="col">
             <input name="ByForshort" type="text" id="ByForshort" style="width:300px;"   value="<?php echo $ByForshort ?>"  >
                <input name="ByCompanyId" type="hidden" id="ByCompanyId"  value="<?php echo $ByCompanyId ?>" >   
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
    
		<tr >
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right" height="30">保养日期：</td>
            <td scope="col"> 
               <input name="ByDate" type="text" id="ByDate" value="<?php echo $ByDate ?>" style="width:300px;" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>  
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
            <td scope="col" align="right">保养原因：</td>
            <td scope="col">
               <textarea name="ByReason" cols="20" rows="6" id="ByReason"  style="width:300px;"><?php echo $ByReason?></textarea>
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

?>
<script>
window.onload = function(){
        var subName=<?php  echo json_encode($subName);?>;
        var subCompanyId=<?php  echo json_encode($subCompanyId);?>;
        var subNumber=<?php  echo json_encode($subNumber);?>;
        var subthisName=<?php  echo json_encode($subthisName);?>;
                
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
		document.form1.ByNumber.value=CL[3];
		document.form1.ByName.value=CL[4];
		document.form1.ByCompanyId.value=CL[5];
		document.form1.ByForshort.value=CL[6];
		}
  }
</script>
