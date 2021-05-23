<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=600;
$sumCols="5";		//求和列
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 行政费用待审核列表");
$funFrom="adminicost";
//$Th_Col="选项|40|序号|40|请款人|50|请款日期|70|金额|70|货币|40|说明|300|分类|120|票据|60|状态|60|审核人|60|上次审核退回原因|300";
$Th_Col="选项|40|序号|40|所属公司|60|请款人|50|请款日期|70|金额|70|货币|40|说明|300|分类|120|票据|60|状态|60|款项是否<br>收回|80|收回类型|60|上次审核退回原因|300";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量

//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
$AuditTypeWidth="100px;";
$cSignTB="T";
$SelectFrom=1;
if($From!="slist"){
	$SearchRows="";
	 include "../model/subselect/admini_audittype.php"; 
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select> $CencalSstr";
//步骤4：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";

//步骤5：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,S.cSign,
T.Name AS Type,C.Symbol AS Currency,
S.OtherId,O.Estate AS OtherEstate,O.getmoneyNO,S.Property,M.InvoiceNO,M.cwSign,M.InvoiceFile
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
    LEFT JOIN $DataIn.cw4_otherinsheet O ON O.Id=S.OtherId AND S.Property=1
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.OtherId AND S.Property=2
	WHERE 1 $SearchRows AND S.Estate=2 ORDER BY S.Date DESC";	
	//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Content=$myRow["Content"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$myRow["ReturnReasons"]."</span>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/cwadminicost/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="H".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"];
		$Type=$myRow["Type"];
		$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
		$Locks=1;
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
		       // $f2=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
				$f2=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		       //$OtherEstate=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
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
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Amount,1=>"align='center'"),
			array(0=>$Currency,1=>"align='center'"),
			array(0=>$Content,3=>"..."),
			array(0=>$Type),
			array(0=>$Bill,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$OtherEstate, 1=>"align='center'"),
			array(0=>$PropertyStr, 1=>"align='center'"),
			array(0=>$ReturnReasons)
			);
		//array(0=>$AuditStaff,1=>"align='center'"),
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
$ActioToS="1,17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过
include "../model/subprogram/read_model_menu.php";
?>