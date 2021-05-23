<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="4";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 我的行政费用列表");
$funFrom="adminicost";
$Th_Col="选项|40|序号|40|所属公司|60|请款日期|70|金额|70|货币|40|说明|400|分类|100|票据|45|状态|45|款项是否<br>收回|80|收回类型|60|审核退回原因|300|内部单号|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,14,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.hzqksheet S WHERE 1 and S.Operator=$Login_P_Number $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	$SearchRows.=$Estate==""?"":" and S.Estate=$Estate";
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				echo "<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo "<option value='$dateValue'>$dateText</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'";
			}
		echo"</select>&nbsp;";
		}
		$SearchRows.=$PEADate;
	//月份
	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,S.cSign,
T.Name AS Type,C.Symbol AS Currency,S.BillNumber,
S.OtherId,O.Estate AS OtherEstate,O.getmoneyNO,S.Property,M.InvoiceNO,M.cwSign,M.InvoiceFile
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
    LEFT JOIN $DataIn.cw4_otherinsheet O ON O.Id=S.OtherId AND S.Property=1
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.OtherId AND S.Property=2
	WHERE 1 AND S.Operator=$Login_P_Number $SearchRows order by S.Date DESC";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];		
		$Content=$myRow["Content"];
		$Type=$myRow["Type"];
		$BillNumber=$myRow["BillNumber"];
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$myRow["ReturnReasons"]."</span>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/cwadminicost/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="H".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Locks=$myRow["Locks"];			
		$Estate=$myRow["Estate"];		
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$checkPay= mysql_fetch_array(mysql_query("SELECT PayDate FROM $DataIn.hzqkmain WHERE Id='$Mid' LIMIT 1",$link_id));
				$PayDate=$checkPay["PayDate"];
				$Estate="<div align='center' class='greenB' title='已结付,结付日期：$PayDate'>√</div>";
				$LockRemark="记录已经结付，强制锁定操作！";
				$Locks=0;
				break;
			}
        $OtherId=$myRow["OtherId"];	
        $OtherEstate=$myRow["OtherEstate"];	
        $getmoneyNO=$myRow["getmoneyNO"];	
        if($OtherId!=0){
                if($OtherEstate==0)$FontColor="style='color:#FF6633'"; 
                else if($OtherEstate==3) $FontColor="style='color:#0000CC'";          
	            $d1=anmaIn("download/otherin/",$SinkOrder,$motherSTR);		
		        $f1=anmaIn($getmoneyNO,$SinkOrder,$motherSTR);
		        $getmoneyNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\" $FontColor>$getmoneyNO</a>";
                $OtherEstate=$getmoneyNO;
              }
        $InvoiceNO=$myRow["InvoiceNO"];	
        $cwSign=$myRow["cwSign"];	
        $InvoiceFile=$myRow["InvoiceFile"];	
         if($cwSign!=""){
				$d2=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
				$f2=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		       
			   if($InvoiceFile==0) {
				   $OtherEstate="&nbsp";
			   }
			   else{
				$dfname=urldecode($InvoiceNO);
			    $OtherEstate=strlen($InvoiceNO)>25?"<a href=\"openorload.php?dfname=$dfname&Type=invoice\" target=\"download\">$InvoiceNO</a>":"<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=7\" target=\"download\" >$InvoiceNO</a>";
			   }
             }
        $Property=$myRow["Property"];	
        switch($Property){
               case "1":
                     $PropertyStr="其他收入";
               break;
               case "2":
                     $PropertyStr="Invoice";
               break;
               case "3":
                     $PropertyStr="薪资";
               break;
              default:
                     $PropertyStr="&nbsp;";
               break;
          }
        $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		//财务强制锁:非未处理皆锁定
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$Amount,   1=>"align='center'"),
			array(0=>$Currency, 1=>"align='center'"),
			array(0=>$Content,	3=>"..."),
			array(0=>$Type,	  3=>"..."),
			array(0=>$Bill,   1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$OtherEstate, 1=>"align='center'"),
			array(0=>$PropertyStr, 1=>"align='center'"),
			array(0=>$ReturnReasons),
			array(0=>$BillNumber,  1=>"align='center'")
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