<?require_once($_SERVER["DOCUMENT_ROOT"] . '/teams_section/seo_team/header.php');?>
<?php
$APPLICATION->SetTitle("");

use \Bitrix\Iblock\PropertyEnumerationTable;
use \Bitrix\Main\Grid\Options as GridOptions;
use \Bitrix\Main\UI\PageNavigation;

CModule::IncludeModule("crm");

$list_id = 'sale_listings_list';
$factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory(MEETINGS_SPA_ID);

//Достаём все поля сущьности
$fieldsEntity = $factory->getUserFieldsInfo();
$fieldsEntity = array_merge($fieldsEntity, $factory->getFieldsInfo());
$arAcceptField = ['boolean', 'integer', 'enumeration', 'money', 'string', 'url', 'date', 'datetime', 'double', 'text', 'employee', 'user'];
foreach($fieldsEntity as $key=>$value){
	if(!in_array($value['TYPE'], $arAcceptField)) unset($fieldsEntity[$key]);
	if($value['TYPE'] == 'enumeration'){
		foreach($value['ITEMS'] as $key2=>$value2){
			unset($fieldsEntity[$key]['ITEMS'][$key2]);
			$fieldsEntity[$key]['ITEMS'][$value2['ID']] = $value2;
		}
	}
}

//Сортировка и пагинация
$grid_options = new GridOptions($list_id);
$sort = $grid_options->GetSorting(['sort' => ['CREATED_TIME' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();

$nav = new PageNavigation($list_id);
$nav->allowAllRecords(true)
	->setPageSize($nav_params['nPageSize'])
	->initFromUri();
if ($nav->allRecordsShown()) {
	$nav_params = false;
} else {
	$nav_params['iNumPage'] = $nav->getCurrentPage();
}

//Настройки фильтра
$ui_filter = [
	['id' => 'ID', 'name' => 'ID', 'type'=>'number', 'default' => false],
	['id' => 'TITLE', 'name' => 'Title', 'type'=>'text', 'default' => true],
	['id' => 'STAGE_SEMANTIC_ID', 'name' => 'Status', 'type'=>'list', 'items'=>['F'=>'"Lead failed" stage group','S'=>'"Lead converted" stage','P'=>'"Lead in progress" stage group'], 'params'=>['multiple'=>'Y'], 'default' => true],
	['id' => 'CREATED_TIME', 'name' => 'Created on', 'type'=>'date', 'default' => true],
];
foreach($fieldsEntity as $key=>$value){
	if(in_array($value['TYPE'], ['string', 'enumeration'])){
		$list_item_filter = [];
		if($value['TYPE'] == 'enumeration'){
			foreach($value['ITEMS'] as $key2=>$value2){
				$list_item_filter[$value2['ID']] = $value2['VALUE'];
			}
			$ui_filter[] = ['id' => $key, 'name' => $value['TITLE'], 'type'=>'list', 'items'=>$list_item_filter, 'params'=>['multiple'=>'N'], 'default' => false];
			continue;
		}
		$ui_filter[] = ['id' => $key, 'name' => $value['TITLE'], 'type'=>'text', 'default' => false];
	}
}

?>
	<style>
		.filter_panel {
			display: flex;
			align-items: center;
		}
		.filter_panel div:first-child {
			margin-right:20px;
		}
	</style>
	<div class="filter_panel">
		<div>
			<a target="_blank" href="/crm/type/<?=MEETINGS_SPA_ID?>/details/0/"><button class="ui-btn ui-btn-success">Create</button></a>
		</div>
		<div>
			<?$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
				'FILTER_ID' => $list_id,
				'GRID_ID' => $list_id,
				'FILTER' => $ui_filter,
				'ENABLE_LIVE_SEARCH' => true,
				'ENABLE_LABEL' => true
			]);?>
		</div>
	</div>
    <div style="clear: both;"></div>

<?php
$filterOption = new Bitrix\Main\UI\Filter\Options($list_id);
$filterData = $filterOption->getFilter([]);

//Поиск по названию
if(isset($filterData['FIND']) && $filterData['FIND']){
	$filterData['TITLE'] = "%".$filterData['FIND']."%";
}
//Дата создания
if(isset($filterData['DATE_CREATE_from']) && $filterData['DATE_CREATE_from']){
	$filterData['>=CREATED_TIME'] = Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($filterData['DATE_CREATE_from']));
}
if(isset($filterData['DATE_CREATE_to']) && $filterData['DATE_CREATE_to']){
	$filterData['<=CREATED_TIME'] = Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($filterData['DATE_CREATE_to']));
}
//ID
if(isset($filterData['ID_from']) && $filterData['ID_from']){
	$filterData['>=ID'] = $filterData['ID_from'];
}
if(isset($filterData['ID_to'])){
	if($filterData['ID_to']){
		$filterData['<=ID'] = $filterData['ID_to'];
	}else{
		$filterData['<=ID'] = $filterData['ID_from'];
	}
}
foreach ($filterData as $k => $v) {
	if(in_array($k, ['CREATED_TIME', '<=CREATED_TIME', '>=CREATED_TIME', '<=ID', '>=ID'])) break;
	if(!isset($fieldsEntity[$k])){
		unset($filterData[$k]);
	}
}

//Столбцы
$columns = [];
foreach($fieldsEntity as $key=>$value){
	if($key == 'TITLE'){
		$value['TITLE'] = $fieldsEntity[$key]['TITLE'] = 'Title';
	}
	$columns[] = ['id' => $key, 'name' => $value['TITLE'], 'type'=>$value['TYPE'], 'sort' => $key, 'default' => false];
}


//Получаем данные
$items = [];
$nav_params['iNumPage']--;
$result = $factory->getItems([
	'select'=>array_keys($fieldsEntity),
	'filter'=>$filterData,
	'order'=>$sort['sort'],
	'limit'=>$nav_params['nPageSize'],
	'offset'=>$nav_params['iNumPage']*$nav_params['nPageSize'],
]);
$nav->setRecordCount($factory->getItemsCount($filterData));

//Готовим даные 
foreach($result as $key=>$value){
	$data = [];
	$items[] = $item = $value->getData();
	foreach($item as $id_field=>$value_field){
		if(isset($fieldsEntity[$id_field])){
			switch($fieldsEntity[$id_field]['TYPE']){
				case 'employee': case 'user':
					if(is_array($value_field)){
						foreach($value_field as $item_value_field){
							if($item_value_field){
								$user_data = $USER->GetById($item_value_field)->Fetch();
								$data[$id_field] = '<a target="_blank" href="/company/personal/user/'.$item_value_field.'/">'.trim($user_data['LAST_NAME'].' '.$user_data['NAME']).'</a>'.PHP_EOL;
							}
						}
					}else{
						if($value_field){
							$user_data = $USER->GetById($value_field)->Fetch();
							$data[$id_field] = '<a href="/company/personal/user/'.$value_field.'/">'.trim($user_data['LAST_NAME'].' '.$user_data['NAME']).'</a>';
						}
					}
				break;
				case 'enumeration':
					if(is_array($value_field)){
						foreach($value_field as $item_value_field){
							$data[$id_field] = $fieldsEntity[$id_field]['ITEMS'][$item_value_field]['VALUE'].PHP_EOL;
						}
					}else{
						$data[$id_field] = $fieldsEntity[$id_field]['ITEMS'][$value_field]['VALUE'];
					}
				break;
				case 'date': case 'datetime':
					if(is_array($value_field)){
						foreach($value_field as $item_value_field){
							if($item_value_field){
								$data[$id_field] = $item_value_field->toString().PHP_EOL;
							}else{
								$data[$id_field] = $item_value_field;
							}
						}
					}else{
						if($value_field){
							$data[$id_field] = $value_field->toString();
						}else{
							$data[$id_field] = $value_field;
						}
					}
				break;
				case 'boolean':
					if($value_field == '1'){
						$value_field = 'Y';
					}else{
						$value_field = 'N';
					}
					$data[$id_field] = $value_field;
				break;
				default:
					if(is_array($value_field)){
						foreach($value_field as $item_value_field){
							$data[$id_field] = $item_value_field.PHP_EOL;
						}
					}else{
						if($id_field == 'TITLE'){
							$data[$id_field] = '<a href="/crm/type/'.MEETINGS_SPA_ID.'/details/'.$item['ID'].'/">'.$value_field.'</a>';
						}else{
							$data[$id_field] = $value_field;
						}
					}
				break;
			}
			
		}
	}
	$list[] = [
		'data' => $data,
		/*
		'actions' => [
			[
				'text'    => 'Просмотр',
				'default' => true,
				'onclick' => 'document.location.href="?op=view&id='.$row['ID'].'"'
			], [
				'text'    => 'Удалить',
				'default' => true,
				'onclick' => 'if(confirm("Точно?")){document.location.href="?op=delete&id='.$row['ID'].'"}'
			]
		]
		*/
	];
	
}


$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
	'GRID_ID' => $list_id,
	'COLUMNS' => $columns,
	'ROWS' => $list,
	'SHOW_ROW_CHECKBOXES' => false,
	'NAV_OBJECT' => $nav,
	'AJAX_MODE' => 'Y',
	'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
	'PAGE_SIZES' =>  [
		['NAME' => '20', 'VALUE' => '20'],
		['NAME' => '50', 'VALUE' => '50'],
		['NAME' => '100', 'VALUE' => '100']
	],
	'AJAX_OPTION_JUMP'          => 'N',
	'SHOW_CHECK_ALL_CHECKBOXES' => false,
	'SHOW_ROW_ACTIONS_MENU'     => true,
	'SHOW_GRID_SETTINGS_MENU'   => true,
	'SHOW_NAVIGATION_PANEL'     => true,
	'SHOW_PAGINATION'           => true,
	'SHOW_SELECTED_COUNTER'     => true,
	'SHOW_TOTAL_COUNTER'        => true,
	'SHOW_PAGESIZE'             => true,
	'SHOW_ACTION_PANEL'         => true,
	'ALLOW_COLUMNS_SORT'        => true,
	'ALLOW_COLUMNS_RESIZE'      => true,
	'ALLOW_HORIZONTAL_SCROLL'   => true,
	'ALLOW_SORT'                => true,
	'ALLOW_PIN_HEADER'          => true,
	'AJAX_OPTION_HISTORY'       => 'N'
]);
?>

<?require_once($_SERVER["DOCUMENT_ROOT"] . '/teams_section/seo_team/footer.php');?>