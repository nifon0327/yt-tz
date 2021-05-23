<?php
include_once "../../basic/parameter.inc";
include_once("getWageSignState.php");
echo checkWageSign('11008', $DataIn, $DataPublic, $link_id);

?>