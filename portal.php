<?php
define('PUN_ROOT', dirname(__FILE__).'/');
define('PUN_PORTAL', 1);
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'portal_header.php';
?>
<link rel="stylesheet" href="portal/css/fullcalendar.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="portal/css/fullcalendar.print.css" type="text/css" media="all" />
<link rel="stylesheet" href="portal/css/jquery.qtip.min.css" type="text/css" media="all" />

<div>
<p>Paragraph</p>
<p id="result" title="Hey !">Result</p>
</div>
<div id='calendar'></div>
<script src="portal/js/jquery-2.2.1.min.js"></script>
<script src="portal/js/moment.js"></script>
<script src="portal/js/fullcalendar.min.js"></script>
<script src="portal/js/jquery.qtip.min.js"></script>

<script>
	$.get("json_gateway.php?action=events", function(data){
		calendarData = data;
		$('#calendar').fullCalendar({
			events: data,
		    eventRender: function(event, element) {
		    	var eventFullDesc = event.event_desc;
				if(event.max_users)
					eventFullDesc = eventFullDesc + "</br></br>Nombre d'inscris : " + event.registered_users + "/" + event.max_users;
				if(event.topic_id)
					eventFullDesc = eventFullDesc + '</br></br><a target="_blank" href=viewtopic.php?id=' + event.topic_id + '>Suivre dans le forum</a>';
		        element.qtip({
					content: {
						title: event.title,
						text: eventFullDesc
					},
					my: 'center center',
					at: 'bottom center',
					style: {
				        classes: 'qtip-dark qtip-shadow qtip-rounded'
				    },
				    hide: {
				        delay: 1000
				    }				    				
				})
		    }			
		});
	});
</script>
<?php
$footer_style = 'index';
require PUN_ROOT.'footer.php';
?>

