
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.structure.min.css">
<link rel="stylesheet" type="text/css" href="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.theme.min.css">
<link rel="stylesheet" href="portal/css/jquery.qtip.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.theme.min.css" type="text/css" media="all" />

<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'header.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/event.php';

if ($pun_user['g_read_board'] == '0')
	message($lang_common['No view'], false, '403 Forbidden');
?>
<div id="postform" class="blockform">
	<h2><span><?php echo $action ?></span></h2>
	<div class="box">
		<?php echo $form."\n" ?>
			<div class="inform">
				<fieldset >				
					<legend><?php echo $lang_event['Create message legend'] ?></legend>
					<div class="infldset txtarea">
						<div>
							<label class="conl required" for="title"><span style="display: inline-block;width: 150px;"><b><?php echo $lang_event['title'] ?></b></span>
								<input type="text" name="title" id="title" title="" data-bind="value: title" value="" size="70" maxlength="60" tabindex="" />
							</label>
						</div>
						<div>
							<label class="conl required" for="start"><span style="display: inline-block;width: 150px;"><b><?php echo $lang_event['start'] ?></b></span>
								<input type="text" id="start" name="start" data-bind="value: start" value="" size="25" maxlength="25" tabindex="" />
							</label>
							<label class="conl required" for="end" style="margin-left:22px;"><span style="display: inline-block;width: 85px;"><b><?php echo $lang_event['end'] ?></b></span>
								<input type="text" id="end" name="end" data-bind="value: end" value="" size="25" maxlength="25" tabindex="" />
							</label>
						</div>
						<div>
							<label class="conl required" style="display:inline-block;margin-right: 400px;"><span style="display: inline-block;width: 150px;"><b><?php echo $lang_event['maxusers'] ?></b></span>
								<select id="maxusers" name="maxusers" data-bind="options: maxusers_list, optionsText: 'value', optionsValue: 'id', value: maxusers"></select> 
							</label>
						</div>
						<div>
							<label class="conl required"><span style="display: inline-block;width: 150px;"><b><?php echo $lang_event['forum'] ?></b></span>
								<select id="forum" name="forum" data-bind="options: forum_list, optionsText: 'value', optionsValue: 'id', value: forum"></select> 
							</label>						
							<label class="conl required"><span style="display: inline-block;width: 150px;margin-left: 100px;" title=<?php echo '"'.$lang_event['newtopic_explain'].'"' ?>><b><?php echo $lang_event['newtopic'] ?></b></span>
								<input type="checkbox" id="newtopic" data-bind="checked: newtopic, disable: hasId" name="newtopic" value="" tabindex="" />
							</label>
							<label class="conl required" for="title_formatted" ><span style="display: inline-block;width: 150px;"><b><?php echo $lang_event['title_formatted'] ?></b></span>
								<input type="text" name="title_formatted" disabled="disabled" id="title_formatted" data-bind="value: title_formatted()" style="background: inherit;border: hidden;" value="" size="70" maxlength="60" tabindex="" />
							</label>
						</div>
						<label class="conl required"><span style="display: inline-block;width: 150px;margin-bottom: 10px;"><b><?php echo $lang_event['desc'] ?></b></span>
							<textarea name="desc" id="desc" data-bind="value: event_desc" rows="20" cols="95" tabindex=""></textarea>
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
	.validationElement {
		border-color: red;
	}
	div.validationMessage {
		color: red;
		text-align: center;
		margin-left: auto;
		margin-right: auto;
		width: 300px;
		position: relative;
		align: right;
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
<!--<script type="text/javascript" src="portal/js/datepicker-fr.js"></script>-->
<script type="text/javascript" src="portal/js/jquery-2.2.1.min.js"></script>
<script type="text/javascript" src="portal/js/moment.js"></script>
<script type="text/javascript" src="portal/js/fr.js"></script>
<script type="text/javascript" src="portal/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="portal/js/knockout-3.4.0.js"></script>
<script type="text/javascript" src="portal/js/knockout.validation.min.js"></script>
<script type="text/javascript" src="portal/js/kovalidation/fr-FR.js"></script>
<script type="text/javascript" src="portal/js/noty/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="portal/js/noty/themes/relax.js"></script>
<script type="text/javascript" src="portal/js/noty/themes/default.js"></script>
<script type="text/javascript" src="portal/js/jquery-ui-i18n.js"></script>
<script type="text/javascript" src="portal/js/jquery.qtip.min.js"></script>

<script>
	var id;
	<?php 
		//Conversion des variables php en variables js
		echo "var langArray = ".json_encode($lang_event).";\n";
		echo "var kovalidation_language = '".$lang_event['kovalidationlang']."';\n";
		if (isset($_GET['id'])){
			$id = $_GET['id'];
			echo "id = '".$id."';\n";
		}
		//Récupération des forums associés aux nouveaux événements
		echo "var o_topic_events_rpg = '".$pun_config['o_topic_events_rpg']."';\n";
		echo "var o_topic_events_ttp = '".$pun_config['o_topic_events_ttp']."';\n";
		echo "var o_topic_events_ttp = '".$pun_config['o_topic_events_other']."';\n";

		$events_forums = [];
		$query = "SELECT id, forum_name as value from ".$db->prefix."forums where id in (".$pun_config['o_topic_events_rpg'].", ".$pun_config['o_topic_events_ttp'].", ".$pun_config['o_topic_events_other'].") order by id";
		$result = $db->query($query) or $db->error('Unable to fetch forums list');
		while($event_forum = $db->fetch_assoc($result))
		{
			array_push($events_forums, $event_forum);
		}
		echo "var events_forums = ".json_encode($events_forums).";\n";
		//echo "events_forums.reverse();";
	?>
	function dialogue(content, title) {
	    $('<div />').qtip({
	        content: {
	            text: content,
	            title: title
	        },
	        position: {
	            my: 'center', at: 'center',
	            target: $(window)
	        },
	        show: {
	            ready: true,
	            modal: {
	                on: true,
	                blur: false
	            }
	        },
	        hide: false,
	        style: 'dialogue',
	        events: {
	            render: function(event, api) {
	                $('button', api.elements.content).click(function(e) {
	                    api.hide(e);
	                });
	            },
	            hide: function(event, api) { api.destroy(); }
	        },
	        style: {
	        	widget: true
	        }
	    });
	}
	function AppViewModel() {
		var self = this;

		self.id = ko.observable("");
	    self.title = ko.observable().extend({ requiredCustom: langArray['title'] });
	    self.useStartDate = ko.observable(true);
	    self.title_formatted = ko.observable();
	    self.newtopic = ko.observable(false);
	    self.event_desc = ko.observable("").extend({ requiredCustom: langArray['desc'] });
	    self.maxusers_list = ko.observableArray([
	    	{id:0, value:0},
	    	{id:1, value:1},
	    	{id:2, value:2},
	    	{id:3, value:3},
	    	{id:4, value:4},
	    	{id:5, value:5},
	    	{id:6, value:6},
	    	{id:7, value:7}]);
	    self.forum_list = ko.observableArray(events_forums);
	    self.maxusers = ko.observable(0);
	    self.forum = ko.observable(4);
	    self.title_formatted = ko.computed(function() {
	    	var title = self.title();
			if(title != '' && title != undefined && self.forum() != 0){
				var eventStartMoment = moment(self.start(), dtDateFormat).format("dddd D MMMM");
				if(self.start() != "" && self.useStartDate() == true && title.indexOf(eventStartMoment) < 0){
					if(self.newtopic() == true){
						return 'Partie de ' + title + langArray['titleFormPrefix'] + eventStartMoment;						
					}else{
						return 'Partie' + langArray['titleFormPrefix'] + eventStartMoment;												
					}
				}
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
			//self.forum_list.splice(0,1,{id:'0', value:'Parties de jeux de rôle du ' + self.start()});
			if(curStartDate != undefined && curStartDate > curEndDate){
				self.end(self.start());
			}
		});	    
	    self.end = ko.observable("");//.extend({ requiredCustom: langArray['end'] });
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
				return false;

			return true;
		})
		self.errors = ko.validation.group([self.title, self.event_desc, self.start]);
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
					self.event_desc(data.event_desc);
					self.newtopic(data.newtopic != undefined ? true : false);
					self.maxusers(data.max_users);
					self.istopicable(data.topic_id != undefined ? true : false);					
				});
			}
		};

		self.save = function(){
			self.errors.showAllMessages(true);
			if(self.errors().length === 0){
				var postJsonData = {
					id: self.id(),
					title: self.title(),
					title_formatted: self.title_formatted(),
					start: self.startDate(),
					end: self.endDate(),
					event_desc: self.event_desc(),
					newtopic: self.newtopic(),
					max_users: self.maxusers(),
					forum: self.forum()
				};
				$.post( "api/events/", postJsonData, function( data ) {
					var message = $('<p />', { html: langArray[data.message]}),
					ok = $('<button />', { text: 'Ok', 'class': 'full' });

					dialogue( message.add(ok), langArray['infoModalTitle'] );				
				});
			}else{
				var message = $('<p />', { html: langArray['errorModalMsg'] + ' :<br/><br/>' + self.errors().join('<br/>') }),
				ok = $('<button />', { text: 'Ok', 'class': 'full' });

				dialogue( message.add(ok), langArray['errorModalTitle'] );
			}
		};
	}

	moment.locale(langArray['calendarLang']);
	$.datepicker.setDefaults( $.datepicker.regional[langArray['calendarLang']]);
	$("#start").datepicker({dateFormat: langArray['pickerDatePattern']});	
	$("#end").datepicker({dateFormat: langArray['pickerDatePattern']});
	var dtDateFormat = langArray['datePattern'];

	ko.validation.init({
		messagesOnModified: false,
		decorateInputElement: true,
		insertMessages: false,
		errorElementClass: 'validationElement',
		errorClass: 'validationElement'
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
</script>
<?php


require PUN_ROOT.'footer.php';
?>