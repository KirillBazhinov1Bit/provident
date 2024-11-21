<?require_once($_SERVER["DOCUMENT_ROOT"] . '/teams_section/seo_team/header.php');?>
<?
$arUserData = \bitCustomPages::getUserData($USER->getId());
$arUserData['DATE_REGISTER_PREPARE'] = date('d.m.Y', strtotime($arUserData['DATE_REGISTER']));
//$arUserData['SUPERVISOR'] = 'Alexander Kiriyatskih';
//\bitCustomPages::dump($arUserData);
?>
<style>
	.main_page_sales {
		display: flex;
		justify-content: space-between;
	}
	.main_page_sales p {
		margin: 0;
	}
	.main_page_profile {
		display: flex;
		flex-direction: column;
		align-items: center;
		min-width: 350px;
		margin-top: 30px;
	}
	.main_page_profile_img {
		max-width: 250px;
		border-radius: 120px;
		margin: 20px 0;
	}
	.main_page_statistics {
		display: flex;
		flex-direction: column;
		width: 100%;
	}
	.main_page_statistics_table {
		width: 100%;
		border: none;
		margin-bottom: 20px;
	}
	.main_page_statistics_table thead th {
		font-weight: bold;
		text-align: left;
		border: none;
		padding: 10px 15px;
		background: #d8d8d8;
		font-size: 14px;
		cursor: pointer;
	}
	.main_page_statistics_table thead tr th:first-child {
		border-radius: 8px 0 0 8px;
	}
	.main_page_statistics_table thead tr th:last-child {
		border-radius: 0 8px 8px 0;
	}
	.main_page_statistics_table tbody td {
		text-align: left;
		border: none;
		padding: 10px 15px;
		font-size: 14px;
		vertical-align: top;
	}
	.main_page_statistics_table tbody tr:nth-child(even){
		background: #ebebeb;
	}
	.main_page_statistics_table tbody tr td:first-child {
		border-radius: 8px 0 0 8px;
		font-weight: bold;
	}
	.main_page_statistics_table tbody tr td:last-child {
		border-radius: 0 8px 8px 0;
	}
	.main_page_statistics_row {
		display: flex;
	}
	.main_page_statistics_row_section {
		width:100%;
	}
	.main_page_statistics_row_section:not(:nth-child(1)) {
		margin-left: 20px;
	}
	.input-group {
		max-width: 150px;
	}
	.main_page_statistics_table thead th:hover {
		color: #8b8b8b;
	}
	.progress_numbers {
		display: flex;
		justify-content: space-between;
	}
	.date_period {
		display:none;
		margin-top: 10px;
	}
	#date_controler {
		padding: 5px;
		border-radius: 5px;
		margin-top: 10px;
	}
	#date_controler:focus-visible {
		outline:none;
	}
</style>
<div class="main_page_sales">
	<div class="main_page_profile">
		<p style="text-align: center;"><b>Hello, <?=$arUserData['NAME']?></b></p>
		<p><img class="main_page_profile_img" src="<?=$arUserData['PERSONAL_PHOTO_LINK']?>" alt="User picture"></p>
		<p><b>Joining Date: </b><?=$arUserData['DATE_REGISTER_PREPARE']?></p>
		<div>
			<select class="form-select" id="date_controler">
				<option selected value="current_day">Сurrent day</option>
				<option value="this_week">This week</option>
				<option value="this_month">This month</option>
				<option value="this_year">This year</option>
				<option value="current_quarter">Сurrent quarter</option>
				<option value="custom_range">Custom range</option>
			</select>
		</div>
		<div class="date_period">
			<span><b>Period from: </b></span>
			<div class="input-group" id="date_start">
				<input type="text" class="form-control" id="date_start_input"/>
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-calendar"></span>
				</span>
			</div>
			<span><b>Period to: </b></span>
			<div class="input-group" id="date_stop">
				<input type="text" class="form-control" id="date_stop_input"/>
				<span class="input-group-addon">
					<span class="glyphicon glyphicon-calendar"></span>
				</span>
			</div>
		</div>
		<button class="ui-btn ui-btn-success update_dashboard" style="margin-top: 20px;">Apply</button>
	</div>
	<div class="main_page_statistics">
		<div class="main_page_statistics_row">
			<div class="main_page_statistics_row_section">
				<h3>Leads</h3>
				<table class="main_page_statistics_table sortable" id="leads_table">
					<thead>
						<tr>
							<th>Agent</th>
							<th>Total Leads</th>
							<th>Fresh Leads</th>
							<th>Claimed Leads</th>
							<th>Lost Leads</th>
							<th>Junked Leads</th>
							<th>Retained Leads</th>
							<th>Conversion</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
		<div class="main_page_statistics_row">
			<div class="main_page_statistics_row_section">
				<h3>Deals</h3>
				<table class="main_page_statistics_table sortable" id="deals_table">
					<thead>
						<tr>
							<th>Agent</th>
							<th>Deals Closed</th>
							<th>Sale Value</th>
							<th>Gross Commission</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<div class="main_page_statistics_row_section">
				<h3>Listings</h3>
				<table class="main_page_statistics_table sortable" id="listings_table">
					<thead>
						<tr>
							<th>Agent</th>
							<th>Active Listings</th>
							<th>Off-Market Listings</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
		<div class="main_page_statistics_row">
			<div class="main_page_statistics_row_section">
				<h3>Activities</h3>
				<table class="main_page_statistics_table sortable" id="activities_table">
					<thead>
						<tr>
							<th>Agent</th>
							<th>Total Calls</th>
							<th>Total Emails</th>
							<th>Total Activities</th>
							<th>Total Meetings</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script src="js/sorttable.js"></script>
<script>
	$(document).ready(function(){
		let date_start = '<?=date('d.m.Y')?>';
		let date_stop = '<?=date('d.m.Y')?>';
		let type_date = 'current_day';
		
		//Date input
		$('#date_start').datetimepicker({format: 'YYYY-MM-DD'});
		$('#date_stop').datetimepicker({
            useCurrent: false, format: 'YYYY-MM-DD'
        });
		 $("#date_start").on("dp.change", function (e) {
            $('#date_stop').data("DateTimePicker").minDate(e.date);
        });
        $("#date_stop").on("dp.change", function (e) {
            $('#date_start').data("DateTimePicker").maxDate(e.date);
        });
		
		//Выбор варианта даты
		$('#date_controler').on('change', function(){
			type_date = $(this).val();
			if($(this).val() == 'custom_range'){
				$('.date_period').show();
			}else{
				$('.date_period').hide();
			}
		});
		
		writeDataDashboard(date_start, date_stop, function(){
				
		});
			
		//Обновляем дашборд
		$('.update_dashboard').on('click', function(){
			writeDataDashboard(date_start, date_stop, function(){
				
			});
		});
		
		
		//Загрузка данных дашборда
		function writeDataDashboard(date_start, date_stop, callback){
			let filter = {
				'GET_DASHBOARD_SEO':{
					USER_ID:<?=$USER->getId()?>,
					TYPE_DATE:type_date,
					DATE_START:$('#date_start_input').val(),
					DATE_STOP:$('#date_stop_input').val(),
				}
			};
			callAjax(filter, function(result){
				//console.log(result);
				$.each(result.items, function(key, value){
					let table_container = $('#'+key);
					if(table_container.length){
						let html = '<thead><tr>';
						//Формируем заголовок таблицы
						$.each(value.headers, function(key_head, value_head){
							html += '<th>'+value_head+'</th>';
						});
						html += '</thead></tr><tbody>';
						//Формируем тело таблицы
						$.each(value.items, function(key_row, value_row){
							html += '<tr><td><a href="/company/personal/user/'+value_row.user.id+'/" target="_blank">'+value_row.user.name+'</a></td>';
							$.each(value_row.items, function(key_row_item, value_row_item){
								html += '<td>'+value_row_item.value+'</td>';
							});
							html += '</tr>';
						});
						html += '</tbody>';
						table_container.empty();
						table_container.append(html);
					}
				});
				return callback(result);
			});
		}
		//Отправка ajax
		function callAjax(data, callback){
			$.ajax({
				url: 'ajax.php',
				method: 'post',
				dataType: 'json',
				data: data,
				success: function(response){
					return callback(response);
				},
				error: function(response){
					console.error(response);
					return callback(response);
				},
			});
		}
	});
</script>
<?require_once($_SERVER["DOCUMENT_ROOT"] . '/teams_section/seo_team/footer.php');?>