<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=14;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 海关编码列表");
$funFrom="ch_customscode";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|客户|80|产品类型|120|产品ID|60|中文名|320|eCode|150|海关编码|120|商品名称|120|材质|80|用途|80|品牌授权书|70|备注|60|状态|50|更新日期|80|操作人|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8,13";

//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";	
	$Clientresult = mysql_query("SELECT C.CompanyId,C.Forshort 
	FROM $DataIn.customscode H
	LEFT JOIN $DataIn.productdata P ON P.ProductId = H.ProductId 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	WHERE  C.Estate=1 GROUP BY  C.CompanyId",$link_id);
	if($ClientRow = mysql_fetch_array($Clientresult)){
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	echo "<option value=''>全 部</option>";
		do{
			$theCompanyId=$ClientRow["CompanyId"];
			$Forshort=$ClientRow["Forshort"];
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$Forshort</option>";
				$SearchRows.=" AND P.CompanyId=".$CompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$Forshort</option>";
				}
			}while($ClientRow = mysql_fetch_array($Clientresult));
		}
	echo"</select>&nbsp;";
	
	//******产品类型				  
    $result = mysql_query("SELECT T.TypeId,T.TypeName 
	           FROM $DataIn.customscode H
	           LEFT JOIN $DataIn.productdata P ON P.ProductId = H.ProductId 
	           LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	           WHERE 1 $SearchRows AND P.Id>0 GROUP BY T.TypeId",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";
	echo "<option value=''>全 部</option>";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["TypeName"];
			if ($TypeId==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND P.TypeId='$theTypeId'";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT H.Id,C.Forshort,T.TypeName,H.HSCode,H.Remark,H.Date,H.Estate,H.Locks,H.Operator,
       P.ProductId,P.cName,P.eCode,P.TestStandard,H.GoodsName,M.Name AS MaterialQ,W.Name AS UseWay
       FROM $DataIn.customscode H
       LEFT JOIN $DataIn.productdata P ON P.ProductId = H.ProductId 
       LEFT JOIN $DataIn.productmq M ON M.Id = P.MaterialQ
       LEFT JOIN $DataIn.productuseway W ON W.Id = P.UseWay
	   LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
       LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	   WHERE 1 $SearchRows ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	  do{
	     $m=1;
		 $Id=$myRow["Id"];
		 $ProductId=$myRow["ProductId"];
		 $cName=$myRow["cName"];
		 $eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		 $GoodsName=$myRow["GoodsName"]==""?"&nbsp;":$myRow["GoodsName"];
		 $MaterialQ=$myRow["MaterialQ"]==""?"&nbsp;":$myRow["MaterialQ"];
		 $UseWay=$myRow["UseWay"]==""?"&nbsp;":$myRow["UseWay"];
		 include "../model/subprogram/product_clientproxy.php";//客户授权书
		 $TestStandard=$myRow["TestStandard"];
		 include "../admin/Productimage/getProductImage.php";
		 $TypeName=$myRow["TypeName"];
		 $Forshort=$myRow["Forshort"];
		 $HSCode=$myRow["HSCode"];
		 $Remark=$myRow["Remark"];
		 $Remark=$Remark==""?"&nbsp;":"<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		 $Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		 $Date=$myRow["Date"];
		 $Locks=$myRow["Locks"];
		 $Operator=$myRow["Operator"];
		 include "../model/subprogram/staffname.php";
		
		 $ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$ProductId,1=>"align='center'"),
			array(0=>$TestStandard,1=>"align='left'"),
			array(0=>$eCode,1=>"align='left'"),
			array(0=>$HSCode,1=>"align='center'"),
			array(0=>$GoodsName,1=>"align='center'"),
			array(0=>$MaterialQ,1=>"align='center'"),
			array(0=>$UseWay,1=>"align='center'"),
			array(0=>$clientproxy,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
			
		   $checkidValue=$Id;
	      include "../model/subprogram/read_model_6.php";
	 	}while ($myRow = mysql_fetch_array($myResult));
		
	 }
else{
	  noRowInfo($tableWidth);
  	}

//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>