<?php
define('PUN_ROOT', dirname(__FILE__).'/');
define('PUN_PORTAL', 1);
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'portal_header.php';
?>
<link rel="stylesheet" href="portal/css/fullcalendar.min.css" type="text/css" media="all" />
<!--<link rel="stylesheet" href="portal/css/fullcalendar.print.css" type="text/css" media="all" />-->
<link rel="stylesheet" href="portal/css/jquery.qtip.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.structure.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.theme.min.css" type="text/css" media="all" />

<script src="portal/js/jquery-2.2.1.min.js"></script>
<script src="portal/css/jquery-ui-1.12.0-rc.1.custom/jquery-ui.min.js"></script>
<script src="portal/js/moment.js"></script>
<script src="portal/js/fullcalendar.min.js"></script>
<script src="portal/js/jquery.qtip.min.js"></script>

<div id='calendar'></div>

<script>
	moment.locale('fr');
	var calendarData = [];
	<?php
		echo "var user_name = '".$pun_user['username']."';\n";
		echo "var user_id = '".$pun_user['id']."';\n";
	?>
	$.get("api/events/", function(data){
		console.log(data);
		data.forEach(function(value, index, array){
			calendarData.push({
				title: value.title,
				owner_id: value.owner_id,
				start: moment.unix(value.start),
				end: moment.unix(value.end),
				registered_users: value.registered_users,
				max_users: value.max_users,
				topic_id: value.topic_id,
				event_desc: value.event_desc,
				allDay: true
			});			
		});
		$('#calendar').fullCalendar( 'addEventSource', calendarData );
	});
	$('#calendar').fullCalendar({
		events: calendarData,
		theme: true,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		defaultView: 'month',
	    eventRender: function(event, element) {
	    	var eventFullDesc = event.event_desc;
			if(event.max_users && event.max_users > 0)
				eventFullDesc = eventFullDesc + "</br></br>Nombre d'inscris : " + event.registered_users + "/" + event.max_users;
			if(event.topic_id)
				eventFullDesc = eventFullDesc + '</br></br><a target="_blank" href=viewtopic.php?id=' + event.topic_id + '>Suivre dans le forum</a>';
			if(event.owner_id == user_id){
				eventFullDesc = eventFullDesc + '</br></br><a target="_blank" href=viewtopic.php?id=' + event.topic_id + '>Supprimer l\'événement</a>';
				eventFullDesc = eventFullDesc + '</br></br><a target="_blank" href=viewtopic.php?id=' + event.topic_id + '>Annuler l\'événement</a>';
			}
	        element.qtip({
				content: {
					title: event.title,
					text: eventFullDesc
				},
				my: 'top center',
				at: 'bottom center',
				style: {
			        classes: 'qtip-dark qtip-shadow qtip-rounded',
			        widget: true
			    },
			    hide: {
			    	fixed: true,
			    	delay: 500
			    },
				show: {
					solo: true
				}		    				    				
			})
	    }	    			
	});
</script>

<?php
$footer_style = 'index';
require PUN_ROOT.'footer.php';
?>

