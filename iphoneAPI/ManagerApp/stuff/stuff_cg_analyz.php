<?php 
$typeidSelect = $info[0];
$typeidSelect = $typeidSelect <= 0 ? -1 :$typeidSelect;
	$currentMonth = date("Y-m");
	$monthList = array("$currentMonth"); 
	$len = 6;
	$searchMonth = "";
	$headArr = array();
	$headArr[]= date("m月",strtotime(($currentMonth)));
	for ($i=1; $i < $len ;$i++) {
		$hasS = $i==1 ? "":"s";
		$monthList[]=date("Y-m",strtotime("-$i month$hasS",strtotime($currentMonth)));
		$headArr[]= date("m月",strtotime("-$i month$hasS",strtotime($currentMonth)));
	}
$searchMonth = " and DATE_FORMAT(M.Date,'%Y-%m') in (".implode(",",$monthList).")";
  $sql = mysql_query("select sum(1) as stuff_count,T.TypeName as typename,T.TypeId as typeid 
  from  $DataIn.stuffdata D
  left join $DataIn.stufftype T on T.typeid = D.TypeId 
  where 1 and T.mainType=1 and T.Estate > 0 and D.Estate>0 group by D.TypeId 
  order by stuff_count desc");
  $jsonArray = array();
  
  $allTypeAllMonth = array();
  $i = 0;
  while ($row = mysql_fetch_assoc($sql)) {
	 
	 $stuff_count = $row["stuff_count"];
	 $typename = $row["typename"];
	 $typeid = $row["typeid"];
	 if ($typeidSelect == -1) {
		 $typeidSelect = $typeid;
	 }
	 $i ++;
	 $allMonth = array();
	 
	 for ($i = 0; $i < $len; $i++) {
	 $eachMon = $monthList[$i];
	 $sqlEach = mysql_query("SELECT 
                           SUM(CG.FactualQty + CG.AddQty) AS OrderQty,
						   SUM((CG.FactualQty + CG.AddQty)*CG.Price *C.Rate) as money
                        FROM $DataIn.cg1_stocksheet CG
						left join $DataIn.stuffdata D on D.StuffId=CG.StuffId
						LEFT JOIN $DataIn.cg1_stockmain M ON CG.Mid=M.Id 
left join $DataIn.bps P on P.StuffId=CG.StuffId
LEFT JOIN $DataIn.trade_object   AS R        ON R.companyid=P.companyid
left join $DataIn.currencydata C on C.Id=R.currency

						where D.TypeId=$typeid and CG.Mid>0 and  DATE_FORMAT(M.Date,'%Y-%m')='$eachMon'
						");
						
	 if ($sqlEachRow = mysql_fetch_assoc($sqlEach)) {
		
		 $eachOrderQty = $sqlEachRow["OrderQty"];
		 $eachOrderQty = number_format($eachOrderQty,0);
		 $eachmoney = $sqlEachRow["money"];
		 $eachmoney = number_format($eachmoney,0);
		 $allMonth[] = array("$eachOrderQty","¥"."$eachmoney");
 
	 } else {
		 $allMonth[] = array("0","¥"."0");
	 }
	 }
	$allTypeAllMonth[] = array("Title"=>"$typename","Count"=>"$stuff_count","Type"=>"$typeid",
								  "AllMonth"=>$allMonth);
  }
 
 
  
 
 $jsonArray= array("SixMonth"=>$allTypeAllMonth,"head"=>$headArr);
?>