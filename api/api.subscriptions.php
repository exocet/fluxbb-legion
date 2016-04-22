<?php
	function doGet($params){
		global $db;
		$array_subsciptions = [];

		if (isset($params['id']) && $params['id'] != null){
			$event_id = $params['id'];

			$query = 'SELECT username FROM '.$db->prefix.'events_subscriptions, users WHERE event_id = '.$db->escape($event_id).' and users.id = user_id'  ;
			$result = $db->query($query);
			while($cur_subscription = $db->fetch_assoc($result))
			{
				array_push($array_subsciptions, $cur_subscription);
			}
		}

		return $array_subsciptions;
	}

	function doPost($params){
		global $db, $pun_user;

		$user_id = $pun_user['id'];

		if (isset($params['id']) && $params['id'] != null)
			$event_id = $params['id'];


		$query = 'INSERT INTO '.$db->prefix.'events_subscriptions (event_id, user_id) VALUES ('.$db->escape($event_id).', '.$user_id.')';
		$db->query($query) or apiDbLayerError('Unable to create event subscription');

		return ['success', 'EventSubscriptionCreated'];
	}	

	function doDelete($params){
		global $db, $pun_user;

		$user_id = $pun_user['id'];

		if (isset($params['id']) && $params['id'] != null)
			$event_id = $params['id'];

		$query = 'DELETE FROM '.$db->prefix.'events_subscriptions WHERE event_id ='.$db->escape($event_id).' AND user_id = '.$user_id;
		$db->query($query) or apiDbLayerError('Unable to delete event subscription');

		return ['success', 'EventSubscriptionDeleted'];
	}
?>