<?php 
//EWEN 2012-12-16
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=600;
$sumCols="5";		//求和列
ChangeWtitle("$SubCompany 总务申购清单");
$funFrom="zwwp_purchases";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|申购日期|75|申购人|50|申购物品名称|200|分类|80|数量|50|单位|50|申购说明|300|申购状态|60|推荐供应商|100";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="";
if($Login_P_Number==10002){
	$ActioToS="1,2,3,4,7,8,52";
	}
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT DATE_FORMAT(Date,'%Y-%m') AS Month FROM $DataIn.zwwp4_purchase WHERE 1 group by DATE_FORMAT(Date,'%Y-%m') order by Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" and DATE_FORMAT(A.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
      
	$checkType =mysql_query("SELECT Id,TypeName FROM $DataPublic.zwwp2_subtype  WHERE Estate=1",$link_id);
 	if($checkTypeRow = mysql_fetch_array($checkType)){
   		$typestr="<select name='GoodsType' id='GoodsType'  onchange='ResetPage(this.name)'>";
   		$typestr.="<option value='' selected>物品类别</option>";
   		do{
        	$TypeId=$checkTypeRow["Id"];
			$TypeName=$checkTypeRow["TypeName"];
        	if ($TypeId==$GoodsType){
	   			$typestr.="<option value='$TypeId' selected>$TypeName</option>";
           		$SearchRows.=" and C.TypeId='$TypeId'";
        		}
			else{
           		$typestr.="<option value='$TypeId'>$TypeName</option>"; 
        		}
     		}while ( $checkTypeRow = mysql_fetch_array($checkType));
    	$typestr.="</select>";
     	echo $typestr;
		} 
	$selState="selState" . $Estate;
   	$$selState="selected"; 
   	echo "<select name='Estate' id='Estate'  onchange='ResetPage(this.name)'>";
   	echo "<option value='' $selState>状态</option>";
   	echo "<option value='1' $selState1>待申购</option>";
   	echo "<option value='2' $selState2>待审核</option>";
   	echo "<option value='3' $selState3>待采购</option>";
   	echo "<option value='0' $selState0>已购回</option></select>";
   	if ($Estate!="") $SearchRows.="  AND A.Estate='$Estate'";
 	}

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select> <span class='redB'>功能更新中，暂停使用</span>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理B.Unit,
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodsId,A.Qty,A.Remark,A.Date,A.Estate,A.Locks,A.Purchaser,B.GoodsName,B.Attached,B.Unit,B.Id AS TId,C.TypeName,D.Forshort,D.Linkman,D.Tel  
	FROM $DataIn.zwwp4_purchase A 
	LEFT JOIN $DataPublic.zwwp3_data B ON B.Id=A.GoodsId 
	LEFT JOIN $DataPublic.zwwp2_subtype C ON C.Id=B.TypeId
	LEFT JOIN $DataPublic.zwwp0_retailer D ON D.CompanyId=B.CompanyId
	WHERE 1 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Purchaser"];
		include "../model/subprogram/staffname.php";
		$GoodsName=$myRow["GoodsName"];
		$TypeName=$myRow["TypeName"];
		$Qty=$myRow["Qty"];
		$Unit=$myRow["Unit"]==""?"<span class='redB'>未设置</span>":$myRow["Unit"];
		$Remark=$myRow["Remark"];
		$LockRemark="";
		
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached="Z".$myRow["TId"].".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];
		switch($Estate){
			case 0:$Estate="<div class='greenB'>已购回</div>";$LockRemark="物品已购回，锁定记录";break;
			case 1:$Estate="<div class='redB'>待申购</div>";break;
			case 2:$Estate="<div class='yellowB'>待审核</div>";$LockRemark="申购审核中，锁定记录";break;
			case 3:$Estate="<div class='yellowB'>待采购</div>";$LockRemark="物品已购回，锁定记录";break;
			}
		$Forshort=$myRow["Forshort"];
        if($Forshort!=""){
        	$Forshort="<span title='联系人:" . $myRow["Linkman"] . "&#10 联系电话:" . $myRow["Tel"] . "'>$Forshort</span>";
            }
		else{
        	$Forshort="<span class='redB'>未设置</sapn>";
            }
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$TypeName),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate, 1=>"align='center'"),
            array(0=>$Forshort),
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
<script type="text/javascript">
   function gotoPage(Flag){
       if (Flag==1){
         document.form1.action="zw_purchases_read.php";
	 document.form1.submit();
       }
       else{
         document.form1.action="zw_purchasesqk_read.php";
	 document.form1.submit();   
       } 
   }
</script>