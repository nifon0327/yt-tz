<?php 
include "../basic/parameter.inc";
$checkResult=mysql_fetch_array(mysql_query("SELECT N.Name AS Buyer,G.GroupName AS DevelopGroup,A.Name AS DevelopName,M.Name AS Position,M.CheckSign  
                            FROM $DataIn.StuffType T 
							LEFT JOIN $DataIn.base_mposition M ON M.Id=T.Position 
							LEFT JOIN $DataIn.staffgroup G ON G.Id=T.DevelopGroupId 
							LEFT JOIN $DataPublic.staffmain A ON A.Number=T.DevelopNumber  
							LEFT JOIN $DataPublic.staffmain N ON N.Number=T.BuyerId 
							WHERE T.TypeId='$TypeId' LIMIT 1",$link_id));
							$Buyer=$checkResult["Buyer"];
							$DevelopGroup=$checkResult["DevelopGroup"];
							$DevelopName=$checkResult["DevelopName"];
							$SendFloor=$checkResult["Position"];
							$CheckSign=$checkResult["CheckSign"];
							switch($CheckSign){
								case 1: $CheckSign="全 检";break;
								case 99: $CheckSign="-----";break;
								default:$CheckSign="抽 检";break;
							}
echo "$Buyer|$DevelopGroup-" . $DevelopName . "（系统默认）|$SendFloor [$CheckSign]";							
?>