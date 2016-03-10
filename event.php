
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

<ul data-bind="foreach: validation_errors">
    <li>
        <b data-bind="text: $data"></b>
    </li>
</ul>

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
								<input type="text" name="title" id="title" data-bind="value: title" value="" size="60" maxlength="60" tabindex="" /><br />
							</label>
						</div>
						<div style="display:inline-block;width:100%;">
							<label class="conl required" style="width: 25%;"><span><b>Inclure la date dans le titre</b></span><br />
								<input type="checkbox" id="useStartDate" data-bind="checked: useStartDate" name="useStartDate" value="" tabindex="" /><br />
							</label>
							<label class="conl required" style="width: 60%;"><span><b>Titre formaté</b></span><br />
								<input type="text" name="formatted_title" disabled="disabled" id="formatted_title" data-bind="value: formatted_title()" style="border: none;background: inherit;" value="" size="60" maxlength="60" tabindex="" /><br />
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
								<select id="maxusers" name="maxusers" data-bind="options: maxusers_list, value: maxusers"></select> 
							</label>
							<label class="conl required"><span><b>Créer un sujet associé</b></span><br />
								<input type="checkbox" id="istopicable" data-bind="checked: istopicable" name="istopicable" value="" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b>Evénement visible pour les invités ?</b></span><br />
								<input type="checkbox" id="ispublic" data-bind="checked: ispublic" name="ispublic" value="" tabindex="" /><br />
							</label>							
						</div>
						<label class="conl required"><span><b>Description</b></span><br />
							<textarea name="desc" id="desc" data-bind="value: desc" rows="20" cols="95" tabindex=""></textarea><br />
						</label>
					</div>
				</fieldset>	
				<input type="button" data-bind='click: save' id="addEvent" value="Add Event"/>
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
	    self.useStartDate = ko.observable(true);
	    self.formatted_title = ko.observable();
	    self.istopicable = ko.observable(true);
	    self.ispublic = ko.observable(false);
	    self.desc = ko.observable("");
	    self.maxusers_list = ko.observableArray([0,1,2,3,4,5,6,7,8,9,10]);
	    self.maxusers = 0;
	    self.formatted_title = ko.computed(function() {
	    	var title = self.title();
			if(title != '' && title != undefined){
				var eventStartDate = "";
				if(self.start() != undefined && self.useStartDate() == true)
					eventStartDate = ' du ' + moment(self.start(), dtDateFormat).format("dddd D MMMM");

				return title + eventStartDate;
			}
			return "";
	    }, self);
	    self.startDate = ko.observable("");
	    self.endDate = ko.observable("");

	    self.start = ko.observable("");
		self.start.subscribe(function(newValue) {
			self.startDate(new moment(newValue, dtDateFormat).unix());
			var curStartDate = self.startDate();
			var curEndDate = self.endDate();
			if(curStartDate != undefined && curStartDate > curEndDate){
				self.end(self.start());
			}
		});	    
	    self.end = ko.observable("");
		self.end.subscribe(function(newValue) {
			self.endDate(new moment(newValue, dtDateFormat).unix());			
			var curStartDate = self.startDate();
			var curEndDate = self.endDate();
			if(curEndDate != undefined && curStartDate > curEndDate){
				self.end(self.start());
			}
		});
		self.validation_errors = ko.observableArray([]);
		self.validation = function(){
			self.validation_errors.splice(0);

			if(self.formatted_title() == "")
				self.validation_errors.push("Le champ titre est vide");

			if(self.startDate() == "")
				self.validation_errors.push("Le champ début est vide");

			if(self.desc() == "")
				self.validation_errors.push('Le champ Description est vide');

		};
		self.save = function(){
			self.validation();

			var postJsonData = {
				title: self.formatted_title(),
				start: self.startDate(),
				end: self.endDate(),
				desc: self.desc(),
				ispublic: self.ispublic(),
				istopicable: self.istopicable(),
				maxusers: self.maxusers
			};
			console.log(JSON.stringify(postJsonData));
		}	    
	}

	moment.locale('fr');
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	$("#start").datepicker();	
	$("#end").datepicker();
	var dtDateFormat = "DD/mm/YYYY";

	ko.applyBindings(new AppViewModel());
</script
<?php


require PUN_ROOT.'footer.php';
?>