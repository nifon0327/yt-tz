<?php
//$DataPublic.staffsheet 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="员工资料";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
echo $ActionId;
switch($ActionId){
	case 64:		//需生成三份文件：农行工资单、工行工资单、其他和现金工资单  ewen2014-06-11
		$Log_Funtion="生成银行单";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		//工行格式：#币种|日期|顺序号|付款帐号|付款账号类型|收款帐号|收款帐号名称|金额|用途|备注信息|是否允许收款人查看付款人信息|
		$ICBC="#总计信息\r\n#注意：本文件中的金额均以分为单位！\r\n#币种|日期|总计标志|总金额|总笔数|\r\n";
		//$ABC=",,单位代发工资名单,\r\n,,,\r\n,单位名称:,上海市研砼包装有限公司,\r\n ,单位帐号:,' 41021700040055832,\r\n,联系电话:,61139580,,单位印鉴:,,\r\n,,,\r\n序号,姓名,帐号:,金额:\r\n";	//农行文件
		$CreatedTime=date("Y-m-dTH:i:sZ");
		$ABC="
		<?xml version='1.0'?>
		<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet'
		 xmlns:o='urn:schemas-microsoft-com:office:office'
		 xmlns:x='urn:schemas-microsoft-com:office:excel'
		 xmlns:dt='uuid:C2F41010-65B3-11d1-A29F-00AA00C14882'
		 xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet'
		 xmlns:html='http://www.w3.org/TR/REC-html40'>
		 <DocumentProperties xmlns='urn:schemas-microsoft-com:office:office'>
		 <LastAuthor>ewen</LastAuthor>
		  <Created>".date("Y-m-d")."T".date("H:i:s")."Z</Created>
		  <Company>middlecloud</Company>
		  <Version>14.0</Version>
		 </DocumentProperties>
		 <CustomDocumentProperties xmlns='urn:schemas-microsoft-com:office:office'>
		  <KSOProductBuildVer dt:dt='string'>2052-9.1.0.4636</KSOProductBuildVer>
		 </CustomDocumentProperties>
		 <OfficeDocumentSettings xmlns='urn:schemas-microsoft-com:office:office'>
		  <AllowPNG/>
		 </OfficeDocumentSettings>
		 <ExcelWorkbook xmlns='urn:schemas-microsoft-com:office:excel'>
		  <WindowHeight>9000</WindowHeight>
		  <WindowWidth>25120</WindowWidth>
		  <WindowTopX>5580</WindowTopX>
		  <WindowTopY>0</WindowTopY>
		  <TabRatio>600</TabRatio>
		  <ProtectStructure>False</ProtectStructure>
		  <ProtectWindows>False</ProtectWindows>
		 </ExcelWorkbook>
		 <Styles>
		  <Style ss:ID='Default' ss:Name='Normal'>
		   <Alignment ss:Vertical='Bottom'/>
		   <Borders/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		   <Interior/>
		   <NumberFormat/>
		   <Protection/>
		  </Style>
		  <Style ss:ID='m419505208'>
		   <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s62'>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s63'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s64'>
		   <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s65'>
		   <Alignment ss:Vertical='Bottom'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s66'>
		   <Alignment ss:Vertical='Center'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		   <NumberFormat ss:Format='@'/>
		  </Style>
		  <Style ss:ID='s67'>
		   <Alignment ss:Horizontal='Left' ss:Vertical='Center'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s68'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
		   <Borders>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		   <Interior ss:Color='#C0C0C0' ss:Pattern='Solid'/>
		  </Style>
		  <Style ss:ID='s69'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		  </Style>
		  <Style ss:ID='s70'>
		   <Alignment ss:Horizontal='Left' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		  </Style>
		  <Style ss:ID='s71'>
		   <Alignment ss:Horizontal='Left' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <NumberFormat ss:Format='@'/>
		  </Style>
		  <Style ss:ID='s72'>
		   <Alignment ss:Horizontal='Right' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		  </Style>
		  <Style ss:ID='s73'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		 </Styles>
		 <Worksheet ss:Name='农行代发工资（总公司)'>
		  <Table ss:ExpandedColumnCount='4' ss:ExpandedRowCount='208' x:FullColumns='1'
		   x:FullRows='1' ss:StyleID='s62' ss:DefaultColumnWidth='54'
		   ss:DefaultRowHeight='15'>
		   <Column ss:StyleID='s63' ss:Width='33'/>
		   <Column ss:StyleID='s62' ss:AutoFitWidth='0' ss:Width='61'/>
		   <Column ss:StyleID='s62' ss:AutoFitWidth='0' ss:Width='164'/>
		   <Column ss:StyleID='s64' ss:AutoFitWidth='0' ss:Width='70'/>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s65'/>
			<Cell ss:StyleID='s63'><Data ss:Type='String'>单位代发工资名单</Data></Cell>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s65'/>
			<Cell ss:StyleID='s63'/>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s63'><Data ss:Type='String'>单位名称:</Data></Cell>
			<Cell ss:StyleID='s63'><Data ss:Type='String'>上海市研砼包装有限公司</Data></Cell>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell><Data ss:Type='String'> </Data></Cell>
			<Cell ss:StyleID='s63'><Data ss:Type='String'>单位帐号:</Data></Cell>
			<Cell ss:StyleID='s66'><Data ss:Type='String'>41021700040055832</Data></Cell>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s63'><Data ss:Type='String'>联系电话:</Data></Cell>
			<Cell ss:StyleID='s67'><Data ss:Type='Number'>61139580</Data></Cell>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s63'><Data ss:Type='String'>单位印鉴:</Data></Cell>
			<Cell ss:StyleID='s63'/>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s63'/>
			<Cell ss:StyleID='s63'/>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>序号</Data></Cell>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>姓名</Data></Cell>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>帐号:</Data></Cell>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>金额:</Data></Cell>
		   </Row>
		";
		$Other="
		<?xml version='1.0'?>
		<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet'
		 xmlns:o='urn:schemas-microsoft-com:office:office'
		 xmlns:x='urn:schemas-microsoft-com:office:excel'
		 xmlns:dt='uuid:C2F41010-65B3-11d1-A29F-00AA00C14882'
		 xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet'
		 xmlns:html='http://www.w3.org/TR/REC-html40'>
		 <DocumentProperties xmlns='urn:schemas-microsoft-com:office:office'>
		 <LastAuthor>ewen</LastAuthor>
		  <Created>".date("Y-m-d")."T".date("H:i:s")."Z</Created>
		  <Company>middlecloud</Company>
		  <Version>14.0</Version>
		 </DocumentProperties>
		 <CustomDocumentProperties xmlns='urn:schemas-microsoft-com:office:office'>
		  <KSOProductBuildVer dt:dt='string'>2052-9.1.0.4636</KSOProductBuildVer>
		 </CustomDocumentProperties>
		 <OfficeDocumentSettings xmlns='urn:schemas-microsoft-com:office:office'>
		  <AllowPNG/>
		 </OfficeDocumentSettings>
		 <ExcelWorkbook xmlns='urn:schemas-microsoft-com:office:excel'>
		  <WindowHeight>9000</WindowHeight>
		  <WindowWidth>25120</WindowWidth>
		  <WindowTopX>5580</WindowTopX>
		  <WindowTopY>0</WindowTopY>
		  <TabRatio>600</TabRatio>
		  <ProtectStructure>False</ProtectStructure>
		  <ProtectWindows>False</ProtectWindows>
		 </ExcelWorkbook>
		 <Styles>
		  <Style ss:ID='Default' ss:Name='Normal'>
		   <Alignment ss:Vertical='Bottom'/>
		   <Borders/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		   <Interior/>
		   <NumberFormat/>
		   <Protection/>
		  </Style>
		  <Style ss:ID='m419505208'>
		   <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s62'>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s63'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s64'>
		   <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s65'>
		   <Alignment ss:Vertical='Bottom'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s66'>
		   <Alignment ss:Vertical='Center'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		   <NumberFormat ss:Format='@'/>
		  </Style>
		  <Style ss:ID='s67'>
		   <Alignment ss:Horizontal='Left' ss:Vertical='Center'/>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		  <Style ss:ID='s68'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
		   <Borders>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		   <Interior ss:Color='#C0C0C0' ss:Pattern='Solid'/>
		  </Style>
		  <Style ss:ID='s69'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		  </Style>
		  <Style ss:ID='s70'>
		   <Alignment ss:Horizontal='Left' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		  </Style>
		  <Style ss:ID='s71'>
		   <Alignment ss:Horizontal='Left' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <NumberFormat ss:Format='@'/>
		  </Style>
		  <Style ss:ID='s72'>
		   <Alignment ss:Horizontal='Right' ss:Vertical='Center'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		  </Style>
		  <Style ss:ID='s73'>
		   <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
		   <Borders>
			<Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
			<Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
		   </Borders>
		   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
		  </Style>
		 </Styles>
		 <Worksheet ss:Name='其他银行或现金工资（总公司)'>
		  <Table ss:ExpandedColumnCount='4' ss:ExpandedRowCount='208' x:FullColumns='1'
		   x:FullRows='1' ss:StyleID='s62' ss:DefaultColumnWidth='54'
		   ss:DefaultRowHeight='15'>
		   <Column ss:StyleID='s63' ss:Width='33'/>
		   <Column ss:StyleID='s62' ss:AutoFitWidth='0' ss:Width='61'/>
		   <Column ss:StyleID='s62' ss:AutoFitWidth='0' ss:Width='164'/>
		   <Column ss:StyleID='s64' ss:AutoFitWidth='0' ss:Width='70'/>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s65'/>
			<Cell ss:StyleID='s63'><Data ss:Type='String'>其他银行或现金工资名单</Data></Cell>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:Index='2' ss:StyleID='s65'/>
			<Cell ss:StyleID='s63'/>
		   </Row>
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>序号</Data></Cell>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>姓名</Data></Cell>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>帐号:</Data></Cell>
			<Cell ss:StyleID='s68'><Data ss:Type='String'>金额:</Data></Cell>
		   </Row>
		";
		//$Other=",,其他银行或现金工资名单,\r\n,,,\r\n序号,姓名,帐号:,金额:\r\n";	//其他银行文件
		$checkSql=mysql_query("SELECT A.Amount,B.Bank AS Bank_B,B.Bank2 AS Bank_A,B.Bank3 AS Bank_C,C.Name
							  FROM $DataIn.cwxzsheet A 
							  LEFT JOIN $DataPublic.staffsheet B ON B.Number=A.Number 
							  LEFT JOIN $DataPublic.staffmain C ON C.Number=B.Number 
							  WHERE A.Id IN ($Ids)",$link_id);
		if($checkRow=mysql_fetch_array($checkSql)){
			$ABC_i=$ICBC_i=$Other_i=1;
			$AmountTatol_A=$AmountTatol_B=$AmountTatol_C=0;
			do{
				$Bank_A=$Bank_B=$Bank_C="";
				$Amount_A=$Amount_B=$Amount_C=0;
				$Name=$checkRow["Name"];								//员工姓名
				$Bank_A_Temp=$checkRow["Bank_A"];
				if($Bank_A_Temp!=""){
					$Bank_A_Array=explode("|",$Bank_A_Temp);		//农行
					$Bank_A=$Bank_A_Array[0];								//农行
					}
				$Bank_B=$checkRow["Bank_B"];							//工行

				$Bank_C_Temp=$checkRow["Bank_C"];
				if($Bank_C_Temp!=""){
					$Bank_C_Array=explode("|",$Bank_C_Temp);
					$Bank_C=$Bank_C_Array[0];								//其他银行
					}

				$Amount=$checkRow["Amount"]*100;					//员工实付薪资
				//////////////////////////////
				//默认薪资分配
				if($Amount>398500){
					$Amount_A=398500;							//农行
					$Amount_B=$Amount-$Amount_A;	//工行
					}
				else{
					$Amount_A=$Amount;					//只发农行
					}

				$AmountTatol_A+=$Amount_A;
				$AmountTatol_B+=$Amount_B;
				$AmountTatol_C+=$Amount_C;
				//////////////////////////
				if($Amount_A>0){//如果农行金额大于0,则加入至工行资料 序号,姓名,帐号,金额
					$Amount_A=$Amount_A*0.01;
					//$ABC.=$ABC_i.",".$Name.", ".$Bank_A.",".$Amount_A."\r\n";
					$ABC.="<Row ss:AutoFitHeight='0'>
					<Cell ss:StyleID='s69'><Data ss:Type='Number'>$ABC_i</Data></Cell>
					<Cell ss:StyleID='s70'><Data ss:Type='String'>$Name</Data></Cell>
					<Cell ss:StyleID='s71'><Data ss:Type='String'>$Bank_A</Data></Cell>
					<Cell ss:StyleID='s72'><Data ss:Type='Number'>$Amount_A</Data></Cell>
				   </Row>";
					$ABC_i++;
					}

				if($Amount_B>0){//如果工行金额大于0,则加入至工行资料
					$ICBC_Row.="RMB|20100420|$ICBC_i|6222004000122695224|灵通卡|".$Bank_B."|".$Amount_B."|||0|\r\n";
					$ICBC_i++;
					}

				if($Amount_C>0){//如果工行金额大于0,则加入至工行资料
					$Amount_C=$Amount_C*0.01;
					$Bank_C=$Bank_C==""?"现金":$Bank_C;
					//$Other.=$Other_i.",".$Name.", ".$Bank_C.",".$Amount_C."\r\n";
					$Other.="<Row ss:AutoFitHeight='0'>
					<Cell ss:StyleID='s69'><Data ss:Type='Number'>$Other_i</Data></Cell>
					<Cell ss:StyleID='s70'><Data ss:Type='String'>$Name</Data></Cell>
					<Cell ss:StyleID='s71'><Data ss:Type='String'>$Bank_C</Data></Cell>
					<Cell ss:StyleID='s72'><Data ss:Type='Number'>$Amount_C</Data></Cell>
				   </Row>";
					$Other_i++;
					}

				}while ($checkRow=mysql_fetch_array($checkSql));

			$datetime=date("YmdHis");
			//农行文件输出
			$AmountTatol_A=$AmountTatol_A*0.01;
			//$ABC.="合计,,,$AmountTatol_A\r\n";
			//$ABC=iconv("UTF-8","GB2312//IGNORE",$ABC);
			//$file_A="../download/bankbill/abc_xz".$datetime.".csv";
			$ABC_i=$ABC_i-1;
			$ABC.="<Row ss:AutoFitHeight='0'>
    					<Cell ss:StyleID='s73'><Data ss:Type='String'>合计</Data></Cell>
    					<Cell ss:MergeAcross='2' ss:StyleID='m419505208'
     					ss:Formula='=SUM(R[-$ABC_i]C[2]:R[-1]C[2])'><Data ss:Type='Number'>$AmountTatol_A</Data></Cell>
   						</Row>
  						</Table>
						</Worksheet>
						</Workbook>";
			$file_A="../download/bankbill/abc_xz".$datetime.".xml";
			if(!file_exists($file_A)){
				$handle=fopen($file_A,"a");
				fwrite($handle,$ABC);
				fclose($handle);
				}

			//工行文件输出
			$ICBC_i=$ICBC_i-1;
			$ICBC.="RMB|20100420|1|$AmountTatol_B|$ICBC_i|\r\n#明细指令信息\r\n#其中付款账号类型：灵通卡、理财金0；信用卡1\r\n#币种|日期|顺序号|付款帐号|付款账号类型|收款帐号|收款帐号名称|金额|用途|备注信息|是否允许收款人查看付款人信息|\r\n";
			$ICBC_Row.="*";
			$ICBC=$ICBC.$ICBC_Row;
			$ICBC=iconv("UTF-8","GB2312//IGNORE",$ICBC);
			$file_B="../download/bankbill/icbc_xz".$datetime.".gbpt";
			if(!file_exists($file_B)){
				$handle=fopen($file_B,"a");
				fwrite($handle,$ICBC);
				fclose($handle);
				}
			//其他银行或现金文件输出
			$AmountTatol_C=$AmountTatol_C*0.01;
			//$Other.="合计,,,$AmountTatol_C\r\n";
			//$Other=iconv("UTF-8","GB2312//IGNORE",$Other);
			//$file_C="../download/bankbill/other_xz".$datetime.".csv";
			$Other_i=$Other_i-1;
			$Other.="<Row ss:AutoFitHeight='0'>
    					<Cell ss:StyleID='s73'><Data ss:Type='String'>合计</Data></Cell>
    					<Cell ss:MergeAcross='2' ss:StyleID='m419505208'
     					ss:Formula='=SUM(R[-$Other_i]C[2]:R[-1]C[2])'><Data ss:Type='Number'>$AmountTatol_C</Data></Cell>
   						</Row>
  						</Table>
						</Worksheet>
						</Workbook>";
			$file_C="../download/bankbill/other_xz".$datetime.".xml";
			if(!file_exists($file_C)){
				$handle=fopen($file_C,"a");
				fwrite($handle,$Other);
				fclose($handle);
				}

			}

			$FilePath="download/bankbill/";
			$file_A="abc_xz".$datetime.".xml";

			$Field_A=$file_A;
			$Field_A=anmaIn($Field_A,$SinkOrder,$motherSTR);
			$Td_A=anmaIn($FilePath,$SinkOrder,$motherSTR);
			$file_A="<a href=\"../admin/openorload.php?d=$Td_A&f=$Field_A&Type=&Action=6\" target=\"download\">农行薪资单</a>";

			$file_C="other_xz".$datetime.".xml";
			$Field_C=$file_C;
			$Field_C=anmaIn($Field_C,$SinkOrder,$motherSTR);
			$Td_C=anmaIn($FilePath,$SinkOrder,$motherSTR);
			$file_C="<a href=\"../admin/openorload.php?d=$Td_C&f=$Field_C&Type=&Action=6\" target=\"download\">其他银行或现金薪资单</a>";

		//列出下载链接
		$Log="点击下载生成的<br>$file_A<br><a href='$file_B' target='_blank'>工行薪资单</a><br>$file_C<br>";
		break;
	default:
		$x=1;
		$Bank_A=FormatSTR($Bank_A);
		$Bank_B=FormatSTR($Bank_B);
		$Bank_C=FormatSTR($Bank_C);
		$UpSql ="UPDATE 
			$DataPublic.staffmain 
			INNER JOIN $DataPublic.staffsheet ON $DataPublic.staffsheet.Number=$DataPublic.staffmain.Number
			SET $DataPublic.staffmain.Currency='$Currency',
			$DataPublic.staffsheet.Bank='$Bank_B',
			$DataPublic.staffsheet.Bank2='$Bank_A',
			$DataPublic.staffsheet.Bank3='$Bank_C' 
			WHERE $DataPublic.staffmain.Number='$Number'";
		$UpResult = mysql_query($UpSql);
		if($UpResult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp; 员工编号为 $Number 的信息更新成功!</br>";
			}
		else{
			$Log.="&nbsp;&nbsp;员工编号为 $Number 的信息更新失败! $UpSql</br>";
			$OperationResult="N";
			}
	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>