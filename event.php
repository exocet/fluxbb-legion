
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui.structure.min.css">
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui.theme.min.css">
<link rel="stylesheet" href="portal/css/jquery.qtip.min.css" type="text/css" media="all" />

<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'header.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/event.php';

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view'], false, '403 Forbidden');
?>
			<!-- ko if: saveResponse -->
			<div class="informationMessage">
				<span id="saveResponse" data-bind="text: saveResponse" title="saveResponse"></span>
			</div>
			<!-- /ko -->
			<div data-bind="foreach: { data: errors, as: 'error' }" class="validationMessage">
				<p data-bind="text: error"></p>
			</div>
<div id="postform" class="blockform">
	<h2><span><?php echo $action ?></span></h2>
	<div class="box">
		<?php echo $form."\n" ?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_event['Create message legend'] ?></legend>
					<div class="infldset txtarea">
						<div style="display:inline-block;width:100%;">
							<label class="conl required"><span><b><?php echo $lang_event['title'] ?></b></span><br />
								<input type="text" name="title" id="title" title="" data-bind="value: title" value="" size="60" maxlength="60" tabindex="" /><br />
							</label>
						</div>
						<div style="display:inline-block;width:100%;">
							<label class="conl required" style="width: 30%;"><span><b><?php echo $lang_event['useStartDate'] ?></b></span><br />
								<input type="checkbox" id="useStartDate" data-bind="checked: useStartDate" name="useStartDate" value="" tabindex="" /><br />
							</label>
							<label class="conl required" style="width: 60%;"><span><b><?php echo $lang_event['formatted_title'] ?></b></span><br />
								<input type="text" name="formatted_title" disabled="disabled" id="formatted_title" data-bind="value: formatted_title()" style="border: none;background: inherit;" value="" size="60" maxlength="60" tabindex="" /><br />
							</label><br />
						</div>
						<div style="display:inline-block;width:100%;">
							<label class="conl required"><span><b><?php echo $lang_event['startDate'] ?></b></span><br />
								<input type="text" id="start" name="start" data-bind="value: start" value="" size="25" maxlength="25" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b><?php echo $lang_event['endDate'] ?></b></span><br />
								<input type="text" id="end" name="end" data-bind="value: end" value="" size="25" maxlength="25" tabindex="" /><br />
							</label>
						</div>
						<div style="display:inline-block;width:100%;">
							<label class="conl required"><span><b><?php echo $lang_event['maxusers'] ?></b></span><br />
								<select id="maxusers" name="maxusers" data-bind="options: maxusers_list, optionsText: 'value', optionsValue: 'id', value: maxusers"></select> 
							</label>
							<label class="conl required"><span><b><?php echo $lang_event['istopicable'] ?></b></span><br />
								<input type="checkbox" id="istopicable" data-bind="checked: istopicable, disable: hasId" name="istopicable" value="" tabindex="" /><br />
							</label>
							<label class="conl required"><span><b><?php echo $lang_event['ispublic'] ?></b></span><br />
								<input type="checkbox" id="ispublic" data-bind="checked: ispublic" name="ispublic" value="" tabindex="" /><br />
							</label>							
						</div>
						<label class="conl required"><span><b><?php echo $lang_event['desc'] ?></b></span><br />
							<textarea name="desc" id="desc" data-bind="value: desc" rows="20" cols="95" tabindex=""></textarea><br />
						</label>
					</div>
				</fieldset>	
				<p class="buttons">
					<input type="button" data-bind='click: save' id="saveEvent" value="<?php echo $lang_event['saveevent'] ?>"/>
				</p>
			</div>
	</div>
</div>
<style>
	div.validationMessage {
		color: red;
		text-align: center;
		margin-left: auto;
		margin-right: auto;
		width: 300px;
		position: relative;
	}
	div.informationMessage {
		color: green;
		text-align: center;
		margin-left: auto;
		margin-right: auto;
		width: 300px;
		position: relative;
		height: : 100px;
	}	
</style>
<script src="portal/js/jquery-2.2.1.min.js"></script>
<script src="portal/js/moment.js"></script>
<script src="portal/js/fr.js"></script>
<script src="portal/js/jquery-ui.min.js"></script>
<script src="portal/js/datepicker-fr.js"></script>
<script src="portal/js/knockout-3.4.0.js"></script>
<script src="portal/js/knockout.validation.min.js"></script>
<script type="text/javascript" src="portal/js/kovalidation/fr-FR.js"></script>
<script src="portal/js/jquery.qtip.min.js"></script>

<script>
	var id;
	//Conversion des variables php en variables js
	<?php 
		echo "var langArray = ".json_encode($lang_event).";\n";
		echo "var kovalidation_language = '".$lang_event['kovalidationlang']."';\n";
		if (isset($_GET['id'])){
			$id = $_GET['id'];
			echo "id = '".$id."';\n";
		}
	?>

	function AppViewModel() {
		var self = this;

		self.id = ko.observable("");
	    self.title = ko.observable().extend({ requiredCustom: langArray['title'] });
	    self.useStartDate = ko.observable(true);
	    self.formatted_title = ko.observable();
	    self.istopicable = ko.observable(true);
	    self.ispublic = ko.observable(false);
	    self.desc = ko.observable("").extend({ requiredCustom: langArray['desc'] });
	    self.maxusers_list = ko.observableArray([
	    	{id:0, value:0},
	    	{id:1, value:1},
	    	{id:2, value:2},
	    	{id:3, value:3},
	    	{id:4, value:4},
	    	{id:5, value:5},
	    	{id:6, value:6},
	    	{id:7, value:7}]);
	    self.maxusers = ko.observable(0);
	    self.formatted_title = ko.computed(function() {
	    	var title = self.title();
			if(title != '' && title != undefined){
				var eventStartDate = "";
				var eventStartMoment = moment(self.start(), dtDateFormat).format("dddd D MMMM");
				if(self.start() != "" && self.useStartDate() == true && title.indexOf(eventStartMoment) < 0)
					eventStartDate = ' du ' + eventStartMoment;

				return title + eventStartDate;
			}
			return "";
	    }, self);
	    self.startDate = ko.observable("");
	    self.endDate = ko.observable("");
	    self.start = ko.observable("").extend({ requiredCustom: langArray['start'] });
		self.start.subscribe(function(newValue) {
			self.startDate(new moment(newValue, dtDateFormat).unix());
			var curStartDate = self.startDate();
			var curEndDate = self.endDate();
			if(curStartDate != undefined && curStartDate > curEndDate){
				self.end(self.start());
			}
		});	    
	    self.end = ko.observable("").extend({ requiredCustom: langArray['end'] });
		self.end.subscribe(function(newValue) {
			self.endDate(new moment(newValue, dtDateFormat).unix());			
			var curStartDate = self.startDate();
			var curEndDate = self.endDate();
			if(curEndDate != undefined && curStartDate > curEndDate){
				self.end(self.start());
			}
		});
		self.hasId = ko.computed(function(){
			if (self.id != undefined && self.id != "")
				return true;

			return false;
		})
		self.saveResponse = ko.observable("");
		self.errors = ko.validation.group([self.title, self.desc, self.start]);
		self.validated = ko.computed(function(){
			if (self.errors().length > 0){
				return false;
			}else{
				return true;
			}
		});

		self.load = function(){
			if(id != undefined){
				$.get( "json_gateway.php", { id: id, action: "getEvent" } ).done(function( data ) {
					console.log( "Data Loaded: " + JSON.stringify(data) );
					//Do mapping
					self.id(data.id);
					self.title(data.title);
					self.startDate(data.start);
					self.endDate(data.end);
					self.start(new moment.unix(data.start).format(dtDateFormat));
					self.end(new moment.unix(data.end).format(dtDateFormat));
					self.desc(data.event_desc);
					self.ispublic(data.is_public  != undefined ? true : false);
					self.istopicable(data.istopicable != undefined ? true : false);
					self.maxusers(data.max_users);
					self.istopicable(data.topic_id != undefined ? true : false);					
				});
			}
		};

		self.save = function(){
			self.saveResponse("");
			self.errors.showAllMessages(true);
			//self.errors = ko.validation.group([self.title, self.desc, self.start]);
			console.log("errors : " + self.errors().length);
			if(self.errors().length === 0){
				var postJsonData = {
					action: 'saveEvent',
					id: self.id(),
					title: self.formatted_title(),
					start: self.startDate(),
					end: self.endDate(),
					desc: self.desc(),
					ispublic: self.ispublic(),
					istopicable: self.istopicable(),
					maxusers: self.maxusers()
				};
				console.log( "Data Sent : " + JSON.stringify(postJsonData));
				$.get( "json_gateway.php", postJsonData, function( data ) {
					self.saveResponse(data.message);
					console.log(self.saveResponse());
				});
			}
		};
	}

	moment.locale('fr');
	$.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
	$("#start").datepicker();	
	$("#end").datepicker();
	var dtDateFormat = "DD/MM/YYYY";

	ko.validation.init({
		messagesOnModified: true,
		decorateInputElement: true,
		insertMessages: false
	});
	ko.validation.rules['requiredCustom'] = {
	    validator: function(val, otherVal) {
	        return (val != "" && val != undefined);
	    },
	    message: 'The field {0} is required.'
	};
	ko.validation.registerExtenders();
	ko.validation.locale(kovalidation_language);
	ko.validation.init();
	var eventViewModel = new AppViewModel();
	ko.applyBindings(eventViewModel);
	eventViewModel.load();

    /*$(":input").qtip({
		position: {
			my: 'center left',
			at: 'center right'
		},
		style: {
	        classes: 'qtip-dark qtip-tipped',
	        tip: {
	            corner: 'left center'
	        }	        
	    }
    });	*/	
</script>
<?php


require PUN_ROOT.'footer.php';
?>