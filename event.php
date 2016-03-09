
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
								<input type="text" name="title" id="title" data-bind="value: title" value="" size="100" maxlength="80" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b>Titre formatté</b></span><br />
								<input type="text" name="formatted_title" disabled="disabled" id="formatted_title" data-bind="value: formatted_title()" style="border: none;background: inherit;" value="" size="100" maxlength="80" tabindex="" /><br />
							</label><br />
						</div>
						<div style="display:inline-block;width:100%;">
							<label class="conl required"><span><b>Date de démarrage</b></span><br />
								<input type="text" id="start" name="start" data-bind="value: start" value="" size="25" maxlength="25" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b>Date de fin</b></span><br />
								<input type="text" id="end" name="end" data-bind="value: end" value="" size="25" maxlength="25" tabindex="" /><br />
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
								<input type="checkbox" id="istopicable" data-bind="value: istopicable" name="istopicable" value="" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b>Evénement visible pour les invités ?</b></span><br />
								<input type="checkbox" id="ispublic" data-bind="value: ispublic" name="ispublic" value="" tabindex="" /><br />
							</label>							
						</div>
						<label class="conl required"><span><b>Description</b></span><br />
							<textarea name="desc" id="desc" data-bind="value: desc" rows="20" cols="95" tabindex=""></textarea><br />
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
<script src="portal/js/knockout-3.4.0.js"></script>

<script>
	function AppViewModel() {
		var self = this;

	    self.title = ko.observable();
	    self.formatted_title = ko.observable();
	    self.istopicable = ko.observable();
	    self.ispublic = ko.observable();
	    self.desc = ko.observable();
	    self.formatted_title = ko.computed(function() {
	    	var title = self.title();
			if(title != '' && title != undefined){
				var eventStartDate = moment(self.start()).format("dddd D MMMM");
				return title + ' du ' + eventStartDate;
			}
			return "";
	    }, self);
	    self.start = ko.computed({
	    	read: function(){
	    		if(self.startDate != undefined){
	    			return moment(self.startDate).format(dtDateFormat);	    			
	    		}else{
	    			return "";
	    		}
	    	},
	    	write: function(value){
	    		if(self.startdate != undefined && moment(self.startDate).isAfter(self.endDate) == true){
	    			self.startDate(self.endDate);
	    		}else{
	    			self.startDate(moment(value, dtDateFormat));
	    		}
	    	},
			owner: self
	    });
	    self.end = ko.computed({
	    	read: function(){
	    		if(self.endDate != undefined){
	    			return moment(self.endDate).format(dtDateFormat);	    			
	    		}else{
	    			return "";
	    		}
	    	},
	    	write: function(value){
	    		if(self.enddate != undefined && moment(self.endDate).isBefore(self.startDate) == true){
	    			self.endDate(self.startDate);
	    		}else{
	    			self.endDate(moment(value, dtDateFormat));
	    		}
	    	},
			owner: self
	    });
	    self.startDate = ko.observable();
	    self.endDate = ko.observable();

		self.title.subscribe(function(newValue) {
		    console.log(newValue);
		});	    
	}

	moment.locale('fr');
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	$("#start").datepicker();	
	$("#end").datepicker();
	var dtDateFormat = $("#start").datepicker("option", "dateFormat");

	ko.applyBindings(new AppViewModel());
</script
<?php


require PUN_ROOT.'footer.php';
?>