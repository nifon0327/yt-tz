<?php
	
	include "../model/modelhead.php";
	
	$From=$From==""?"read":$From;
	//需处理参数
	$ColsNumber=12;				
	$tableMenuS=500;
	ChangeWtitle("$SubCompany 上班日期对调");
	$funFrom="kq_newDdSet";
	$nowWebPage=$funFrom."_read";
	$sumCols="";		//求和列
	$Th_Col="选项|40|序号|40|对调前原工作日|200|对调前原工作日时间|200|对调前原休息日|200|对调前原休日时间|200|操作员|80";
	$Pagination=$Pagination==""?1:$Pagination;
	$Page_Size = 200;

	$ActioToS="1,2,3,4";
	
	//步骤3：
	include "../model/subprogram/read_model_3.php";
	//步骤4：需处理-条件选项
	echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
  	//步骤5：
  	include "../model/subprogram/read_model_5.php";
  	//步骤6：需处理数据记录处理
  	$i=1;
  	$j=($Page-1)*$Page_Size+1;
  	List_Title($Th_Col,"1",0);
  	$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
	
	$mySql="SELECT D.Id,D.GDate,D.GTime,D.XDate,D.XTime,D.Operator
			FROM $DataIn.kq_rqddnew D
			WHERE 1 $SearchRows 
			order by D.GDate DESC";
	
	$myResult = mysql_query($mySql);
	if(mysql_num_rows($myResult) > 0)
	{
		while($myRow = mysql_fetch_assoc($myResult))
		{
			$m=1;
			$Id=$myRow["Id"];
			$GDate=$myRow["GDate"];
			$MonthTemp=substr($GDate,0,7);
			$weekTemp1="(星期".$Darray[date("w",strtotime($GDate))].")";
			$GDate.=$weekTemp1;
		
			$GTime = $myRow["GTime"];
			$XDate = $myRow["XDate"];
			if($XDate != "")
			{
				$weekTemp2="(星期".$Darray[date("w",strtotime($XDate))].")";
				$XDate.=$weekTemp2;
			}
			else
			{
				$XDate = "待定";
			}
			
			$XTime = $myRow["XTime"];
			if($XTime == "")
			{
				$XTime = "待定";
			}
		
			$Operator=$myRow["Operator"];
            include "../model/subprogram/staffname.php";
			$ValueArray=array(
				array(0=>$GDate, 		1=>"align='center'"),
				array(0=>$GTime,		1=>"align='center'"),
				array(0=>$XDate,		1=>"align='center'"),
				array(0=>$XTime,		1=>"align='center'"),
				array(0=>$Operator,		1=>"align='center'")
				);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
		}
	}
	else
	{
		noRowInfo($tableWidth);
	}
	
	//步骤7：
echo '</div>';
	List_Title($Th_Col,"0",0);
	$myResult = mysql_query($mySql,$link_id);
	$RecordToTal= mysql_num_rows($myResult);
	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
	include "../model/subprogram/read_model_menu.php";
	
?>