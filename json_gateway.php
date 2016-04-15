<?php

define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';
header('Content-type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : null;
$returnValue;

if (isset($_GET['debug']) && $_GET['debug'] == true){
	print_r($_GET);
	print_r(isset($pun_user['is_guest']) ? 'is_guest : N' : 'is_guest : O');
}

if ($pun_user['is_guest']){
	exit;
}
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
	
	case 'getEvent';
		if ($_GET['id'] != null){
			$id = $_GET['id'];		
			getEvent($id);
		}
		break;

	case 'saveEvent';
		$is_topicable = false;
		$is_public = false;
		$max_users = 0;
		$start = 0;
		$end = 0;
		$title_formatted = "";

		if (isset($_GET['title']) && isset($_GET['event_desc'])){
			if ($_GET['istopicable'] != null && ($_GET['istopicable'] == false || $_GET['istopicable'] == true))
				$is_topicable = $_GET['istopicable'];

			if ($_GET['maxusers'] != null && (is_numeric($_GET['maxusers'])))
				$max_users = $_GET['maxusers'];

			if ($_GET['title_formatted'] != null)
				$title_formatted = $_GET['title_formatted'];

			if ($_GET['start'] != null && (is_numeric($_GET['start'])))
				$start = $_GET['start'];

			if ($_GET['end'] != null && (is_numeric($_GET['end'])))
				$end = $_GET['end'];

			if ($_GET['ispublic'] != null && ($_GET['ispublic']))
				$is_public = $_GET['ispublic'] === false ? 0 : 1;

			if(isset($_GET['id']) && $_GET['id'] != null && $_GET['id'] != ""){
				$max_users = $_GET['maxusers'];
				$start = $_GET['start'];
				$end = $_GET['end'];
				updateEvent($_GET['id'], $_GET['title'], $title_formatted, $_GET['event_desc'], $max_users, $start, $end, $is_public);
			}else{
				addEvent($_GET['title'], $title_formatted, $_GET['event_desc'], $is_topicable, $max_users, $start, $end, $is_public);
			}		
		}
		break;
		
	default:
		exit();
}

//Final instruction
if ($returnValue)
	echo buildReturnValue($returnValue);



//Functions
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
	
	$result = $db->query('SELECT '.$db->prefix.'events.id, title, owner_id, title_formatted, event_desc, max_users, topic_id, start, end, count(user_id) as registered_users, is_public FROM '.$db->prefix.'events left outer join '.$db->prefix.'events_subscriptions on event_id = '.$db->prefix.'events.id group by events.id') or error('Unable to fetch events list', __FILE__, __LINE__, $db->error());
	while($cur_event = $db->fetch_assoc($result))
	{
		array_push($array_events, $cur_event);
	}

	$returnValue = $array_events;
}


function getEvent($id)
{	
	global $returnValue, $db;
	$array_events = [];
	
	$result = $db->query('SELECT '.$db->prefix.'id, owner_id, title, title_formatted, event_desc, max_users, topic_id, start, end, is_public, topic_id FROM '.$db->prefix.'events WHERE id = \''.$db->escape($id).'\'') or error('Unable to fetch event', __FILE__, __LINE__, $db->error());
	while($cur_event = $db->fetch_assoc($result))
	{
		array_push($array_events, $cur_event);
	}

	$returnValue = $array_events[0];
}

function addEvent($title, $title_formatted, $event_desc, $isTopicAble, $max_users, $start, $end, $is_public){
	
	global $returnValue, $db, $pun_user;
	$now = time();
	$user_id = $pun_user['id'];
	
	$query = 'INSERT INTO '.$db->prefix.'events (owner_id, title,  title_formatted, event_desc, max_users, start, end, topic_id, is_public) VALUES ('.$user_id.', \''.$db->escape($title).'\', \''.$db->escape($title_formatted).'\',\''.$db->escape($event_desc).'\', \''.$db->escape($max_users).'\', '.$db->escape($start).', '.$db->escape($end).', NULL, '.$db->escape($is_public).')';
	$db->query($query) or error('Unable to create event', __FILE__, __LINE__, $db->error()); 
	$new_eid = $db->insert_id();
	print_r($isTopicAble);
	if ($isTopicAble == 'true'){
		$new_tid = addEventTopic($title, $event_desc);
		$query = 'UPDATE '.$db->prefix.'events set topic_id='.$new_tid.' where id='.$new_eid;
		$db->query($query) or error('Unable to update event', __FILE__, __LINE__, $db->error()); 
	}
	
	$returnValue = buildMessage(0, "EventCreated");
}

function updateEvent($id, $title, $title_formatted, $event_desc, $max_users, $start, $end, $is_public){
	global $returnValue, $db, $pun_user;
	$now = time();
	
	$query = 'UPDATE '.$db->prefix.'events set title = \''.$db->escape($title).'\', title_formatted = \''.$db->escape($title_formatted).'\', event_desc = \''.$db->escape($event_desc).'\', max_users = \''.$db->escape($max_users).'\', start = '.$db->escape($start).', end = '.$db->escape($end).', is_public = '.$db->escape($is_public).' where id = '.$db->escape($id);
	$db->query($query) or error('Unable to update event', __FILE__, __LINE__, $db->error()); 

	$returnValue = buildMessage(0, "EventUpdated");
}

function addEventTopic($subject, $event_desc){

	require PUN_ROOT.'include/search_idx.php';

	global $returnValue, $db, $pun_user;
	
	$username = $pun_user['username'];
	$email = $pun_user['email'];
	$hide_smilies = 0;
	$fid = 2;
	$now = time();
	
	// Create the topic
	$query = 'INSERT INTO '.$db->prefix.'topics (poster, subject, posted, last_post, last_poster, sticky, forum_id) VALUES(\''.$db->escape($username).'\', \''.$db->escape($subject).'\', '.$now.', '.$now.', \''.$db->escape($username).'\', 0, '.$fid.')';
	//print_r($query);
	$db->query($query) or error('Unable to create topic', __FILE__, __LINE__, $db->error());
	$new_tid = $db->insert_id();

	// To subscribe or not to subscribe, that ...
	if ($pun_config['o_topic_subscriptions'] == '1' && $subscribe)
		$db->query('INSERT INTO '.$db->prefix.'topic_subscriptions (user_id, topic_id) VALUES('.$pun_user['id'].' ,'.$new_tid.')') or error('Unable to add subscription', __FILE__, __LINE__, $db->error());

	// Create the post ("topic post")
	$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_id, poster_ip, message, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', '.$pun_user['id'].', \''.$db->escape(get_remote_address()).'\', \''.$db->escape($event_desc).'\', '.$hide_smilies.', '.$now.', '.$new_tid.')') or error('Unable to create post', __FILE__, __LINE__, $db->error());

	$new_pid = $db->insert_id();

	// Update the topic with last_post_id
	$db->query('UPDATE '.$db->prefix.'topics SET last_post_id='.$new_pid.', first_post_id='.$new_pid.' WHERE id='.$new_tid) or error('Unable to update topic', __FILE__, __LINE__, $db->error());
	
	update_search_index('post', $new_pid, $event_desc, $subject);

	update_forum($fid);	
	
	return $new_tid;
}


//Local utils functions
function buildMessage($error, $message){
	$message = json_encode(array('error' => $error, 'message' => $message), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
	return $message;
}

function buildReturnValue($returnValue){
	if (!is_string($returnValue))
		return json_encode($returnValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
	return $returnValue;
}

?>