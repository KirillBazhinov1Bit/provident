<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/teams_section/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/teams_section/classes/main.php';

$APPLICATION->SetTitle("Seo");
$APPLICATION->ShowHead();
CJSCore::Init(['jquery3']);
\Bitrix\Main\UI\Extension::load("ui.buttons");

?>
<link rel="stylesheet" href="/teams_section/plugin/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="/teams_section/plugin/bootstrap/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="/teams_section/plugin/bootstrap/bootstrap-select.min.css">
<link rel="stylesheet" href="/teams_section/plugin/bootstrap/ajax-bootstrap-select.min.css">
<script src="/teams_section/plugin/bootstrap/bootstrap.min.js"></script>
<script src="/teams_section/plugin/bootstrap/bootstrap-select.min.js"></script>
<script src="/teams_section/plugin/bootstrap/moment-with-locales.min.js"></script>
<script src="/teams_section/plugin/bootstrap/bootstrap-datetimepicker.min.js"></script>
<script src="/teams_section/plugin/bootstrap/ajax-bootstrap-select.min.js"></script>
<style>
	body {
		display: flex;
		background-color: #9e9e9e42;
	}
	.left_menu {
		min-width: 265px;
	}
	.content_section {
		width:100%;
	}
	.content_page {
		margin: 10px 0 10px 10px;
		border-radius: 5px 0 0 5px;
		padding: 10px;
		background-color: #fdf3f3;
	}
	.button:active, button:focus {
		outline: none;
	}
</style>
<aside class="left_menu">
	<?$APPLICATION->IncludeComponent(
		"bit:menu",
		"left_vertical",
		Array(
			"ALLOW_MULTI_SELECT" => "N",
			"CACHE_SELECTED_ITEMS" => "N",
			"CHILD_MENU_TYPE" => "left",
			"COMPONENT_TEMPLATE" => "left_vertical",
			"DELAY" => "N",
			"MAX_LEVEL" => "1",
			"MENU_CACHE_GET_VARS" => array(),
			"MENU_CACHE_TIME" => "604800",
			"MENU_CACHE_TYPE" => "Y",
			"MENU_CACHE_USE_GROUPS" => "N",
			"MENU_CACHE_USE_USERS" => "Y",
			"ROOT_MENU_TYPE" => "seo_left",
			"USE_EXT" => "Y"
		)
	);?> 
</aside> 
<section class="content_section">
<?//$APPLICATION->ShowPanel();?>
	<div class="top_menu">
		 <?$APPLICATION->IncludeComponent(
		"bit:menu",
		"top_horizontal",
		Array(
			"ALLOW_MULTI_SELECT" => "N",
			"CHILD_MENU_TYPE" => "left",
			"DELAY" => "N",
			"MAX_LEVEL" => "1",
			"MENU_CACHE_GET_VARS" => array(""),
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_THEME" => "site",
			"ROOT_MENU_TYPE" => "seo_top",
			"USE_EXT" => "N"
		)
	);?>
	</div>
	<div class="content_page">