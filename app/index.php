<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<link rel="stylesheet" href="app.css" />
		<title>APP应用下载</title>
	</head>
	<body>
		<div id = "app_body">
			<div class="app_title"></div>
			<table id="app_table">
			<?php   
			    include "../basic/chksession.php";
				include "../basic/parameter.inc";
				$appSql = "select * from $DataPublic.app_sheet where estate=1";
				$appResult = mysql_query($appSql);
				$appCount = mysql_num_rows($appResult);
				$rows = ($appCount%2 == 0)?$appCount/2:($appCount+1)/2;
				$num = 1;
				for($i=0;$i<$rows;$i++){
					$cloumColor =  ($i % 2 != 0)?"singleColor":"doubleColor";
					echo "<tr class = '$cloumColor'>";
					
					for($j=0;$j<2;$j++){
						echo "<td>";
						$appRows = mysql_fetch_assoc($appResult);
						$name = $appRows["name"];
						$Id=$appRows["id"];
						$dscribe = $appRows["dscribe"];
						$link = $appRows["link"];
						$provision = $appRows["provision"];
						$icon = $appRows["icon"];
						if($name != ""){
						   echo "<a href=\"outdownload_add.php?Id=$Id\" style='font-size:13px;position:relative;float:right;'>外部下载链接</a>";
							echo "<div class ='clear' onclick=\"location.href='itms-services://?action=download-manifest&url=$link';\">
							<div class = 'floatLeft numDiv'>$num</div>
									  	  <div class = 'imageDiv floatLeft'>
									  	  	<img class = 'icon' src='$icon' />
								  		  </div>
								      	<div class = 'floatLeft textDiv'>
								     		<ul>
												<li class = 'nameli'>$name
												<a  hidden = 'true' href='$provision'>Provisioning File</a>
												</li>
												<li class = 'dscli'>$dscribe</li>
							    			</ul>
							    		</div>
							  		</div>";
							echo "</td>";
							$num++;
						}
					}
					echo "</tr>";
				}
			?>
			</table>
		</div>
	</body>
</html>