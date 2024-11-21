<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();
$this->addExternalCss($templateFolder."/css/bootstrap.min.css");
$this->addExternalJS($templateFolder."/js/bootstrap.min.js");

global $APPLICATION;

if (!is_array($arResult) || empty($arResult))return;

?>
<nav class="bit-top-menu" role="navigation">
	<ul>
		<? $is_dropdown = 0;
		foreach($arResult as $key=>$value){
			//Закрываем выпадающий список
			if($value[3]['DEPTH_LEVEL'] < 2){
				if($is_dropdown){ 
				?>
					</ul>
				</li>
				<?
				}
				$is_dropdown = 0;
			}
			if($value[3]['IS_PARENT'] == 1){
				//Выпадающее меню, начало
				$is_dropdown = 1; 
				?>
				<li><a onclick="return false;"><?=$value[0]?></a>
					<ul>
				<?
			}elseif($value[3]['DEPTH_LEVEL'] == 2){
				//Выпадающее меню, ссылки
				?>
				<li>
					<a href="<?=$value[1]?>"<?if(isset($value[3]['TARGET']))echo ' target="'.$value[3]['TARGET'].'"';?>><?=$value[0]?></a>
				</li>
				<?
			}elseif($value[3]['DEPTH_LEVEL'] == 1){
				//Обычные ссылки
				?>
				<li>
					<a href="<?=$value[1]?>"<?if(isset($value[3]['TARGET']))echo ' target="'.$value[3]['TARGET'].'"';?>><?=$value[0]?></a>
				</li>
				<?
			}
			
		}?>
	</ul>
</nav>