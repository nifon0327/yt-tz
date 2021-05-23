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
    float: left;
 }  
.closeinco{
 position: relative;
 float: right;
 right: -491px;
 top: -1625px;
}
</style>
<?php 
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
ChangeWtitle("$SubCompany 关联BOM采购供应商");//需处理
$nowWebPage =$funFrom."_BomCompany";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$tableWidth=800;$tableMenuS=500;
$GoodsId=$Mid;
include "../model/subprogram/add_model_t.php";
$CheckGoodsResult=mysql_fetch_array(mysql_query("SELECT GoodsName  FROM $DataPublic.nonbom4_goodsdata  WHERE GoodsId=$GoodsId ",$link_id));
$GoodsName=$CheckGoodsResult["GoodsName"];



$mCompanyId="";
$mCompanyName="";
$CheckMainResult=mysql_query("SELECT B.CompanyId,T.Forshort  FROM  $DataPublic.nonbom4_bomcompany  B  
LEFT JOIN $DataIn.trade_object  T ON T.CompanyId=B.CompanyId  WHERE   B.GoodsId=$GoodsId AND B.cSign=7",$link_id);
while($CheckMainRow = mysql_fetch_array($CheckMainResult)){
    if($mCompanyId==""){
	   $mCompanyId=$CheckMainRow["CompanyId"];
	   $mCompanyName=$CheckMainRow["Forshort"];
	 }else{
	   $mCompanyId=$mCompanyId."@".$CheckMainRow["CompanyId"];
	   $mCompanyName=$mCompanyName."@".$CheckMainRow["Forshort"];	 
	 }
}


$ptCompanyId="";
$ptCompanyName="";
$CheckptResult=mysql_query("SELECT   B.CompanyId,T.Forshort  FROM  $DataPublic.nonbom4_bomcompany  B  
LEFT JOIN $DataOut.providerdata  T ON T.CompanyId=B.CompanyId  WHERE   B.GoodsId=$GoodsId AND B.cSign=3",$link_id);
while($CheckptRow = mysql_fetch_array($CheckptResult)){
    if($ptCompanyId==""){
	   $ptCompanyId=$CheckptRow["CompanyId"];
	   $ptCompanyName=$CheckptRow["Forshort"];
	 }else{
	   $ptCompanyId=$ptCompanyId."@".$CheckptRow["CompanyId"];
	   $ptCompanyName=$ptCompanyName."@".$CheckptRow["Forshort"];	 
	 }
}
$ActionId="BomCompany";	
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,GoodsId,$GoodsId,ActionId,$ActionId";
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 
    <input name="Mid" id="Mid" type="hidden" value="<?php  echo $Id?>">
    <table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="20" class="A0010">&nbsp;</td>
			<td height="25"    width="220" align="right">非BOM配件资料:</td>
            <td><span class="redB"><?php  echo $GoodsName?></span></td>
			<td width="20" class="A0001">&nbsp;</td>
		</tr>       

  	<tr>
			<td width="20" class="A0010">&nbsp;</td>
			<td  align="right" height="40">关联包装系统的BOM供应商:</td>
            <td>
              <!--<select name="mainCompanyId" id="mainCompanyId" style="width: 380px;" >
			<option value=''>请选择</option>
            <?php 
			$checkSql = "SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign=$Login_cSign OR cSign=0) AND Estate='1' 
            AND  ObjectSign IN (1,3) order by Letter";			
			$checkResult = mysql_query($checkSql); 
			while ( $checkRow = mysql_fetch_array($checkResult)){
				$themainCompanyId=$checkRow["CompanyId"];
				$themainForshort=$checkRow["Letter"].'-'.$checkRow["Forshort"];
				
			      if($mCompanyId==$themainCompanyId)	echo "<option value='$themainCompanyId' selected>$themainForshort</option>";
                  else  echo "<option value='$themainCompanyId'>$themainForshort</option>";
				} 
			?>
            </select>-->
            <textarea   id="mainCompanyName" name="mainCompanyName" onclick="updateJq(this,1)" style="width:300px"><?php echo $mCompanyName;?></textarea><input  type="hidden" id="mainCompanyId" name="mainCompanyId" value="<?php echo $mCompanyId;?>">
            </td>
			<td width="20" class="A0001">&nbsp;</td>
		</tr>       


  	<tr>
			<td width="20" class="A0010">&nbsp;</td>
			<td  align="right" height="40">关联皮套系统的BOM供应商:</td>
            <td>
            <!--<select name="ptsubCompanyId" id="ptsubCompanyId" style="width: 380px;" >
			<option value=''>请选择</option>
            <?php 
			$checkptsubSql = "SELECT CompanyId,Forshort,Letter FROM $DataOut.providerdata WHERE 1 AND Estate='1' order by Letter";			
			$checkptsubResult = mysql_query($checkptsubSql); 
			while ( $checkptsubRow = mysql_fetch_array($checkptsubResult)){
				$theptsubCompanyId=$checkptsubRow["CompanyId"];
				$theptsubForshort=$checkptsubRow["Letter"].'-'.$checkptsubRow["Forshort"];
			      if($ptCompanyId==$theptsubCompanyId)	echo "<option value='$theptsubCompanyId' selected>$theptsubForshort</option>";
                  else  echo "<option value='$theptsubCompanyId' >$theptsubForshort</option>";
				} 
			?>
            </select>-->
            <textarea   id="ptsubCompanyName" name="ptsubCompanyName" onclick="updateJq(this,2)" style="width:300px"><?php echo $ptCompanyName;?></textarea><input  type="hidden" id="ptsubCompanyId" name="ptsubCompanyId" value="<?php echo $ptCompanyId;?>">
            </td>
			<td width="20" class="A0001">&nbsp;</td>
		</tr>       
      
	</table>
	<input name="hfield" type="hidden" id="hfield" value="0">
<input name="SubName0" id="SubName0" type="hidden" value="">
    
<?php 
echo"<div id='Jp' style='position:absolute;width:400px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div><div  class='closeinco'><img src='../images/closeinco.png' onclick='CloseDiv()'></div>
	</div>";
include "../model/subprogram/add_model_b.php";
?>

<script language = "JavaScript"> 
function updateJq(e,toObj){
	var InfoSTR="";
	var buttonSTR="";
	var runningNum="";
	
	var theDiv=document.getElementById("Jp");
	var infoShow=document.getElementById("infoShow");
	
	var ObjId=document.form1.ObjId.value;
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId ){
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	
				<?PHP 
				$echoInfo="<table border='0' cellspacing='0' bgcolor='#FFFFFF'>";
				$rows=0;
				$companyResult = mysql_query("SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE  Estate=1 AND objectSign IN (1,3) ORDER BY Letter ASC",$link_id);
		        if($companyRow = mysql_fetch_array($companyResult)){
				do{
				    	
				   if($rows%8==0){
					      $echoInfo.="<tr><td><input type='checkbox' name='mainCompanyCheckId[]'  id='mainCompanyCheckId' value='$companyRow[CompanyId]|$companyRow[Forshort]'>&nbsp;&nbsp;$companyRow[Letter]-$companyRow[Forshort]</td>";
					}else{
						  $echoInfo.="<td><input type='checkbox' name='mainCompanyCheckId[]'  id='mainCompanyCheckId' value='$companyRow[CompanyId]|$companyRow[Forshort]'>&nbsp;&nbsp;$companyRow[Letter]-$companyRow[Forshort]</td>";
					}
				    $rows++;
					if($rows%8==0)$echoInfo.="</tr>";
				  } while($companyRow = mysql_fetch_array($companyResult));
			    }
			    $echoInfo.="</table>";
				?>
				  infoShow.style.width=920;
				  infoShow.style.height=<?php echo $rows; ?>*26/8+50;
				  InfoSTR="<?php echo $echoInfo; ?>";
				break;
				
			case 2:	
				<?PHP 
				$echoInfo="<table border='0' cellspacing='0' bgcolor='#FFFFFF'>";
				$rows=0;
				  $companyResult = mysql_query("SELECT CompanyId,Forshort,Letter  FROM $DataOut.providerdata WHERE  Estate=1 ORDER BY Letter",$link_id);
		          if($companyRow = mysql_fetch_array($companyResult)){
				  do{
				   if($rows%8==0){
					      $echoInfo.="<tr><td><input type='checkbox' name='ptsubCompanyCheckId[]'  id='ptsubCompanyCheckId' value='$companyRow[CompanyId]|$companyRow[Forshort]'>&nbsp;&nbsp;$companyRow[Letter]-$companyRow[Forshort]</td>";
					}else{
						  $echoInfo.="<td><input type='checkbox' name='ptsubCompanyCheckId[]'  id='ptsubCompanyCheckId' value='$companyRow[CompanyId]|$companyRow[Forshort]'>&nbsp;&nbsp;$companyRow[Letter]-$companyRow[Forshort]</td>";
					}
				    $rows++;
					if($rows%8==0)$echoInfo.="</tr>";
				  } while($companyRow = mysql_fetch_array($companyResult));
			    }
			    $echoInfo.="</table>";
				?>
				  infoShow.style.width=880;
				  infoShow.style.height=<?php echo $rows; ?>*26/8+50;
				  InfoSTR="<?php echo $echoInfo; ?>";

				break;
				
			}

		  var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确  定' onclick=' setValue("+toObj+")'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取  消' onclick='CloseDiv()'>";

		  infoShow.innerHTML=InfoSTR+buttonSTR;
		  theDiv.style.top=event.clientY -5+'px';
	      theDiv.style.left= document.body.scrollLeft+500+'px';
		  theDiv.style.visibility = "";
		  theDiv.style.display="";

  		 switch(toObj){
			case 1:	
              var mainCompanyId=document.getElementById("mainCompanyId").value;
              var mainCompanyArray=mainCompanyId.split("@");
	          var mainCompanyCheckId=document.getElementsByName("mainCompanyCheckId[]");
              for(n=0;n<mainCompanyCheckId.length;n++){
                    for(k=0;k<mainCompanyArray.length;k++){
                            var TempIdArray=mainCompanyCheckId[n].value.split("|");
                                  if(TempIdArray[0]==mainCompanyArray[k])mainCompanyCheckId[n].checked=true;
                        }
              }
              break;
			case 2:	
              var ptsubCompanyId=document.getElementById("ptsubCompanyId").value;
              var ptsubCompanyArray=ptsubCompanyId.split("@");
	          var ptsubCompanyCheckId=document.getElementsByName("ptsubCompanyCheckId[]");
              for(n=0;n<ptsubCompanyCheckId.length;n++){
                    for(k=0;k<ptsubCompanyArray.length;k++){
                            var TempIdArray=ptsubCompanyCheckId[n].value.split("|");
                                  if(TempIdArray[0]==ptsubCompanyArray[k])ptsubCompanyCheckId[n].checked=true;
                        }
              }
              break;
           }
	  }
}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	//theDiv.className="moveLtoR";
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	}
	
function setValue(toObj){
    switch(toObj){
	    case 1:
	       var returnId="";
           var returnName="";
	       var mainCompanyCheckId=document.getElementsByName("mainCompanyCheckId[]");
	        for(var j=0;j<mainCompanyCheckId.length;j++){
	            if (mainCompanyCheckId[j].checked){
                      var tempArray=mainCompanyCheckId[j].value.split("|");
		              returnId+=returnId==""?tempArray[0]:"@"+tempArray[0];
		              returnName+=returnName==""?tempArray[1]:"@"+tempArray[1];
	            }
	        }
	       document.getElementById("mainCompanyId").value=returnId;
	       document.getElementById("mainCompanyName").value=returnName;
	       break;
	    case 2:
	       var returnId="";
           var returnName="";
	       var ptsubCompanyCheckId=document.getElementsByName("ptsubCompanyCheckId[]");
	        for(var j=0;j<ptsubCompanyCheckId.length;j++){
	            if (ptsubCompanyCheckId[j].checked){
                      var tempArray=ptsubCompanyCheckId[j].value.split("|");
		              returnId+=returnId==""?tempArray[0]:"@"+tempArray[0];
		              returnName+=returnName==""?tempArray[1]:"@"+tempArray[1];
	            }
	        }
	       document.getElementById("ptsubCompanyId").value=returnId;
	       document.getElementById("ptsubCompanyName").value=returnName;
	       break;
    }
     CloseDiv();
}
</script>
