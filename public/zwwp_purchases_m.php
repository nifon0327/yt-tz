<?php 
//ewen 2012-12-16
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=15;
$tableMenuS=600;
$sumCols="5,6";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 总务申购审核");
$funFrom="zwwp_purchases";
$Th_Col="选项|40|序号|40|申购日期|75|申购人|50|申购物品名称|200|分类|80|数量|50|单位|50|申购说明|300|推荐供应商|100|在库|60|可用库存|60|上次申购日期|80|上次价格|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量	
if($Login_P_Number==10002){
	$ActioToS="1,17,15";
	}
//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";
    //$sBranchId=$sBranchId==""?1:$sBranchId;
    $selSignStr="selSign" . $sBranchId;
    $$selSignStr="selected";
	echo"<select name='sBranchId' id='sBranchId' onchange='ResetPage(this.name)'>";
    echo"<option value='' $selSign>全部</option>";
    echo"<option value='1' $selSign1>总务</option>";
    echo"<option value='5' $selSign5>开发</option>";
    echo"<option value='4' $selSign4>采购</option>";
    echo"<option value='99' $selSign99>其它</option>";
    echo"</select>&nbsp;";
    switch($sBranchId){
		case 1:$SearchRows=" AND (E.BranchId=1 OR E.BranchId=2)";break;
		case 4:$SearchRows=" AND E.BranchId=4";break;
		case 5:$SearchRows=" AND E.BranchId=5";break;
		case 99:$SearchRows="  AND E.BranchId NOT IN (1,2,4,5)";break;
		default:$SearchRows="";break;
        }
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select><span class='redB'>功能更新中，暂停使用</span>";
//步骤4：
include "../model/subprogram/read_model_5.php";
//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GoodId,A.Qty,A.Remark,A.Date,A.Estate,A.Locks,B.GoodsName,B.Attached,B.Unit,B.Id AS TId,C.TypeName,D.Forshort,D.Linkman,D.Tel,E.Name AS Purchaser  
	FROM $DataIn.zwwp4_purchase A 
	LEFT JOIN $DataPublic.zwwp3_data B ON B.Id=A.GoodId 
	LEFT JOIN $DataPublic.zwwp2_subtype C ON C.Id=B.TypeId
	LEFT JOIN $DataPublic.zwwp0_retailer D ON D.CompanyId=B.CompanyId
	LEFT JOIN $DataPublic.staffmain E ON A.Purchaser=E.Number 
	WHERE A.Estate=2 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/zwwp/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GoodId=$myRow["GoodId"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Purchaser"];
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
		$Locks=1;
		$Forshort=$myRow["Forshort"];
        if($Forshort!=""){
        	$Forshort="<span title='联系人:" . $myRow["Linkman"] . "&#10 联系电话:" . $myRow["Tel"] . "'>$Forshort</span>";
            }
		else{
        	$Forshort="<span class='redB'>未设置</sapn>";
            }
	    //上次购买记录       
        $PriceResult = mysql_query("SELECT Date FROM $DataIn.zwwp4_purchase WHERE GoodId='$GoodId' ORDER BY Date DESC LIMIT 1",$link_id);
        if($PriceRows = mysql_fetch_array($PriceResult)){
        	$lastDate=$PriceRows["Date"];
            $lastPrice=$PriceRows["Price"];
            }
		else{
        	$lastDate="&nbsp;";
            $lastPrice="&nbsp;"; 
            }
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'"),
			array(0=>$GoodsName),
			array(0=>$TypeName),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Unit,1=>"align='center'"),
			array(0=>$Remark),
            array(0=>$Forshort),
			array(0=>'0',1=>"align='right'"),
			array(0=>'0',1=>"align='right'"),
			array(0=>$lastDate, 1=>"align='center'"),
			array(0=>$lastPrice, 1=>"align='center'")
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