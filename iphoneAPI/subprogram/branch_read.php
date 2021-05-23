<?php 
//读取部门数据
	include "../../basic/parameter.inc";
        
                $postInfo = $_POST["info"];
                $mySql="SELECT A.Id,A.Name  FROM $DataPublic.branchdata A WHERE A.Estate=1 ORDER BY A.Id";
	$jsonArray = array();
	$myResult = mysql_query($mySql);
	if($myRow = mysql_fetch_assoc($myResult))
	{
		do 
		{	
		    $Id=$myRow["Id"];
		    $Name = $myRow["Name"];		
			
		$jsonArray[] = array( "$Id","$Name","1");
		}
		while($myRow = mysql_fetch_assoc($myResult));
                //来自通讯录添加主管组别
                if ($postInfo=="Book"){
                    $managerSql="SELECT DISTINCT B.Manager  FROM $DataIn.branchmanager B  ORDER BY B.Id";
                    $managerResult = mysql_query($managerSql);
                    
	   if($managerRow = mysql_fetch_assoc($managerResult))
	     {
                        $ManagerStr="";
		do 
		{	
		    $Manager = $managerRow["Manager"];		
		    $ManagerStr.=$ManagerStr==""?$Manager:",$Manager";
		}
		while($managerRow = mysql_fetch_assoc($managerResult));
                                $jsonArray[] = array( "0","部门主管组","$ManagerStr");
                       }
                }
                echo json_encode($jsonArray);
	}
?>