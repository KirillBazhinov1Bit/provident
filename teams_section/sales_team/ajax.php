<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('STOP_STATISTICS', true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/teams_section/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/teams_section/classes/main.php';
foreach($_POST as $key_post => $value_post){
	if($key_post == 'GET_DASHBOARD_SALE'){
		$value_post['TYPE_DATE'] = $value_post['TYPE_DATE'] ?? '';
		$value_post['DATE_START'] = $value_post['DATE_START'] ?? '';
		$value_post['DATE_STOP'] = $value_post['DATE_STOP'] ?? '';
		
		$date = \bitCustomPages::prepareDate($value_post['TYPE_DATE'], $value_post['DATE_START'], $value_post['DATE_STOP']);
		
		$resultData = \bitCustomPages::getDashboardSales($value_post['USER_ID'], $date['date_start'], $date['date_stop']);
		
		$resultData['test'] = \bitCustomPages::getIblok();
		die(json_encode($resultData,JSON_UNESCAPED_UNICODE));
	}
}
?>