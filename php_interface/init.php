<?
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->AddEventHandler("main", "OnBeforeProlog", "teamsSectionDistribution", 50);
function teamsSectionDistribution(){
	global $USER;
	global $APPLICATION;
	$arUserGroup = $USER->GetUserGroup($USER->getId());
	if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/teams_section/settings.php')){
		require_once $_SERVER['DOCUMENT_ROOT'] . '/teams_section/settings.php';
		//Sales group
		if(in_array(SALES_GROUP_ID, $arUserGroup)){
			$path = $APPLICATION->GetCurPage();
			$arPath = explode('/', $path);
			if(($arPath['0'] == '' && $arPath['1'] == '') || ($arPath['0'] == '' && $arPath['2'] == 'seo_team') || ($arPath['0'] == '' && $arPath['2'] == 'md_team')){
				\LocalRedirect("/teams_section/sales_team/");
			}
		}
		//Seo group
		if(in_array(SEO_GROUP_ID, $arUserGroup)){
			$path = $APPLICATION->GetCurPage();
			$arPath = explode('/', $path);
			if(($arPath['0'] == '' && $arPath['1'] == '') || ($arPath['0'] == '' && $arPath['2'] == 'sales_team') || ($arPath['0'] == '' && $arPath['2'] == 'md_team')){
				\LocalRedirect("/teams_section/seo_team/");
			}
		}
		//MD group
		if(in_array(MD_GROUP_ID, $arUserGroup)){
			$path = $APPLICATION->GetCurPage();
			$arPath = explode('/', $path);
			if(($arPath['0'] == '' && $arPath['1'] == '') || ($arPath['0'] == '' && $arPath['2'] == 'sales_team') || ($arPath['0'] == '' && $arPath['2'] == 'md_team')){
				\LocalRedirect("/teams_section/seo_team/");
			}
		}
	}
}