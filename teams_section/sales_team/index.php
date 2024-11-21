<?require_once($_SERVER["DOCUMENT_ROOT"] . '/teams_section/sales_team/header.php');?>
<?
$arUserData = \bitCustomPages::getUserData($USER->getId());
$arUserData['DATE_REGISTER_PREPARE'] = date('d.m.Y', strtotime($arUserData['DATE_REGISTER']));
$arUserData['SUPERVISOR'] = 'Alexander Kiriyatskih';
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
	
	.main_page_statistics_item_small_cell, .main_page_statistics_item_cell {
		background-color: #ffffff;
		padding: 10px;
		margin:10px;
		border-radius: 10px;
		min-height: 80px;
	}
	.main_page_statistics_item_small_cell {
		width: 150px;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}
	.main_page_statistics_item_cell {
		width: 300px;
		display: flex;
		justify-content: space-around;
		align-items: center;
	}
	.main_page_statistics_item_small_cell span {
		text-align: end;
		font-size: 12px;
	}
	.main_page_statistics_item_small_cell p {
		text-align: center;
	}
	.main_page_statistics_item_small_cell p:nth-child(2), .main_page_statistics_item_cell p:nth-child(2){
		font-weight: bold;
	}
	.main_page_statistics_row_name {
		font-weight: bold;
		text-transform: uppercase;
	}
	.main_page_statistics_data {
		display: flex;
		flex-wrap: wrap;
		background-color: #cfcfcf;
		padding: 10px;
		border-radius: 10px;
	}
	.main_page_statistics_row {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-around;
		margin-bottom: 30px;
	}
	.input-group {
		max-width: 150px;
	}
	.main_page_statistics progress{
		width: 100%;
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
		<p><b>Department: </b><?=$arUserData['DEPARTMENT']?></p>
		<p><b>Supervisor: </b><?=$arUserData['SUPERVISOR']?></p>
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
			<div class="main_page_statistics_row_cell">
				<p class="main_page_statistics_row_name">Leads</p>
				<div class="main_page_statistics_data">
					<div class="main_page_statistics_item_small_cell">
						<p>Total leads</p>
						<p style="color:red" class="total_leads">0</p>
						<progress value="0" max="100" class="total_leads"></progress>
						<div class="progress_numbers">
							<span class="total_leads min">0</span>
							<span class="total_leads max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Fresh Leads</p>
						<p style="color:green" class="fresh_leads">0</p>
						<progress value="0" max="100" class="fresh_leads"></progress>
						<div class="progress_numbers">
							<span class="fresh_leads min">0</span>
							<span class="fresh_leads max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Claimed Leads</p>
						<p style="color:red" class="claimed_leads">0</p>
						<progress value="0" max="100" class="claimed_leads"></progress>
						<div class="progress_numbers">
							<span class="claimed_leads min">0</span>
							<span class="claimed_leads max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Lost Leads</p>
						<p style="color:green" class="lost_leads">0</p>
						<progress value="0" max="100" class="lost_leads"></progress>
						<div class="progress_numbers">
							<span class="lost_leads min">0</span>
							<span class="lost_leads max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Total Junked Leads</p>
						<p style="color:green" class="junked_leads">0</p>
						<progress value="0" max="100" class="junked_leads"></progress>
						<div class="progress_numbers">
							<span class="junked_leads min">0</span>
							<span class="junked_leads max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell"> <!--main_page_statistics_item_cell-->
						<p>Retained Leads</p>
						<p style="color:red" class="retained_leads">0</p>
						<progress value="0" max="100" class="retained_leads"></progress>
						<div class="progress_numbers">
							<span class="retained_leads min">0</span>
							<span class="retained_leads max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Conversion Ratio</p>
						<p style="color:green" class="conversion_ratio">0</p>
						<progress value="0" max="100" class="conversion_ratio"></progress>
						<div class="progress_numbers">
							<span class="conversion_ratio min">0</span>
							<span class="conversion_ratio max">100</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="main_page_statistics_row">
			<div class="main_page_statistics_row_cell">
				<p class="main_page_statistics_row_name">Listings</p>
				<div class="main_page_statistics_data">
					<div class="main_page_statistics_item_small_cell">
						<p>Active Listings</p>
						<p style="color:green" class="active_listings">0</p>
						<progress value="0" max="100" class="active_listings"></progress>
						<div class="progress_numbers">
							<span class="active_listings min">0</span>
							<span class="active_listings max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Off-Market Listings</p>
						<p style="color:red" class="off_market_listings">0</p>
						<progress value="0" max="100" class="off_market_listings"></progress>
						<div class="progress_numbers">
							<span class="off_market_listings min">0</span>
							<span class="off_market_listings max">100</span>
						</div>
					</div>
				</div>
			</div>
			<div class="main_page_statistics_row_cell">
				<p class="main_page_statistics_row_name">Deals</p>
				<div class="main_page_statistics_data">
					<div class="main_page_statistics_item_small_cell">
						<p>Deals Closed</p>
						<p style="color:green" class="deals_closed">0</p>
						<progress value="0" max="100" class="deals_closed"></progress>
						<div class="progress_numbers">
							<span class="deals_closed min">0</span>
							<span class="deals_closed max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Sale Value</p>
						<p style="color:red" class="sale_value">0</p>
						<progress value="0" max="100" class="sale_value"></progress>
						<div class="progress_numbers">
							<span class="sale_value min">0</span>
							<span class="sale_value max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Gross Commission</p>
						<p style="color:green" class="gross_commission">0</p>
						<progress value="0" max="100" class="gross_commission"></progress>
						<div class="progress_numbers">
							<span class="gross_commission min">0</span>
							<span class="gross_commission max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Commission outcome</p>
						<p style="color:green" class="commission_outcome">0</p>
						<progress value="0" max="100" class="commission_outcome"></progress>
						<div class="progress_numbers">
							<span class="commission_outcome min">0</span>
							<span class="commission_outcome max">100</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="main_page_statistics_row"> 
			<div class="main_page_statistics_row_cell">
				<p class="main_page_statistics_row_name">Activities</p>
				<div class="main_page_statistics_data">
					<div class="main_page_statistics_item_small_cell">
						<p>Total Calls</p>
						<p style="color:red" class="total_calls">0</p>
						<progress value="0" max="100" class="total_calls"></progress>
						<div class="progress_numbers">
							<span class="total_calls min">0</span>
							<span class="total_calls max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Total Emails</p>
						<p style="color:green" class="total_emails">0</p>
						<progress value="0" max="100" class="total_emails"></progress>
						<div class="progress_numbers">
							<span class="total_emails min">0</span>
							<span class="total_emails max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Total Activities</p>
						<p style="color:red" class="total_activities">0</p>
						<progress value="0" max="100" class="total_activities"></progress>
						<div class="progress_numbers">
							<span class="total_activities min">0</span>
							<span class="total_activities max">100</span>
						</div>
					</div>
					<div class="main_page_statistics_item_small_cell">
						<p>Total Meetings</p>
						<p style="color:red" class="total_meetings">0</p>
						<progress value="0" max="100" class="total_meetings"></progress>
						<div class="progress_numbers">
							<span class="total_meetings min">0</span>
							<span class="total_meetings max">100</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		let date_start = '<?=date('d.m.Y')?>';
		let date_stop = '<?=date('d.m.Y')?>';
		let type_date = 'current_day';
		
		//Date input
		$('#date_start').datetimepicker({useCurrent: true, format: 'DD.MM.YYYY'});
		$('#date_stop').datetimepicker({
            useCurrent: true, format: 'DD.MM.YYYY'
        });
		 $("#date_start").on("dp.change", function (e) {
            $('#date_stop').data("DateTimePicker").minDate(e.date);
			//date_start = e.date;
        });
        $("#date_stop").on("dp.change", function (e) {
            $('#date_start').data("DateTimePicker").maxDate(e.date);
			//date_stop = e.date.getDate();
			//console.log(date_stop);
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
				'GET_DASHBOARD_SALE':{
					USER_ID:<?=$USER->getId()?>,
					TYPE_DATE:type_date,
					DATE_START:$('#date_start_input').val(),
					DATE_STOP:$('#date_stop_input').val(),
				}
			};
			callAjax(filter, function(result){
				console.log(result);
				$.each(result.items, function(key, value){
					//Меняем значение
					$('p.'+key).text(value.value);
					//Меняем цвет
					$('p.'+key).css('color', value.color);
					//Позиция трекбара
					$('progress.'+key).val(value.value);
					//Макс трекбара
					$('progress.'+key).attr('max', value.progressbar_max);
					//Мин трекбара в строку
					$('span.min.'+key).text(value.progressbar_min);
					//Макс трекбара в строку
					$('span.max.'+key).text(value.progressbar_max);
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
<?require_once($_SERVER["DOCUMENT_ROOT"] . '/teams_section/sales_team/footer.php');?>