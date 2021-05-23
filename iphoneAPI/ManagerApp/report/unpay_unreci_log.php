<?
$table = "unpay_unreceive_log";
/*
mysql_select_db($DataIn,$link_id) or die("无法选择数据库!");


$createSql = "create table $table (
id int primary key auto_increment, //主键
remark varchar(200), //备注内容
time varchar(30) not null, //时间2014-11－01 12:22
operator varchar(30) not null, //操作人姓名
comId_y_m varchar(50) not null , //客户id＋年月
pay_reci int not null //未付或者未收
)  DEFAULT CHARSET=utf8 ";

mysql_query($createSql,$link_id);


*/ 

$jsonArray = array();
					$_action = $info[1];
					$pay_reci =  (int) $info[2];
					$comId_y_m = $info[3];
					$time = $info[4];
					$remark = $info[5];
					$operator = $info[6];

$selectRs = mysql_query("select S.id, S.remark, S.time, M.Name as operator from $DataIn.$table S left join $DataPublic.staffmain M  on M.Number=S.operator  where S.comId_y_m = '".$comId_y_m."' and pay_reci=$pay_reci  and S.Estate>=1",$link_id);
if ($selectRs) {
	$rsRow = mysql_fetch_array($selectRs);
	
}

// 查询操作
if ("sel" == $_action) {
	if ($rsRow) {
		$operator = $rsRow["operator"];

		$time = $rsRow["time"];
		$remark = $rsRow["remark"];
	}
	$jsonArray = array("operator"=>$operator,"time"=>$time,"remark"=>$remark,"idValue"=>$rsRow["id"], "action"=>"sel","cmy"=>$comId_y_m);
} else if ("upd" == $_action) { // 更新操作

	if ($rsRow) {
		$operator = $LoginNumber;
		$idValue = $rsRow["id"];
		$updSql = "update $DataIn.$table  set remark='".$remark."', operator='".$operator."',comId_y_m='".$comId_y_m."', time='".$time."' where id=$idValue";
	} else {
		$operator = $LoginNumber;
		$updSql = "insert into $DataIn.$table  (id, remark, operator, comId_y_m, time, pay_reci) VALUES (null,'".$remark."','".$operator."','".$comId_y_m."','".$time."', ".$pay_reci.") ";
	}
	$updRs = @mysql_query($updSql,$link_id); 
	
	$jsonArray = array("action"=>"upd", "success"=>$updRs,"info"=>"");
}


?>