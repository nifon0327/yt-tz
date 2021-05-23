<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?php 
include "../model/characterset.php";
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo "<link rel='stylesheet' href='../model/css/sharing.css'>";
?>
</head>
<style type="text/css">
body{
	/*background: #eee;*/
	/*background: #E0E0E3 url(../images/sidebar.png) repeat;*/
	background-image: url(images/aside-right-shadow.jpg);
	background-repeat: repeat-y;
    background-origin: padding-box;
    /*
	border-right-color: rgb(198, 200, 204);
	border-right-style: solid;
	border-right-width: 1px;
	*/
}

a{
font-size: 13px;
}
a:visited{
color: #000;
}

.demo-list {
    position: relative;
    margin: 0;
	float: left;
	text-align: left;
	width:100%;
}

.demo-list h2 {
	font-weight: normal;
	margin-bottom: 0;
}

.demo-list ul {
	width: 100%;
	/*border-top: 1px solid #ccc;*/
	margin: 0;
	padding: 0;
	float: left;
	list-style:none;
}

.demo-list li {
	border-bottom: 1px solid #ccc;
	padding: 0;
	background: #eee;
	width: 99%;
}

.active{
	background: #fff;
	color: #f00;
}


.demo-list a {
	text-decoration: none;
	display: block;
	font-weight: bold;
	font-size: 13px;
	color: #3f3f3f;
	text-shadow: 1px 1px #fff;
	padding: 4% 2%;
	/*padding-top: 6px;*/
}

.info{
	float: left;
	margin-left: 5px;
	margin-top:100px;
	font-size:12px;
	line-height: 25px;
}
.info b{
	font-size:13px;
}

</style>

<script type="text/javascript">
function liClick(e){
     var lists=document.getElementsByTagName("li");
     for (var i=0;i<lists.length;i++){
	     lists[i].style.backgroundColor="#EEEEEE";
     }
     e.style.backgroundColor="#FFFFFF";
}
</script>
<body>
<div class="demo-list">
     <!--<h2>功能菜单</h2>-->
     <ul>
        <li style='background:#fff;' onclick='liClick(this)'><a href="supplier_start.php"  target="mainFrame">首页</a></li>
     	<?php 
     	  // $Login_Id=56;
     	 
			$rMenuResult = mysql_query("
			SELECT P.ModuleId,P.IsPrice,F.ModuleName,F.AutoName
			FROM $DataIn.sys4_gysfunpower P
			LEFT JOIN $DataIn.sys4_gysfunmodule F ON F.ModuleId=P.ModuleId 
			LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
			WHERE 1 AND P.UserId='$Login_Id' AND F.Estate=1 ORDER BY F.ModuleId
			",$link_id);
			/*
			$rMenuResult = mysql_query("
			SELECT F.ModuleId,F.ModuleName,F.AutoName
			FROM $DataIn.sys4_gysfunmodule F 
			WHERE 1 AND F.Estate=1 ORDER BY F.ModuleId
			",$link_id);//AND F.ModuleId>'100010'  
			*/
			if ($rMenuRow = mysql_fetch_array($rMenuResult)){
				$i=1;
				$S_IsPrice=0;
				//session_register("S_IsPrice"); 	
				$_SESSION["S_IsPrice"] = $S_IsPrice;
				do{
					$AutoName=$rMenuRow["AutoName"];
					$ModuleId=$rMenuRow["ModuleId"];//加密
					$Mid=anmaIn($ModuleId,$SinkOrder,$motherSTR);
					$IsPrice=$rMenuRow["IsPrice"];
					$S_IsPrice=1;
					$IsPrice=anmaIn($IsPrice,$SinkOrder,$motherSTR);
					$ModuleName=$rMenuRow["ModuleName"];
					if($AutoName!=0){
						include "../model/subprogram/mycompany_info.php";
						if($AutoName==1){
							$ModuleName=$S_Forshort.$ModuleName;
							}
						else{
							$ModuleName=$ModuleName.$S_Forshort;
							}
						}
				   
				  $subTitle="";
				   switch($ModuleId){
				        case "100011"://未出
				            $cgQtySql= mysql_fetch_array(mysql_query("SELECT SUM(S.FactualQty+S.AddQty) AS Qty FROM $DataIn.cg1_stocksheet S WHERE 1 and S.Mid>0 AND  S.CompanyId='$myCompanyId' ",$link_id));
						//已出
						/*$rkTemp=mysql_fetch_array(mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id WHERE M.CompanyId='$myCompanyId'",$link_id));
		                  */
		              $rkTemp=mysql_fetch_array(mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
		LEFT JOIN $DataIn.cg1_stocksheet S ON  S.StockId=R.StockId WHERE S.CompanyId='$myCompanyId'",$link_id));
		               //已送未入库
		              $shSql=mysql_fetch_array(mysql_query("SELECT SUM(G.Qty) AS Qty FROM $DataIn.gys_shsheet G
									   LEFT JOIN $DataIn.gys_shmain S ON S.Id=G.Mid
									   WHERE 1 AND G.SendSign=0 AND G.Estate>0 AND S.CompanyId='$myCompanyId'",$link_id)); 
					   
						    $nochQty=$cgQtySql["Qty"]-$rkTemp["Qty"]-$shSql["Qty"];
						    
						    $subTitle=number_format($nochQty) . " pcs";
				            break;
				           case "100013"://退货审核
				           $thSql=mysql_fetch_array(mysql_query("SELECT count(*) AS nums FROM $DataIn.ck2_thmain M 
				           LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid=M.Id
				            WHERE  M.CompanyId='$myCompanyId' AND NOT EXISTS (SELECT R.Mid FROM $DataIn.ck2_threview R WHERE  R.Mid=S.Id )",$link_id));
			                $subTitle=$thSql["nums"]==0?"":$thSql["nums"];
				           break;
				           case "100014"://未结付货款
				           $FKSql=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount) AS Amount  FROM $DataIn.cw1_fkoutsheet S WHERE  S.CompanyId='$myCompanyId' AND S.Estate =3 AND S.Month!=''",$link_id));
			                $FK_Amount=$FKSql["Amount"];
			                $subTitle="¥ " . number_format($FK_Amount,2);
				           break;
				          
				   }
				   $subTitle=$subTitle==""?"":" <span style='color:#f00;float:right;margin-right:10px;'>" . $subTitle . "</span>";
				    echo "<li onclick='liClick(this)'><a href='mainFrame.php?Mid=$Mid&IsPrice=$IsPrice'  target='mainFrame'> $subTitle $ModuleName</a></li>";
					$i++;
					}while ($rMenuRow = mysql_fetch_array($rMenuResult));
					
					$eResult =mysql_fetch_array(mysql_query("SELECT count(*) AS nums FROM (SELECT E.Id FROM $DataIn.errorcasedata E
                        LEFT JOIN $DataIn.casetostuff C ON C.cId=E.Id
						LEFT JOIN $DataIn.stuffprovider S ON S.StuffId=C.StuffId
						LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=S.CompanyId
                        WHERE E.Estate=1 AND E.Type=2  AND L.Id=$Login_P_Number 
						GROUP BY E.Id ) A",$link_id));
                    $eCaseNums=$eResult["nums"]==0?"":"<span style='color:#f00;float:right;margin-right:10px;'>" . $eResult["nums"] . "</span>";

				  echo "<li onclick='liClick(this)'><a href='supplier_errorcase_read.php'  target='mainFrame'>$eCaseNums 检讨报告</a></li>";
				}
			else{
			    echo "<li ><a href='about:'  target='mainFrame'><span style='color:#f00;'>未设可访问项目</span></a></li>";
				}
				?>
        </ul>
     </div>
     
     <div class="info">
         <b>研砼公司联系信息:</b>
		       <?php 
		         /*
               $pResult =  mysql_fetch_array(mysql_query("SELECT M.Name,M.ExtNo,M.Mail,S.Mobile 
                 FROM  $DataIn.linkmandata L 
                 LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=L.CompanyId  
                 LEFT JOIN $DataPublic.staffmain M ON P.Operator=M.Number  
                 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number  
                  WHERE L.Id='$Login_P_Number' ORDER BY L.Id LIMIT 1",$link_id));
                  */
                  $pResult =  mysql_fetch_array(mysql_query("SELECT M.Name,M.ExtNo,M.Mail,S.Mobile 
                 FROM $DataIn.cg1_stockmain G
                 LEFT JOIN $DataPublic.staffmain M ON G.BuyerId=M.Number  
                 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number  
                  WHERE G.CompanyId='$myCompanyId' ORDER BY G.Date DESC LIMIT 1",$link_id));

				 $Name=$pResult["Name"];
				 $ExtNo=$pResult["ExtNo"];
				 $Mail=$pResult["Mail"];
				 $Mobile=$pResult["Mobile"];
				 echo "<br><span>采&nbsp;&nbsp;购:   $Name </span><br>";
				 echo "<span>电&nbsp;&nbsp;话:  0755-61139580-$ExtNo</span><br>";
				 echo "<span>手&nbsp;&nbsp;机:  $Mobile</span><br>";
				 echo "<span>邮&nbsp;&nbsp;箱:  $Mail</span><br>";
			  ?>
			<b>附:<a  href='mc_hzdoc_read.php' target='mainFrame' >公司资料</a></b>
		     </div>
</body>
</html>
