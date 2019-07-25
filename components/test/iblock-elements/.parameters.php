<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if (!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-" => " "));

$arIBlocks = array();
$db_iblock = CIBlock::GetList(array("SORT" => "ASC"), array("SITE_ID" => $_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")));
while ($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];

$arSorts = array("ASC" => GetMessage("IBLOCK_ELEMENTS_DESC_ASC"), "DESC" => GetMessage("IBLOCK_ELEMENTS_DESC_DESC"));
$arSortFields = array(
	"ID" => GetMessage("IBLOCK_ELEMENTS_DESC_FID"),
	"NAME" => GetMessage("IBLOCK_ELEMENTS_DESC_FNAME"),
	"ACTIVE_FROM" => GetMessage("IBLOCK_ELEMENTS_DESC_FACT"),
	"SORT" => GetMessage("IBLOCK_ELEMENTS_DESC_FSORT"),
	"TIMESTAMP_X" => GetMessage("IBLOCK_ELEMENTS_DESC_FTSAMP")
);

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])));
while ($arr = $rsProp->Fetch()) {
	$arProperty[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S"))) {
		$arProperty_LNS[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
	}
}


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"SORT_BY1" => array(
			"NAME" => GetMessage("IBLOCK_ELEMENTS_DESC_IBORD1"),
			"TYPE" => "LIST",
			"DEFAULT" => "ACTIVE_FROM",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER1" => array(
			"NAME" => GetMessage("IBLOCK_ELEMENTS_DESC_IBBY1"),
			"TYPE" => "LIST",
			"DEFAULT" => "DESC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_BY2" => array(
			"NAME" => GetMessage("IBLOCK_ELEMENTS_DESC_IBORD2"),
			"TYPE" => "LIST",
			"DEFAULT" => "SORT",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER2" => array(
			"NAME" => GetMessage("IBLOCK_ELEMENTS_DESC_IBBY2"),
			"TYPE" => "LIST",
			"DEFAULT" => "ASC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"FILTER" => array(
			"NAME" => GetMessage("IBLOCK_ELEMENTS_FILTER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"PROPERTY_CODE" => array(
			"NAME" => GetMessage("IBLOCK_ELEMENTS_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_LNS,
			"ADDITIONAL_VALUES" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>300000),
	),
);
?>
