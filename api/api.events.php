<?php
	function doGet($params){
		global $db;
		$array_events = [];

		if(isset($params['id']) && $params['id'] != null && $params['id'] != ''){
			$id = $params['id'];
			$query = 'SELECT '.$db->prefix.'events.id, title, owner_id, title_formatted, event_desc, max_users, topic_id, start, end, count(user_id) as registered_users, is_multi_game FROM '.$db->prefix.'events left outer join '.$db->prefix.'events_subscriptions on event_id = '.$db->prefix.'events.id where events.id = '.$db->escape($id).' group by events.id';
		}else if(isset($params['mod']) && $params['mod'] != null && $params['mod'] != ''){
			switch ($params['mod']) {
				case 'today':
					$start = new DateTime();
					$start = $start->getTimestamp();
					$end = $start;

					break;
				case 'thisweek':
					$start = new DateTime();
					$start->modify('last monday');
					$end = clone $start;
					$end->modify('next sunday');
					
					$end = $end->getTimestamp();
					$start = $start->getTimestamp();

					break;
				
				case 'thismonth':
					$start = new DateTime();
					$start->modify('first day of this month');
					$end = clone $start;
					$end->modify('last day of this month');
					
					$end = $end->getTimestamp();
					$start = $start->getTimestamp();

					break;
				default:					
					break;
			}			
			$query = 'SELECT '.$db->prefix.'events.id, title, owner_id, title_formatted, event_desc, max_users, topic_id, start, end, count(user_id) as registered_users, is_multi_game FROM '.$db->prefix.'events left outer join '.$db->prefix.'events_subscriptions on event_id = '.$db->prefix.'events.id where start >= '.$start.' and end <= '.$end.' group by events.id';
		}else{
			$query = 'SELECT '.$db->prefix.'events.id, title, owner_id, title_formatted, event_desc, max_users, topic_id, start, end, count(user_id) as registered_users, is_multi_game FROM '.$db->prefix.'events left outer join '.$db->prefix.'events_subscriptions on event_id = '.$db->prefix.'events.id group by events.id';
		}
		$result = $db->query($query) or apiDbLayerError('Unable to fetch events list');
		while($cur_event = $db->fetch_assoc($result))
		{
			array_push($array_events, $cur_event);
		}

		return $array_events;
	}

	function doDelete($params){
		global $db, $pun_user;
		$now = time();
		$user_id = $pun_user['id'];

		if(isset($params['id']) && $params['id'] != null && $params['id'] != ''){		
			$id = $params['id'];

			$query = 'SELECT id FROM '.$db->prefix.'events where id='.$db->escape($id).' and owner_id = '.$db->escape($user_id);
			$result = $db->query($query) or apiDbLayerError('Unable to check events owner');
			$num_rows = $db->num_rows($result);
			if($num_rows === 1){
				$query = 'DELETE FROM '.$db->prefix.'events where id='.$db->escape($id);
				$result = $db->query($query) or apiDbLayerError('Unable to delete event');
				$query = 'DELETE FROM '.$db->prefix.'events_subscriptions where event_id='.$db->escape($id);
				$result = $db->query($query) or apiDbLayerError('Unable to delete event subscriptions');			
			}else{
				return ['error', 'EventDeletionNotAllowed'];
			}

			return ['success', 'EventDeleted'];
		}
		return ['error', 'EventDeletionNoIdSubmitted'];
	}

	function doPost($params){
		global $db, $pun_user;
		$now = time();
		$user_id = $pun_user['id'];

		//defaults
		$newtopic = false;
		$is_multi_game = 0;
		$max_users = 0;
		$start = 0;
		$end = 0;
		$title_formatted = "";
		$is_multi_game = 0;
		$forum = 0;	

		//validation
		if (isset($params['title']) && $params['title'] != null)
			$title = $params['title'];
		
		if (isset($params['title_formatted']) && $params['title_formatted'] != null)
			$title_formatted = $params['title_formatted'];

		if (isset($params['start']) && $params['start'] != null && (is_numeric($params['start'])))
			$start = $params['start'];

		if (isset($params['end']) && $params['end'] != null && (is_numeric($params['end'])))
			$end = $params['end'];

		if (isset($params['event_desc']) && $params['event_desc'] != null && ($params['event_desc']))
			$event_desc = $params['event_desc'];
		
		if (isset($params['newtopic']) && $params['newtopic'] != null)
			$newtopic = $params['newtopic'] == 'false' ? false : true;

		if (isset($params['max_users']) && $params['max_users'] != null && (is_numeric($params['max_users'])))		
			$max_users = $params['max_users'];

		if (isset($params['forum']) && $params['forum'] != null && (is_numeric($params['forum'])))		
			$forum = $params['forum'];

		if(isset($params['id']) && $params['id'] != null && $params['id'] != ''){
			$id = $params['id'];
			$query = 'UPDATE '.$db->prefix.'events set title = \''.$db->escape($title).'\', title_formatted = \''.$db->escape($title_formatted).'\', event_desc = \''.$db->escape($event_desc).'\', max_users = \''.$db->escape($max_users).'\', start = '.$db->escape($start).', end = '.$db->escape($end).', is_multi_game = '.$db->escape($is_multi_game).' where id = '.$db->escape($id);
			$errorMessage = "Unable to update event";
			$db->query($query) or apiDbLayerError($errorMessage); 
		}else{
			if($newtopic == false){
				/* On cherche s'il existe un topic commun pour cette date
				L'attribut is_multi_game indique que l'événement est associé à un topic pouvant être associé à d'autres événements. Permet par exemple d'avoir un topic pour les parties du vendredi soir.
				*/
				$query = 'SELECT topic_id FROM '.$db->prefix.'events WHERE start='.$db->escape($start).' AND is_multi_game = 1';
				$result = $db->query($query) or apiDbLayerError('Unable to fetch multi_game topic');
				$assoc_topic_id = null;
				while($cur_topic = $db->fetch_assoc($result))
				{
					$assoc_topic_id = $cur_topic['topic_id'];
				}
				$is_multi_game  = ($assoc_topic_id == null ? 1 : 0);
			}
			$assoc_topic_id_sql = ($assoc_topic_id == null ? 'NULL' : $assoc_topic_id);

			$query = 'INSERT INTO '.$db->prefix.'events (owner_id, title,  title_formatted, event_desc, max_users, start, end, topic_id, is_multi_game) VALUES ('.$user_id.', \''.$db->escape($title).'\', \''.$db->escape($title_formatted).'\',\''.$db->escape($event_desc).'\', \''.$db->escape($max_users).'\', '.$db->escape($start).', '.$db->escape($end).', '.$assoc_topic_id_sql.', '.$db->escape($is_multi_game).')';					
			$db->query($query) or apiDbLayerError("Unable to create event"); 
			$new_eid = $db->insert_id();
			if ($newtopic == true || $assoc_topic_id == null || $assoc_topic_id == ''){
				//On doit créer un nouveau topic réservé à cet événement.
				$new_tid = addEventTopic($title_formatted, $event_desc, $forum);
				$query = 'UPDATE '.$db->prefix.'events set topic_id='.$new_tid.' where id='.$new_eid;
				$db->query($query) or apiDbLayerError('Unable to associate event with topic'); 
			}
	}
		
		return ['success', 'EventCreated'];		
	}


//Utils function specific to events
	function addEventTopic($subject, $event_desc, $forum){

		require PUN_ROOT.'include/search_idx.php';

		global $db, $pun_user;
		
		$username = $pun_user['username'];
		$email = $pun_user['email'];
		$hide_smilies = 0;
		$fid = $forum;
		$now = time();
		
		// Create the topic
		$query = 'INSERT INTO '.$db->prefix.'topics (poster, subject, posted, last_post, last_poster, sticky, forum_id) VALUES(\''.$db->escape($username).'\', \''.$db->escape($subject).'\', '.$now.', '.$now.', \''.$db->escape($username).'\', 0, '.$fid.')';
		//print_r($query);
		$db->query($query) or apiDbLayerError('Unable to create topic');
		$new_tid = $db->insert_id();

		// To subscribe or not to subscribe, that ...
		if ($pun_config['o_topic_subscriptions'] == '1' && $subscribe)
			$db->query('INSERT INTO '.$db->prefix.'topic_subscriptions (user_id, topic_id) VALUES('.$pun_user['id'].' ,'.$new_tid.')') or apiDbLayerError('Unable to add subscription');

		// Create the post ("topic post")
		$db->query('INSERT INTO '.$db->prefix.'posts (poster, poster_id, poster_ip, message, hide_smilies, posted, topic_id) VALUES(\''.$db->escape($username).'\', '.$pun_user['id'].', \''.$db->escape(get_remote_address()).'\', \''.$db->escape($event_desc).'\', '.$hide_smilies.', '.$now.', '.$new_tid.')') or apiDbLayerError('Unable to create post');

		$new_pid = $db->insert_id();

		// Update the topic with last_post_id
		$db->query('UPDATE '.$db->prefix.'topics SET last_post_id='.$new_pid.', first_post_id='.$new_pid.' WHERE id='.$new_tid) or apiDbLayerError('Unable to update topic');
		
		update_search_index('post', $new_pid, $event_desc, $subject);

		update_forum($fid);	
		
		return $new_tid;
	}
?>