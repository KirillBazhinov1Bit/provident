<?
class bitCustomPages {
	public static function getUserData($user_id){
		global $USER;
		$arUserData = $USER->GetById($user_id)->Fetch();
		$arUserData['DEPARTMENTS'] = \Bitrix\Main\UserUtils::getDepartmentNames($arUserData['UF_DEPARTMENT']);
		$arUserData['DEPARTMENT'] = $arUserData['DEPARTMENTS'][0]['NAME'] ?? '';
		$arUserData['FULL_NAME'] = $USER->GetFullName();
		$arUserData['PERSONAL_PHOTO_LINK'] = '/bitrix/js/ui/icons/b24/images/ui-user.svg?v2';
		if($arUserData['PERSONAL_PHOTO']){
			$arUserData['PERSONAL_PHOTO_LINK'] = \CFile::GetById($arUserData['PERSONAL_PHOTO'])->Fetch()['SRC'];
		}
		return $arUserData;
	}
	public static function getDashboardSales($user_id, $date_start = '', $date_stop = ''){
		\CModule::IncludeModule('crm');
		\CModule::IncludeModule('voximplant');
		\CModule::includeModule('iblock');
		$factoryLead = \Bitrix\Crm\Service\Container::getInstance()->getFactory(\CCrmOwnerType::Lead);
		$factoryDeal = \Bitrix\Crm\Service\Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
		$factoryLeadPool = \Bitrix\Crm\Service\Container::getInstance()->getFactory(LEADS_POOL_SPA_ID);
		$factoryMeetings = \Bitrix\Crm\Service\Container::getInstance()->getFactory(MEETINGS_SPA_ID);
		$factoryListings = \Bitrix\Crm\Service\Container::getInstance()->getFactory(LISTING_SPA_ID);
		$factoryTargets = \Bitrix\Crm\Service\Container::getInstance()->getFactory(TARGETS_SPA_ID);
		$resultData = self::getDashboardMap();
		$arUserData = self::getUserData($user_id);

		//ЛИДЫ
		$filter = [
			'ASSIGNED_BY_ID'=>$user_id,
		];
		if($date_start)$filter['>DATE_CREATE'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_start." 00:00:00"));
		if($date_stop)$filter['<DATE_CREATE'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_stop." 23:59:59"));
		
		//Общее кол-во лидов полученных агентов за отчетный период во всех статусах
		$items = $factoryLead->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		$resultData['items']['total_leads']['value'] = count($items);
		
		//Общее кол-во лидов полученных агентов за отчетный период на успешной стадии
		$filter['STATUS_SEMANTIC_ID'] = 'S';
		$items = $factoryLead->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		unset($filter['STATUS_SEMANTIC_ID']);
		$success_lead = count($items);
		
		//Общее кол-во лидов за отчетный период истоник которых НЕ = facebook/meta
		$filter['!=SOURCE_ID'] = 'WEBFORM'; //TODO источник будет другой
		$items = $factoryLead->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		$resultData['items']['fresh_leads']['value'] = count($items);
		
		//Общее кол-во лидов помещенных в неуспешный статус агентом за отчетный период
		unset($filter['!=SOURCE_ID']);
		$filter['STATUS_SEMANTIC_ID']='F';
		$items = $factoryLead->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		$resultData['items']['junked_leads']['value'] = count($items);
		
		//Общее кол-во лидов которые были потеряны
		$filter = [
			'IBLOCK_ID'=>33,
			'PROPERTY_96'=>$user_id,
		];
		if($date_start)$filter['>DATE_CREATE'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_start." 00:00:00"));
		if($date_stop)$filter['<DATE_CREATE'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_stop." 23:59:59"));
		$items = \CIBlockElement::GetList(Array(), $arFilter, false, false, ['ID', 'IBLOCK_ID']);
		$result = [];
		while($ar = $items->Fetch()){
			$result[] = $ar;
		}
		$resultData['items']['lost_leads']['value'] = count($result);
		
		//% соотношение кол-ва лидов, которые остались у агента в работе по отношению к лидам, которые были с него переназначены на другого агента
		if($resultData['items']['total_leads']['value'] > 0){
			$resultData['items']['retained_leads']['value'] = round($resultData['items']['lost_leads']['value']/($resultData['items']['total_leads']['value']/100), 2);
		}
		
		//Конверсия из лида в сделку, %
		if($resultData['items']['total_leads']['value'] > 0){
			$resultData['items']['conversion_ratio']['value'] = round($success_lead/($resultData['items']['total_leads']['value']/100), 2);
		}
		
		//СМАРТ ПРОЦЕССЫ
		//общее кол-во лидов, взятых агентом из SPA Leads Pool assignedById createdTime
		$filter = [
			'ASSIGNED_BY_ID'=>$user_id,
		];
		if($date_start)$filter['>CREATED_TIME'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_start." 00:00:00"));
		if($date_stop)$filter['<CREATED_TIME'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_stop." 23:59:59"));
		 
		$items = $factoryLeadPool->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		$resultData['items']['claimed_leads']['value'] = count($items);
		
		//Общее кол-во запланированных встреч (SPA meetings)
		$items = $factoryMeetings->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		$resultData['items']['total_meetings']['value'] = count($items);
		
		//Все листинги по ответственному агенту в статусе aprroved
		//$filter['STAGE_ID'] = ''; //TODO aprroved
		$items = $factoryListings->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		$resultData['items']['active_listings']['value'] = count($items);
		
		//Все листинги по ответственному агенту в статусе Off Market
		//$filter['STAGE_ID'] = ''; //TODO Off Market
		$items = $factoryListings->getItems([
			'filter'=>$filter,
			'select'=>['ID'],
		]);
		$resultData['items']['off_market_listings']['value'] = count($items);
		
		//СДЕЛКИ
		$filter = [
			'ASSIGNED_BY_ID'=>$user_id,
			'STAGE_SEMANTIC_ID'=>'S',
		];
		//кол-во сделок в успешной стадии в воронке
		$items = $factoryDeal->getItems([
			'filter'=>$filter,
			'select'=>['ID', 'OPPORTUNITY', 'UF_CRM_6576D52D70F20'],
		]);
		$resultData['items']['deals_closed']['value'] = count($items);
		foreach($items as $key=>$value){
			//сумма сделок в успешной стадии в воронке
			$resultData['items']['sale_value']['value'] += $value->getOpportunity();
			//сумма комиссии по успешным сделкам
			if($value->hasField('UF_CRM_6576D52D70F20')){
				$resultData['items']['gross_commission']['value'] += $value->get('UF_CRM_6576D52D70F20');
			}
		}
		
		//АКИВИТИ
		//Общее кол-во всех исход писем агента 
		$filter = [
			'PROVIDER_TYPE_ID' => 'EMAIL',
			'AUTHOR_ID' => $user_id,
			'CHECK_PERMISSIONS' => 'N'
		];
		if($date_start)$filter['>CREATED'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_start." 00:00:00"));
		if($date_stop)$filter['<CREATED'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_stop." 23:59:59"));
		
		$resultData['items']['total_emails']['value'] = \CCrmActivity::GetList([], $filter, [], false, ['ID']);
		
		//Общее кол-во запланированных активити агента
		unset($filter['PROVIDER_TYPE_ID']);
		$resultData['items']['total_activities']['value'] = \CCrmActivity::GetList([], $filter, [], false, ['ID']);

		//Общее кол-во звонков сделанных агентом (данные тянутся из интеграции с телефонией)
		$filter = [
			'PORTAL_USER_ID'=>$user_id,
		];
		if($date_start)$filter['>CALL_START_DATE'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_start." 00:00:00"));
		if($date_stop)$filter['<CALL_START_DATE'] = \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime($date_stop." 23:59:59"));
		
		$resultData['items']['total_calls']['value'] = \Bitrix\Voximplant\StatisticTable::getList([            
			"select" => ["ID"],            
			"filter" => $filter,
			"order" => ['ID' => 'DESC'],
			'count_total' => true,
		])->getCount();
		
		//Targets этот блок в самом низу //TODO in prod
		$targets = [];
		$items = $factoryTargets->getItems([]);
		foreach($items as $item){
			$data = $item->getData();
			$targets[$data['UF_CRM_18_TARGET']] = $data;
		}
		foreach($resultData['items'] as $key=>$value){
			if(isset($targets[$value['id_field_target']])){
				$resultData['items'][$key]['progressbar_min'] = $targets[$value['id_field_target']]['UF_CRM_18_MIN_VALUE'];
				$resultData['items'][$key]['progressbar_max'] = $targets[$value['id_field_target']]['UF_CRM_18_MAX_VALUE'];
				if($resultData['items'][$key]['value'] < $resultData['items'][$key]['progressbar_min']){
					$resultData['items'][$key]['color'] = 'red';
				}
			}
		}
		return $resultData;
	}
	public static function getDashboardMD($user_id, $date_start = '', $date_stop = ''){
		global $USER;
		\CModule::includeModule('intranet');
		$structureCompany = \CIntranetUtils::GetStructure();
		$resultData = self::getMdDashboardMap();
		foreach($structureCompany['DATA'] as $key=>$value){
			$departmentData = self::getDashboardMap();
			foreach($value['EMPLOYEES'] as $user){
				$user_dashboard = self::getDashboardSales($user, $date_start, $date_stop);
				foreach($user_dashboard['items'] as $key2=>$value2){
					$departmentData['items'][$key2]['value'] += $value2['value'];
				}
			}
	
			$resultData['items']['leads_table']['items'][] = [
				'user'=>[
					'id'=>$value['ID'],
					'name'=>$value['NAME'],
				],
				'items'=>[
					0=>[
						'value'=>$departmentData['items']['total_leads']['value'],
					],
					1=>[
						'value'=>$departmentData['items']['fresh_leads']['value'],
					],
					2=>[
						'value'=>$departmentData['items']['claimed_leads']['value'],
					],
					3=>[
						'value'=>$departmentData['items']['lost_leads']['value'],
					],
					4=>[
						'value'=>$departmentData['items']['junked_leads']['value'],
					],
					5=>[
						'value'=>$departmentData['items']['retained_leads']['value'],
					],
					6=>[
						'value'=>$departmentData['items']['conversion_ratio']['value'],
					],
				],
			];
			$resultData['items']['deals_table']['items'][] = [
				'user'=>[
					'id'=>$value['ID'],
					'name'=>$value['NAME'],
				],
				'items'=>[
					0=>[
						'value'=>$departmentData['items']['deals_closed']['value'],
					],
					1=>[
						'value'=>$departmentData['items']['sale_value']['value'],
					],
					2=>[
						'value'=>$departmentData['items']['gross_commission']['value'],
					],
				],
			];
			$resultData['items']['listings_table']['items'][] = [
				'user'=>[
					'id'=>$value['ID'],
					'name'=>$value['NAME'],
				],
				'items'=>[
					0=>[
						'value'=>$departmentData['items']['active_listings']['value'],
					],
					1=>[
						'value'=>$departmentData['items']['off_market_listings']['value'],
					],
				],
			];
			$resultData['items']['activities_table']['items'][] = [
				'user'=>[
					'id'=>$value['ID'],
					'name'=>$value['NAME'],
				],
				'items'=>[
					0=>[
						'value'=>$departmentData['items']['total_calls']['value'],
					],
					1=>[
						'value'=>$departmentData['items']['total_emails']['value'],
					],
					2=>[
						'value'=>$departmentData['items']['total_activities']['value'],
					],
					3=>[
						'value'=>$departmentData['items']['total_meetings']['value'],
					],
				],
			];
		}
		return $resultData;
	}
	public static function getDashboardSeo($user_id, $date_start = '', $date_stop = ''){
		global $USER;
		\CModule::includeModule('intranet');
		$structureCompany = \CIntranetUtils::GetStructure();
		$resultData = self::getSeoDashboardMap();
		foreach($structureCompany['DATA'] as $key=>$value){
			if($value['UF_HEAD'] == $user_id){
				foreach($value['EMPLOYEES'] as $user){
					$user_data = $USER->GetById($user)->Fetch();
					$user_name = trim($user_data['LAST_NAME'].' '.$user_data['NAME']);
					$user_dashboard = self::getDashboardSales($user, $date_start, $date_stop);
					$resultData['items']['leads_table']['items'][] = [
						'user'=>[
							'id'=>$user,
							'name'=>$user_name,
						],
						'items'=>[
							0=>[
								'value'=>$user_dashboard['items']['total_leads']['value'],
							],
							1=>[
								'value'=>$user_dashboard['items']['fresh_leads']['value'],
							],
							2=>[
								'value'=>$user_dashboard['items']['claimed_leads']['value'],
							],
							3=>[
								'value'=>$user_dashboard['items']['lost_leads']['value'],
							],
							4=>[
								'value'=>$user_dashboard['items']['junked_leads']['value'],
							],
							5=>[
								'value'=>$user_dashboard['items']['retained_leads']['value'],
							],
							6=>[
								'value'=>$user_dashboard['items']['conversion_ratio']['value'],
							],
						],
					];
					$resultData['items']['deals_table']['items'][] = [
						'user'=>[
							'id'=>$user,
							'name'=>$user_name,
						],
						'items'=>[
							0=>[
								'value'=>$user_dashboard['items']['deals_closed']['value'],
							],
							1=>[
								'value'=>$user_dashboard['items']['sale_value']['value'],
							],
							2=>[
								'value'=>$user_dashboard['items']['gross_commission']['value'],
							],
						],
					];
					$resultData['items']['listings_table']['items'][] = [
						'user'=>[
							'id'=>$user,
							'name'=>$user_name,
						],
						'items'=>[
							0=>[
								'value'=>$user_dashboard['items']['active_listings']['value'],
							],
							1=>[
								'value'=>$user_dashboard['items']['off_market_listings']['value'],
							],
						],
					];
					$resultData['items']['activities_table']['items'][] = [
						'user'=>[
							'id'=>$user,
							'name'=>$user_name,
						],
						'items'=>[
							0=>[
								'value'=>$user_dashboard['items']['total_calls']['value'],
							],
							1=>[
								'value'=>$user_dashboard['items']['total_emails']['value'],
							],
							2=>[
								'value'=>$user_dashboard['items']['total_activities']['value'],
							],
							3=>[
								'value'=>$user_dashboard['items']['total_meetings']['value'],
							],
						],
					];
				}
			}
		}
		return $resultData;
	}
	private static function getMdDashboardMap(){
		return self::getSeoDashboardMap();
	}
	private static function getSeoDashboardMap(){
		return [ 
			'items'=>[
				'leads_table'=>[
					'headers'=>[
						0=>'Agent',
						1=>'Total Leads',
						2=>'Fresh Leads',
						3=>'Claimed Leads',
						4=>'Lost Leads',
						5=>'Junked Leads',
						6=>'Retained Leads',
						7=>'Conversion',
					],
					'items'=>[],
				],
				'deals_table'=>[
					'headers'=>[
						0=>'Agent',
						1=>'Deals Closed',
						2=>'Sale Value',
						3=>'Gross Commission',
					],
					'items'=>[],
				],
				'listings_table'=>[
					'headers'=>[
						0=>'Agent',
						1=>'Active Listings',
						2=>'Off-Market Listings',
					],
					'items'=>[],
				],
				'activities_table'=>[
					'headers'=>[
						0=>'Agent',
						1=>'Total Calls',
						2=>'Total Emails',
						3=>'Total Activities',
						4=>'Total Meetings',
					],
					'items'=>[],
				],	
			],
		];
	}
	private static function getDashboardMap(){
		return [
			'items'=>[
				'total_leads'=>[
					'id_field_target'=>949, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'fresh_leads'=>[
					'id_field_target'=>950, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'claimed_leads'=>[
					'id_field_target'=>951, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'lost_leads'=>[
					'id_field_target'=>952, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'junked_leads'=>[
					'id_field_target'=>953, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'retained_leads'=>[
					'id_field_target'=>954, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'conversion_ratio'=>[
					'id_field_target'=>955, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'active_listings'=>[
					'id_field_target'=>956, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'off_market_listings'=>[
					'id_field_target'=>957, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'deals_closed'=>[
					'id_field_target'=>958, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'sale_value'=>[
					'id_field_target'=>959, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'gross_commission'=>[
					'id_field_target'=>960, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'commission_outcome'=>[
					'id_field_target'=>961, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'total_calls'=>[
					'id_field_target'=>962, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'total_emails'=>[
					'id_field_target'=>963, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'total_activities'=>[
					'id_field_target'=>964, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
				'total_meetings'=>[
					'id_field_target'=>965, //TODO in prod
					'value'=>0,
					'color'=>'green',
					'progressbar_min'=>0,
					'progressbar_max'=>100,
				],
			],
		];
	}
	public static function prepareDate($type_date, $date_start, $date_stop){
		$resultData = [
			'date_start'=>$date_start,
			'date_stop'=>$date_stop,
		];
		switch($type_date){
			case 'current_day':
				$resultData['date_start'] = date('d.m.Y');
				$resultData['date_stop'] = date('d.m.Y');
				break;
			case 'this_week':
				$resultData['date_start'] = date('d.m.Y', strtotime('this week monday'));
				$resultData['date_stop'] = date('d.m.Y', strtotime('this week sunday'));
				break;
			case 'this_month':
				$resultData['date_start'] = date('d.m.Y', strtotime('first day of this month'));
				$resultData['date_stop'] = date('d.m.Y', strtotime('last day of this month'));
				break;
			case 'this_year':
				$resultData['date_start'] = date('d.m.Y', strtotime('first day of january '.date('Y')));
				$resultData['date_stop'] = date('d.m.Y', strtotime('last day of december '.date('Y')));
				break;
			case 'current_quarter':
				$current_quarter = ceil(date('n') / 3);
				$resultData['date_start'] = date('d.m.Y', strtotime(date('Y') . '-' . (($current_quarter * 3) - 2) . '-1'));
				$resultData['date_stop'] = date('d.m.Y', strtotime(date('Y') . '-' . (($current_quarter * 3)) . '-31'));
				break;
			case 'custom_range':
				$resultData['date_start'] = $date_start;
				$resultData['date_stop'] = $date_stop;
				break;
		}
		return $resultData;
	}
	public static function dump($array){
		echo '<pre>'.print_r($array,true).'</pre>';
	}
}
?>