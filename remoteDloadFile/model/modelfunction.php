<?php 
//$Log_Funtion="PDF图片上传";
function newGetDateSTR() {
			$array_date=getdate();
			if ($array_date[mon]<10) { $array_date[mon]="0".$array_date[mon]; }
			if ($array_date[mday]<10) { $array_date[mday]="0".$array_date[mday]; }
			if ($array_date[hours]<10) { $array_date[hours]="0".$array_date[hours]; }
			if ($array_date[minutes]<10) { $array_date[minutes]="0".$array_date[minutes]; }
			if ($array_date[seconds]<10) { $array_date[seconds]="0".$array_date[seconds]; }
			$temptime=$array_date[year].$array_date[mon].$array_date[mday].$array_date[hours].$array_date[minutes].$array_date[seconds];
			return $temptime;
}

?>