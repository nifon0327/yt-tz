<?php 
/*
$DataIn.zw3_purchases
$DataIn.zw3_purchaset
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=15;
$tableMenuS=600;
$sumCols="8";		//求和列
ChangeWtitle("$SubCompany 总务申购请款列表");
$funFrom="zw_purchasesqk";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|申购物品名称|100|图片|40|数量|50|单位|50|历史单价|60|单价|50|总额|50|使用地点|50|采购说明|260|请款状态|60|供应商|80|申购日期|75|申购人|50|采购|50|凭证|40";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,3,14,15";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
 echo"<select name='chooseType' id='chooseType' onchange='gotoPage(this.value)'>";
     if ($funFrom=="zw_purchases"){
         echo"<option value='1' selected>申购</option>";
         echo"<option value='2'>请款</option>";
         }
     else{
         echo"<option value='1'>申购</option>";
         echo"<option value='2' selected>请款</option>";
     }
   echo"</select>&nbsp;&nbsp;";
//必选,过滤条件
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT DATE_FORMAT(qkDate,'%Y-%m') AS Month FROM $DataIn.zw3_purchases WHERE 1 group by DATE_FORMAT(qkDate,'%Y-%m') order by qkDate DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;
				}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
        
   $selState="selState" . $cgEstate;
   $$selState="selected"; 
   echo "<select name='cgEstate' id='cgEstate'  onchange='ResetPage(this.name)'>";
   echo "<option value='' $selState>请款状态</option>";
   echo "<option value='1' $selState1>未请款</option>";
   echo "<option value='2' $selState2>请款中</option>";
   echo "<option value='3' $selState3>未结付</option>";
   echo "<option value='0' $selState0>已结付</option></select>";
   if ($cgEstate!="") $SearchRows.=" and S.Estate='$cgEstate'";
 }

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Unit,S.Price,S.Qty,S.TypeId,T.TypeName,S.cgSign,S.WorkAdd,S.Remark,S.Estate,M.Name AS Buyer,S.Bill,S.Locks,S.Date,S.Operator,T.Id AS TId,T.Attached,C.cName,C.Linkman,C.Tel
FROM $DataIn.zw3_purchases S 
LEFT JOIN $DataIn.zw3_purchaset T ON T.Id=S.TypeId
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId
LEFT JOIN $DataIn.retailerdata C ON C.Id=S.Cid
WHERE 1 $SearchRows AND cgSign='0' ORDER BY S.Date DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Qty=$myRow["Qty"];
		$Unit=$myRow["Unit"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$TypeId=$myRow["TypeId"];
		$TypeName=$myRow["TypeName"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":trim($myRow["Remark"]);
		$Estate=$myRow["Estate"];
		$LockRemark="";
		switch($Estate){
			case 0:$Estate="<div class='greenB'>已结付</div>";$LockRemark="记录已结付";break;
			case 1:$Estate="<div class='redB'>未请款</div>";break;
			case 2:$Estate="<div class='yellowB'>请款中</div>";$LockRemark="记录请款中";break;
			case 3:$Estate="<div class='yellowB'>未结付</div>";$LockRemark="记录未结付";break;
			}
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Buyer=$myRow["Buyer"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/zwbuy/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="Z".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
		$Locks=$myRow["Locks"];
		$TId=$myRow["TId"];
		$Attached=$myRow["Attached"];
		$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="Z".$TId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		 $WorkAddFrom=$myRow["WorkAdd"];
	    if ($WorkAddFrom>0){
	       include "../model/subselect/WorkAdd.php"; 
	    }
	    else{
		  $WorkAdd="<div class='redB'>未设置</div>";
	    }

				//历史单价,最大值和最小值
		$checkPrice=mysql_query("SELECT MAX(Price) AS maxPrice,MIN(Price) AS minPrice FROM $DataIn.zw3_purchases WHERE 1 AND TypeId='$TypeId' ORDER BY TypeId",$link_id);
		$maxPrice=mysql_result($checkPrice,0,"maxPrice");
		$minPrice=mysql_result($checkPrice,0,"minPrice");
		if($maxPrice==""){
				$PriceInfo="&nbsp;";
				}
			else{
				$PriceInfo="<a href='zw_historyprice.php?TypeId=$TypeId' target='_blank' title='最低历史单价: $minPrice 最高历史单价: $maxPrice'>查看</a>";
				}
				$cName=$myRow["cName"];
                if ($cName!=""){
                    $cName="<span title='联系人:" . $myRow["Linkman"] . "&#10 联系电话:" . $myRow["Tel"] . "'>$cName</span>";
                }
                else{
                    $cName="&nbsp;";
                }
                
        if(floor($Qty)==$Qty) { $Qty=floor($Qty); }
		$ValueArray=array(
			array(0=>$TypeName),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$PriceInfo,1=>"align='center'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$WorkAdd, 1=>"align='center'"),
			array(0=>$Remark, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$cName, 1=>"align='left'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$Buyer, 1=>"align='center'"),
			array(0=>$Bill, 1=>"align='center'"),
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