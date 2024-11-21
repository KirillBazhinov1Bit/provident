<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();
//$this->addExternalCss($templateFolder."/css/style.css");
$this->addExternalCss($templateFolder."/css/bootstrap.min.css");
$this->addExternalCss($templateFolder."/css/aditional.css");
$this->addExternalCss("https://use.fontawesome.com/releases/v5.0.6/css/all.css");
$this->addExternalJS($templateFolder."/js/bootstrap.min.js");
$this->addExternalJS($templateFolder."/js/popper.min.js");

//Данные пользователя
$arUserData = bitCustomPages::getUserData($USER->GetID());

//Приводим массив меню к нормальному виду
?>
<div class="page-wrapper chiller-theme toggled">
	<nav class="sidebar-wrapper">
		<div class="sidebar-content">
			<!--
			<div class="sidebar-brand">
			   <a href="#">pro sidebar</a>
			   <div id="close-sidebar">
				  <i class="fas fa-times"></i>
			   </div>
			</div>-->
			<div class="sidebar-header">
				<div class="user-pic">
					<img class="img-responsive img-rounded" src="<?=$arUserData['PERSONAL_PHOTO_LINK']?>" alt="User picture">
				</div>
				<div class="user-info">
					<span class="user-name"><?=$arUserData['NAME']?> <strong><?=$arUserData['LAST_NAME']?></strong></span>
					<span class="user-role"><?=$arUserData['DEPARTMENT']?></span>
					<span class="user-status">
						<i class="fa fa-circle"></i>
						<span>Online</span>
					</span>
				</div>
			</div>
			<!-- sidebar-header  -->
			<!--
			<div class="sidebar-search">
			   <div>
				  <div class="input-group">
					 <input type="text" class="form-control search-menu" placeholder="Search...">
					 <div class="input-group-append">
						<span class="input-group-text">
						<i class="fa fa-search" aria-hidden="true"></i>
						</span>
					 </div>
				  </div>
			   </div>
			</div>-->
			<!-- sidebar-search  -->
			<div class="sidebar-menu">
			<ul>
				<? $is_dropdown = 0;
				foreach($arResult as $key=>$value){
					//Закрываем выпадающий список
					if($value[3]['DEPTH_LEVEL'] < 2){
						if($is_dropdown){ 
						?>
								</ul>
							</div>
						</li>
						<?
						}
						$is_dropdown = 0;
					}
					if($value[3]['IS_LABEL'] == 1){
						//Заголовок
						?>
						<li class="header-menu">
							<span><?=$value[0]?></span>
						</li>
						<?
					}elseif($value[3]['IS_PARENT'] == 1){
						//Выпадающее меню, начало
						$is_dropdown = 1; 
						?>
						<li class="sidebar-dropdown">
							<a href="" onclick="return false;">
								<i class="fa <?=$value[3]['FA_ID_LOGO']?>"></i>
								<span><?=$value[0]?></span>
							</a>
							<div class="sidebar-submenu">
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
							<a href="<?=$value[1]?>"<?if(isset($value[3]['TARGET']))echo ' target="'.$value[3]['TARGET'].'"';?>>
								<i class="fa <?=$value[3]['FA_ID_LOGO']?>"></i>
								<span><?=$value[0]?></span>
							</a>
						</li>
						<?
					}
					
				}?>
			</ul>
			</div>
		  <!-- sidebar-menu  -->
		</div>
		
		<!-- sidebar-content  -->
		<div class="sidebar-footer">
		<!--
			<a href="#">
				<i class="fa fa-bell"></i>
				<span class="badge badge-pill badge-warning notification">3</span>
			</a>
			<a href="#">
				<i class="fa fa-envelope"></i>
				<span class="badge badge-pill badge-success notification">7</span>
			</a>
			<a href="#">
				<i class="fa fa-cog"></i>
				<span class="badge-sonar"></span>
			</a>-->
			<a href="?logout=yes&<?=bitrix_sessid_get()?>">
				<i class="fa fa-power-off"></i>
			</a>
		</div>
	</nav>
	<script>
		jQuery(function($) {
			$(".sidebar-dropdown > a").click(function() {
				$(".sidebar-submenu").slideUp(200);
				if($(this).parent().hasClass("active")) {
					$(".sidebar-dropdown").removeClass("active");
					$(this).parent().removeClass("active");
				}else{
					$(".sidebar-dropdown").removeClass("active");
					$(this).next(".sidebar-submenu").slideDown(200);
					$(this).parent().addClass("active");
				}
			});

			$("#close-sidebar").click(function() {
				$(".page-wrapper").removeClass("toggled");
			});
			$("#show-sidebar").click(function() {
				$(".page-wrapper").addClass("toggled");
			});
		});
      
    </script>
</div>