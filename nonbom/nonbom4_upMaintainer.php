<?php 
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
ChangeWtitle("$SubCompany 更新维护保养人员");//需处理
$nowWebPage =$funFrom."_upMaintainer";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$tableWidth=600;$tableMenuS=300;
include "../model/subprogram/add_model_t.php";
$TempArray=explode("|",$Mid);
$GoodsId=$TempArray[0];
$TempId=$TempArray[1];
$PropertySign=$TempArray[2];
$CheckGoodsResult=mysql_fetch_array(mysql_query("SELECT GoodsName  FROM $DataPublic.nonbom4_goodsdata  WHERE GoodsId=$GoodsId ",$link_id));
$GoodsName=$CheckGoodsResult["GoodsName"];
$GoodsName=$GoodsName."<img src='../images/good$PropertySign.gif'  width='18' height='18'>";
if($PropertySign==2){//内部维修
                $WxNumber=$TempId;
                $CheckWxNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$WxNumber",$link_id));
                $WxName=$CheckWxNumberResult["Name"];
}

if($PropertySign==3){//外部维修
                   $WxCompanyId=$TempId;
                    $CheckWxCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$WxCompanyId",$link_id));
                     $WxForshort=$CheckWxCompanyResult["Forshort"];
}

if($PropertySign==4){//内部保养
                   $ByNumber=$TempId;
                   $CheckByNumberResult=mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$ByNumber",$link_id));
                   $ByName=$CheckByNumberResult["Name"];
}


if($PropertySign==5){//外部保养
                $ByCompanyId=$TempId;
                 $CheckByCompanyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$ByCompanyId",$link_id));
                $ByForshort=$CheckByCompanyResult["Forshort"];
}


$ActionId="AddMaintainer";	
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,GoodsId,$GoodsId,ActionId,$ActionId,PropertySign,$PropertySign";
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 
    <input name="Mid" id="Mid" type="hidden" value="<?php  echo $Id?>">
    <table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2"   valign="bottom">◆非BOM配件资料:<span class="redB"><?php  echo $GoodsName?></span></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
<?php
// 内部维修人
if($PropertySign==2){  
?>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">内部维修人：</td>
            <td scope="col">
             <input name="WxName" type="text" id="WxName" style="width:380px;"   value="<?php echo $WxName?>">
                <input name='WxNumber' type='hidden' id='WxNumber'   value="<?php echo $WxNumber?>">           
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
<?php
}
if($PropertySign==3){
?>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">外部维修公司：</td>
            <td scope="col">
             <input name="WxForshort" type="text" id="WxForshort" style="width:380px;" value="<?php echo $WxForshort?>" >
                <input name='WxCompanyId' type='hidden' id='WxCompanyId' value="<?php echo $WxCompanyId?>" >           
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
<?php
}
if($PropertySign==4){
?>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">内部保养人：</td>
            <td scope="col">
             <input name="ByName" type="text" id="ByName" style="width:380px;"  value="<?php echo $ByName?>" >
                <input name='ByNumber' type='hidden' id='ByNumber' value="<?php echo $ByNumber?>" >           
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
<?php
    }
if($PropertySign==5){
?>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">外部保养公司：</td>
            <td scope="col">
             <input name="ByForshort" type="text" id="ByForshort" style="width:380px;"   value="<?php echo $ByForshort?>">
                <input name='ByCompanyId' type='hidden' id='ByCompanyId' value="<?php echo $ByCompanyId?>">           
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
<?php
}
?>
             
	</table>
	<input name="hfield" type="hidden" id="hfield" value="0">
<input name="SubName0" id="SubName0" type="hidden" value="">
    
<?php 
//步骤5：
$subCompanyId[]=0;
$subName[]="清空";
  $CompanySql = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
	while ($CompanyRow = mysql_fetch_array($CompanySql)){
		     $sCompanyId=$CompanyRow["CompanyId"];
                $sForshort=$CompanyRow["Forshort"];
		        $sLetter=$CompanyRow["Letter"];
                $subCompanyId[]=$sCompanyId;
                $subName[]=$sForshort;
	}

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
include "../model/subprogram/add_model_b.php";

?>

<script LANGUAGE='JavaScript'  type="text/JavaScript">
 window.onload = function(){
       var PropertySign=<?php  echo json_encode($PropertySign);?>;
        var subName=<?php  echo json_encode($subName);?>;
        var subCompanyId=<?php  echo json_encode($subCompanyId);?>;
        var subNumber=<?php  echo json_encode($subNumber);?>;
        var subthisName=<?php  echo json_encode($subthisName);?>;
     if(PropertySign==2){
		var sinaSuggestWxMan= new InputSuggest({
			input: document.getElementById('WxName'),
			poseinput: document.getElementById('WxNumber'),
			data: subthisName,
            id:subNumber,
			width: 290
		});   
      }
     if(PropertySign==3){
		var sinaSuggestWxCompany = new InputSuggest({
			input: document.getElementById('WxForshort'),
			poseinput: document.getElementById('WxCompanyId'),
			data: subName,
            id:subCompanyId,
			width: 290
		});             
     }
     if(PropertySign==4){
		var sinaSuggestByMan= new InputSuggest({
			input: document.getElementById('ByName'),
			poseinput: document.getElementById('ByNumber'),
			data: subthisName,
            id:subNumber,
			width: 290
		});        
    }
     if(PropertySign==5){
		var sinaSuggestByCompany = new InputSuggest({
			input: document.getElementById('ByForshort'),
			poseinput: document.getElementById('ByCompanyId'),
			data: subName,
            id:subCompanyId,
			width: 290
		});   	
     }
}
</script>
