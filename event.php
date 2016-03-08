
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui.structure.min.css">
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui.theme.min.css">
<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'header.php';

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view'], false, '403 Forbidden');

?>



<div id="postform" class="blockform">
	<h2><span><?php echo $action ?></span></h2>
	<div class="box">
		<?php echo $form."\n" ?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Write message legend'] ?></legend>
					<div class="infldset txtarea">
						<div style="display:inline-block;width:100%;">
							<label class="conl required"><span><b>Titre</b></span><br />
								<input type="text" name="title" id="title" value="" size="100" maxlength="80" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b>Titre formatté</b></span><br />
								<input type="text" name="formatted_title" disabled="disabled" id="formatted_title" style="border: none;background: inherit;" value="" size="100" maxlength="80" tabindex="" /><br />
							</label><br />
						</div>
						<div style="display:inline-block;width:100%;">
							<label class="conl required"><span><b>Date de démarrage</b></span><br />
								<input type="text" id="start" name="start" value="" size="25" maxlength="25" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b>Date de fin</b></span><br />
								<input type="text" id="end" name="end" value="" size="25" maxlength="25" tabindex="" /><br />
							</label>
						</div>
						<div style="display:inline-block;width:100%;">
							<label class="conl required"><span><b>Nombre de places</b></span><br />
								<select id="maxusers" name="maxusers" /> 
									<option>0</option>       
									<option>1</option>       
									<option>2</option>       
									<option>3</option>             
								</select>
							</label>
							<label class="conl required"><span><b>Créer un sujet associé</b></span><br />
								<input type="checkbox" id="istopicable" name="istopicable" value="" tabindex="" /><br />
							</label>
						</div>
						<label class="conl required"><span><b>Description</b></span><br />
							<textarea name="desc" id="desc" rows="20" cols="95" tabindex=""></textarea><br />
						</label>
					</div>
				</fieldset>	
				<input type="button" id="addEvent" value="Add Event"/>
			</div>
	</div>
</div>


<script src="portal/js/jquery-2.2.1.min.js"></script>
<script src="portal/js/moment.js"></script>
<script src="portal/js/fr.js"></script>
<script src="portal/js/jquery-ui.min.js"></script>
<script src="portal/js/datepicker-fr.js"></script>
<script>
	var startDate, endDate;

	moment.locale('fr');
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	$("#start").datepicker();	
	$("#end").datepicker();
	$("#start").change(function(){
		startDate = $(this).datepicker("getDate");
		if(endDate == undefined || moment(endDate).isAfter(startDate) == false){
			endDate = $(this).datepicker("getDate");
			$("#end").val($(this).val());
			endDate = startDate;
		}
		updateTitleFormatted();
	});
	$('#end').change(function(){
		if(startDate != undefined && moment(startDate).isAfter(endDate) == true){
			endDate = startDate;
			$("#end").val($("#start").val());
		}else{
			endDate = $(this).datepicker("getDate");			
		}
	})
	$("#title").keyup(function(){
		updateTitleFormatted();
	});
	$("#addEvent").click(function(){
		launchEventCreation();
	});
	var updateTitleFormatted = function(){
		if($("#title").val() != ''){
			var eventStartDate = moment(startDate).format("dddd D MMMM");
			$('#formatted_title').val($("#title").val() + ' du ' + eventStartDate);
		}else{
			$('#formatted_title').val('');			
		}		
	};
	var launchEventCreation = function(){
		var eventOptions = {
			action: 'addEvent',
			title: $('#formatted_title').val(),
			message: $('#desc').val(),
			isTopicAble: $('#istopicable').is(':checked'),
			maxusers: $('#maxusers').val(),
			start: moment(startDate).unix(),
			end: moment(endDate).unix()
		};
		console.log(eventOptions);
		$.get( "json_gateway.php", eventOptions ).done(function( data ) {
			alert( "Data Loaded: " + JSON.stringify(data) );
		});
	}
</script
<?php


require PUN_ROOT.'footer.php';
?>