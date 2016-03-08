<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
header('Content-type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : null;
$returnValue = '';

if ($pun_user['is_guest'])
	exit;

if($action == null)
	exit;

switch ($action)
{
	case 'users':
		getUsers();
		break;

	case 'topics':
		getTopics();
		break;
		
	case 'events':
		getEvents();
		break;

	case 'addEvent':
		if (isset($_GET['title']) && isset($_GET['desc']) && isset($_GET['start']) && isset($_GET['end']))
			addEvent($_GET['title'], $_GET['desc'], $_GET['start'], $_GET['end']);
		else
			$returnValue = "{return:0, msg:'Pas assez de paramêtres'}";
		break;

	default:
		exit();
}

function getUsers()
{	
	global $returnValue, $db;
	$array_users = [];
	
	$result = $db->query('SELECT id, username, registered FROM '.$db->prefix.'users') or error('Unable to fetch users list', __FILE__, __LINE__, $db->error());
	while($cur_user = $db->fetch_assoc($result))
	{
		array_push($array_users, $cur_user);
	}

	$returnValue = $array_users;
}

function getTopics()
{
	global $returnValue, $db;
	$array_topics = [];
	
	$result = $db->query('SELECT * FROM '.$db->prefix.'topics') or error('Unable to fetch topics list', __FILE__, __LINE__, $db->error());
	while($cur_topic = $db->fetch_assoc($result))
	{
		array_push($array_topics, $cur_topic);
	}

	$returnValue = $array_topics;
	
}

function getEvents()
{	
	global $returnValue, $db;
	$array_events = [];
	$result = $db->query('SELECT events.id as id, title, event_desc, max_users, start, end, topic_id, count(user_id) as registered_users FROM '.$db->prefix.'events left outer join '.$db->prefix.'events_subscriptions on events.id = event_id') or error('Unable to fetch events list', __FILE__, __LINE__, $db->error());

	//$result = $db->query('SELECT id, title, start, end, event_desc, topic_id, max_users, 0 as registered_users FROM '.$db->prefix.'events') or error('Unable to fetch events list', __FILE__, __LINE__, $db->error());
	while($cur_event = $db->fetch_assoc($result))
	{
		array_push($array_events, $cur_event);
	}

	$returnValue = $array_events;
}


function addEvent($title, $desc, $start, $end){
	global $returnValue, $db;

	// Insert the new event
	$db->query('INSERT INTO '.$db->prefix.'events (title, event_desc, start, end) VALUES(\''.$db->escape($title).'\', \''.$db->escape($desc).'\', '.$start.', '.$end.')') or error('Unable to create event', __FILE__, __LINE__, $db->error());
	$new_eid = $db->insert_id();


	$returnValue = "{error: 0, msg: 'New event".$new_eid." created'}";
}


if($returnValue)
	echo json_encode($returnValue);
?>