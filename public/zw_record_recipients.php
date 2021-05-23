<?php 
//已更新
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0c.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新固定资产领用人资料");//需处理
$nowWebPage =$funFrom."_recipients";	
$toWebPage  =$funFrom."_updated";	
$_session['nowWebPage']=$nowWebPage ; 

//步骤3：
$tableWidth=600;$tableMenuS=300;
//$CustomFun="<span onClick='ViewStuffId(7)' $onClickCSS>新的使用者</span>&nbsp;";//自定义功能
//$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$cSigntmp=$cSign_XY==""?"7":$cSign_XY; //可以把$cSign放在登录时就加载,这样就可实现通用,为某天合并打好条件cSign=7 为研砼，5 为鼠宝， 3 为皮套

$upResult = mysql_query("SELECT * FROM  $DataPublic.fixed_assetsdata WHERE Id='$Id'",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
	$CpName=$upData["CpName"];
	$Model=$upData["Model"];
	$tempCM=$Model."(".$CpName.")";
	}
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Id,$Id,ActionId,$ActionId";
//<td height="22" width='' align="right"><span class="redB">本页操作请谨慎</span></td>
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 
    <input name="Mid" id="Mid" type="hidden" value="<?php  echo $Id?>">
    <table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2"   valign="bottom"><span class="redB">◆固定资产资料:<?php  echo $tempCM?></span></td>
			
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">新的领用人：</td>
            <td scope="col">
             <input name="UserName" type="text" id="UserName" style="width:200px;"  dataType="Require"   msg="未填写">
                <input name='User' type='hidden' id='User' >
               
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
    
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">日期：</td>
            <td scope="col">
  
               <input name="UserDate" type="text" id="UserDate" style="width:200px;" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>  
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>   
        
 		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">备注：</td>
            <td scope="col">
               <textarea name="Remark" cols="20" rows="6" id="Remark" style="width:200px;"></textarea>
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>              

		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2" valign="bottom"><span class="redB">◆历史领用人明细</span></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
	</table>
	<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
         <tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A1111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
                    
                <table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
                  <tr>  
                    <td width="30" class="A0001" align="center">操作</td>
                    <td width="30" class="A0001" align="center">序号</td>
                    <td width="80" class="A0001" align="center">领用人</td>
                    <td width="90" class="A0001" align="center">日期</td>
                    <td width="90" class="A0001" align="center">公司</td>
                     <td width="130" class="A0001" align="center">备注</td>
                    <td width="" class="A0000" align="center">操作者</td>
                  </tr>         
                </table>
            </div>		
            </td>
            <td width="10" class="A0001">&nbsp;</td>
        </tr>               
		
		<tr>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A0010">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='StuffList'>
		<?php 
		//子分类列表
		$StockResult = mysql_query("SELECT S.Name,F.cSign,C.CShortName,F.User,F.SDate as Date,F.Remark,O.Name as NOperator,F.Operator FROM $DataPublic.fixed_userdata F
										left join $DataPublic.staffmain S ON S.Number=F.User
										left join $DataPublic.staffmain O ON O.Number=F.Operator
										left join $DataPublic.companys_group C ON C.cSign=F.cSign
										where F.Mid='$Id' AND F.UserType=1 order by F.SDate ",$link_id);		
		if($StockRows = mysql_fetch_array($StockResult)){
			$i=1;
			do{		
				$SubId=$StockRows["Id"];
				$SubName=$StockRows["Name"];
				$NOperator=$StockRows["NOperator"];
				$SubCShortName=$StockRows["CShortName"];
				$SubNameondblclick="ondblclick=UpdateRow(this,StuffList,'SubName')";  //
				$Date=$StockRows["Date"];
				$Remark=$StockRows["Remark"]==""?"&nbsp;":$StockRows["Remark"];
				//$Estate=$StockRows["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
				
				$SubcSign=$StockRows["cSign"];
				//echo "$SubUser==$cSigntmp";
				if ($SubcSign==$cSigntmp){  //本公司的记录
					$Operator=$StockRows["Operator"];
					include "../model/subprogram/staffname.php";
				//$Locks=$StockRows["Locks"];	
				}
				else{
					$SubName="--";
					$Operator="--";  //在另一家公司的记录,当然，亦可远程获取，有需要再做
				}
				//$Locks=$StockRows["Locks"];	
				echo"<tr><td width='30' class='A0101' align='center'>";
				//echo"<a href='#' onclick='deleteRow(this.parentNode,StuffList)' title='删除领用人'>×</a>";
				echo "&nbsp;";  //删除的就暂时不添加，看需要时再说
		
				echo"<td width='30' class='A0101' align='center'>$i</td>";
				echo"
					<td width='80' class='A0101' align='left'>$SubName</td>
					<td width='90' class='A0101' align='center'>$Date</td>
					<td width='90' class='A0101' align='center'>$SubCShortName</td>					
					<td width='130' class='A0101' align='left'>$Remark</td>
					<td width='' class='A0100'>$Operator</td>
					</tr>";
				$i++;
				}while($StockRows = mysql_fetch_array($StockResult));
			}
		?>
			</table>
		</div>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
	</table>
	<input name="hfield" type="hidden" id="hfield" value="0">
<input name="SubName0" id="SubName0" type="hidden" value="">  
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
  $StaffSql = mysql_query("SELECT M.Id,M.Number,M.Name, B.Name AS Branch
	FROM $DataPublic.staffmain M 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId	
	WHERE 1 AND M.Estate='1'   ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number",$link_id);

	while ($StaffRow = mysql_fetch_array($StaffSql)){
		$sNumber=$StaffRow["Number"];
         $sName=$StaffRow["Name"];
		$sBranch=$StaffRow["Branch"];
        $subName[]=array($sNumber,$sName,$sBranch);
	};
?>
<script LANGUAGE='JavaScript'  type="text/JavaScript">
    
 window.onload = function(){
                var subName=<?php  echo json_encode($subName);?>;
                
		var sinaSuggest = new InputSuggest({
		        input: document.getElementById('UserName'),
			poseinput: document.getElementById('User'),
			data: subName,
			width: 200
		});
				
	}
	
function CheckForm(){
	//passvalue("SubName");  //add by zx 2011-05-05 必须与上面隐藏传递元素id0号一致,Pid0
	document.form1.action="fixed_assets_updated.php"+"&ActionId=AddUser";
	alert();
	//document.form1.submit();
}

</script>
