<?php   
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";
$ColsNumber=10;$tableMenuS=500;
ChangeWtitle("Product List");
//序号，产品代码，描述，单价，装箱数量，外箱尺寸，重量，净重
$Th_Col="Choose|50|No.|40|Product Code|150|Description|350|Unit/Carton|70|Carton Size(CM)|110|NW(KG)|50|GW(KG)|50|Width|50|Longth|50|Height|50";
$myCompanyId = 1066;
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$ChooseOut="N";
$nowWebPage=$funFrom."_read";
 $toExcelStr="<input type='button' value='toExcel' id='toExcelcel' onclick='celtoExcel()'>";
include "../model/subprogram/read_model_3.php";
echo $toExcelStr."&nbsp;&nbsp;";
include "../admin/subprogram/read_model_5.php";

$Keys=31;
$i=1;$j=1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT P.Id,P.ProductId,P.eCode,P.Description,P.Price,P.Weight,P.productsize,G.Relation,S.Spec
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.pands G ON. G.ProductId=P.ProductId
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
	WHERE 1 AND S.TypeId='9040' AND P.Estate=1 AND P.CompanyId='1066' ORDER BY P.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d3=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$Weight=$myRow["Weight"];
		$productsize=$myRow["productsize"]==""?"&nbsp;":$myRow["productsize"];
		$Relation=explode("/",$myRow["Relation"]); 
		$Boxs=$Relation[1];
		$Spec=$myRow["Spec"];
		$NW=($Boxs*$Weight)/1000;
		if($NW>0){
			$GW=$NW+1;
		}
		else{
			$NW="&nbsp;";$GW="&nbsp;";
			}
		if($productsize!=''){
		    $productsize = str_replace("×", "x", $productsize); 
		    $productsize = str_replace("cm", "", $productsize); 
			$sizeArray = 	explode("x",$productsize); 
			$Width = $sizeArray[0];
			$Longth = $sizeArray[1];
			$Height = $sizeArray[2]==""?"&nbsp;":$sizeArray[2];
		}else{
			$Width ="&nbsp;"; $Longth ="&nbsp;"; $Height ="&nbsp;";
		}
			$ChooseOut="";
			$ValueArray=array(
				array(0=>$eCode,			3=>"..."),
				array(0=>$Description),
				array(0=>$Boxs,			1=>"align='center'"),
				array(0=>$Spec,			1=>"align='center'"),
				array(0=>$NW,			1=>"align='center'"),
				array(0=>$GW,			1=>"align='center'"),
				//array(0=>$productsize,			1=>"align='center'"),
				array(0=>$Width,			1=>"align='center'"),
				array(0=>$Longth,			1=>"align='center'"),
				array(0=>$Height,			1=>"align='center'")
				
				);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
		 echo "<input type='hidden' id='IdCount' name='IdCount' value='$i'>";
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
include "../model/subprogram/read_model_menu.php";
?>
<script>
//**********************************************导出ExCEL
function celtoExcel(){
    var k=0;
    var message="",chooseStr,allStr;
   var index=document.getElementById("IdCount").value;
        for (var i=1;i<index;i++){
	          var checkid="checkid"+i;
	           var checkStr=document.getElementById(checkid);
				if(checkStr.checked){
                   k++;
                   if(k==1)chooseStr=checkStr.value; 
                   else chooseStr=chooseStr+"^^"+checkStr.value;
                  }
                   if(i==1)allStr=checkStr.value;
                   else allStr=allStr+"^^"+checkStr.value;
			}	  
      if(k==0)message="All to Excel? if not ,please choose!";
      if(message!="" && confirm(message)){
               document.form1.action="productdata_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&tempIds="+allStr;
               document.form1.target="_self";
               document.form1.submit();	
              }
        else{
               if(k!=0){
                       document.form1.action="productdata_toexcel.php?myCompanyId="+<?php echo $myCompanyId?>+"&tempIds="+chooseStr;
                       document.form1.target="_self";
                       document.form1.submit();	
                      }
               }
}

</script>