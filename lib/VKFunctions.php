<?php

namespace lib;

use vendor\config;

class VKFunctions {

	const BASE_VK_REQUEST = 'https://api.vk.com/method/';

	/**
	 * @var bool
	 */
	public static $lastcurl;

	/**
	 * @return bool
	 */
	public static function getResponse() {
		return self::$lastcurl;
	}

	/**
	 * @param        $group_id
	 * @param        $text
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function setGroupTitle($group_id, $text, $token) {
		self::VKrequest('groups.edit', ['group_id' => $group_id, 'title' => $text, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param        $x
	 * @param        $y
	 * @param        $x2
	 * @param        $y2
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getOCPUS($group_id, $x, $y, $x2, $y2, $token) {
		self::VKrequest('photos.getOwnerCoverPhotoUploadServer', ['group_id' => $group_id, 'crop_x' => $x, 'crop_y' => $y, 'crop_x2' => $x2, 'crop_y2' => $y2, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $hash
	 * @param        $photo
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function saveOCP($hash, $photo, $token) {
		self::VKrequest('photos.saveOwnerCoverPhoto', ['hash' => rawurlencode($hash), 'photo' => rawurlencode($photo), 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function openGroupWall($group_id, $token) {
		self::VKrequest('groups.edit', ['group_id' => $group_id, 'wall' => 1, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function closeGroupWall($group_id, $token) {
		self::VKrequest('groups.edit', ['group_id' => $group_id, 'wall' => 2, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param        $topic_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function openTopic($group_id, $topic_id, $token) {
		self::VKrequest('board.openTopic', ['group_id' => $group_id, 'topic_id' => $topic_id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param        $topic_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function closeTopic($group_id, $topic_id, $token) {
		self::VKrequest('board.closeTopic', ['group_id' => $group_id, 'topic_id' => $topic_id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param        $topic_id
	 * @param        $message
	 * @param int    $fg
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function createBComment($group_id, $topic_id, $message, $fg = 0, $token) {
		self::VKrequest('board.createComment', ['group_id' => $group_id, 'topic_id' => $topic_id, 'message' => $message, 'guid' => 0, 'from_group' => $fg, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getUploadServer($group_id, $token) {
		self::VKrequest('photos.getWallUploadServer', ['group_id' => $group_id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param int    $count
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getFriends($id, $count = 1, $token) {
		self::VKrequest('friends.get', ['user_id' => $id, 'count' => $count, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function deleteFriend($id, $token) {
		self::VKrequest('friends.delete', ['user_id' => $id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param int    $unread
	 * @param int    $count
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getDialogs($unread = 0, $count = 200, $token) {
		self::VKrequest('messages.getDialogs', ['unread' => $unread, 'count' => $count, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getStatus($id, $token) {
		self::VKrequest('status.get', ['user_id' => $id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param        $topic_id
	 * @param        $count
	 * @param string $sort
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getTopic($group_id, $topic_id, $count, $sort = "desc", $token) {
		self::VKrequest('board.getComments', ['group_id' => $group_id, 'topic_id' => $topic_id, 'count' => $count, 'sort' => $sort, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param int    $count
	 * @param int    $offset
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getChatHistory($id, $count = 20, $offset = 0, $token) {
		self::VKrequest('messages.getHistory', ['chat_id' => $id, 'count' => $count, 'offset' => $offset, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param int    $count
	 * @param int    $offset
	 * @param int    $rev
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getHistory($id, $count = 20, $offset = 0, $rev = 0, $token) {
		self::VKrequest('messages.getHistory', ['user_id' => $id, 'count' => $count, 'offset' => $offset, 'rev' => $rev, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $owner_id
	 * @param        $post_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function pin($owner_id, $post_id, $token) {
		self::VKrequest('wall.pin', ['owner_id' => $owner_id, 'post_id' => $post_id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $owner_id
	 * @param        $post_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function postDelete($owner_id, $post_id, $token) {
		self::VKrequest('wall.delete', ['owner_id' => $owner_id, 'post_id' => $post_id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $text
	 * @param        $attachments
	 * @param int    $owner_id
	 * @param int    $from_group
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function sendPost($text, $attachments, $owner_id = 0, $from_group = 0, $token) {
		self::VKrequest('wall.post', ['message' => $text, 'attachments' => rawurlencode($attachments), 'owner_id' => rawurlencode($owner_id), 'from_group' => $from_group, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param int    $count
	 * @param int    $offset
	 * @param string $token
	 *
	 * @return bool|array
	 */
	public static function getPosts($id, $count, $offset, $token): ?array{
		self::VKrequest('wall.get', ['owner_id' => $id, 'count' => $count, 'offset' => $offset, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $type
	 * @param        $item
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function isLiked($id, $type, $item, $token) {
		self::VKrequest('likes.isLiked', ['user_id' => $id, 'type' => $type, 'item_id' => $item, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $count
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function friendsOnline($id, $count, $token) {
		self::VKrequest('friends.getOnline', ['user_id' => $id, 'count' => $count, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $text
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function addFriend($id, $text, $token) {
		self::VKrequest('friends.add', ['user_id' => $id, 'text' => $text, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $name
	 * @param        $fam
	 * @param        $fields
	 * @param int    $count
	 * @param int    $sex
	 * @param int    $online
	 * @param int    $age
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function search($name, $fam, $fields, $count = 20, $sex = 0, $online = 1, $age = 14, $token) {
		self::VKrequest('users.search', ['fields' => rawurlencode($fields), 'q' => rawurlencode($name. " " .$fam), 'count' => $count, 'sex' => $sex, 'online' => $online, 'age_to' => $age, 'age_from' => $age, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param $id
	 * @param $fields
	 * @param $token
	 *
	 * @return bool|mixed
	 */
	public static function getChatUsers($id, $fields, $token) {
		self::VKrequest('messages.getChatUsers', ['chat_id' => $id, 'fields' => $fields, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $owner_id
	 * @param        $post_id
	 * @param int    $f_group
	 * @param        $text
	 * @param        $attachments
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function createComment($owner_id, $post_id, $f_group = 0, $text, $attachments, $token) {
		self::VKrequest('wall.createComment', ['owner_id' => $owner_id, 'post_id' => $post_id, 'from_group' => $f_group, 'message' => $text, 'attachments' => $attachments, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $comment_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function deleteComment($id, $comment_id, $token) {
		self::VKrequest('wall.deleteComment', ['owner_id' => $id, 'comment_id' => $comment_id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $post_id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getComments($id, $post_id, $token) {
		self::VKrequest('wall.getComments', ['owner_id' => $id, 'post_id' => $post_id, 'count' => 100, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $aid
	 * @param        $artist
	 * @param        $title
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function audioEdit($id, $aid, $artist, $title, $token) {
		self::VKrequest('audio.edit', ['owner_id' => $id, 'audio_id' => $aid, 'artist' => rawurlencode($artist), 'title' => rawurlencode($title), 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $aid
	 * @param        $target
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function audioBroadcast($id, $aid, $target, $token) {
		self::VKrequest('audio.setBroadcast', ['audio' => $id. "_". $aid, 'target_ids' => rawurlencode($target), 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $owner_id
	 * @param        $post
	 * @param        $count
	 * @param        $offset
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getLikeList($owner_id, $post, $count, $offset, $token) {
		self::VKrequest('likes.getList', ['type' => 'post', 'owner_id' => $owner_id, 'item_id' => $post, 'count' => $count, 'offset' => $offset, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $owner_id
	 * @param        $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getCopies($owner_id, $id, $token) {
		self::VKrequest('likes.getList', ['type' => 'post', 'owner_id' => $owner_id, 'item_id' => $id, 'count' => '1000', 'filter' => 'copies', 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $fields
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getUser($id, $fields = '', $token) {
		self::VKrequest('users.get', ['user_ids' => rawurlencode($id), 'fields' => rawurlencode($fields), 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param int    $count
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getMessage($count = 1, $token) {
		self::VKrequest('messages.get', ['count' => $count, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param string $token
	 */
	public static function setOnline($token) {
		self::VKrequest('account.setOnline', ['access_token' => $token], true);
	}

	/**
	 * @param        $text
	 * @param int    $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function setStatus($text, $id = 0, $token) {
		self::VKrequest('status.set', ['text' => $text, 'group_id' => $id, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function setChatActivity($id, $token) {
		self::VKrequest('messages.setActivity', ['chat_id' => $id, 'type' => 'typing', 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function setUserActivity($id, $token) {
		self::VKrequest('messages.setActivity', ['user_id' => $id, 'type' => 'typing', 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $date_from
	 * @param        $date_to
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getStat($id, $date_from, $date_to, $token) {
		self::VKrequest('messages.send', ['stats.get' => $id, 'date_from' => $date_from, 'date_to' => $date_to, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param $id
	 * @param $userId
	 * @param $token
	 *
	 * @return bool|mixed
	 */
	public static function removeChatUser($id, $userId, $token) {
		self::VKrequest('messages.removeChatUser', ['chat_id' => $id, 'user_id' => $userId, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $message
	 * @param int    $attachments
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function sendChatMessage($id, $message, $attachments = 0, $token) {
		self::VKrequest('messages.send', ['chat_id' => $id, 'random_id' => mt_rand(PHP_INT_MIN, PHP_INT_MAX), 'message' => $message, 'attachment' => $attachments, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $message
	 * @param int    $attachments
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function sendUserMessage($id, $message, $attachments = 0, $token) {
		self::VKrequest('messages.send', ['user_id' => $id, 'random_id' => mt_rand(PHP_INT_MIN, PHP_INT_MAX), 'message' => $message, 'attachment' => rawurlencode($attachments), 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $peer_id
	 * @param        $message_id
	 * @param        $message
	 * @param int    $attachments
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function editMessage($peer_id, $message_id, $message, $attachments = 0, $token) {
		self::VKrequest('messages.edit', ['peer_id' => $peer_id, 'message' => $message, 'message_id' => $message_id, 'attachment' => rawurlencode($attachments), 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param        $delete_for_all
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function deleteMessage($id, $delete_for_all, $token) {
		self::VKrequest('messages.delete', ['message_ids' => $id, 'delete_for_all' => $delete_for_all, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function getUserSubs($id, $token) {
		self::VKrequest('users.getFollowers', ['user_id' => $id, 'count' => 1000, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $id
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function groupBanlist($id, $token) {
		self::VKrequest('groups.getBanned', ['group_id' => $id, 'count' => 200, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param string $code
	 * @param string $type
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function appWidgetsUpdate(string $code, string $type, string $token) {
		self::VKrequest('appWidgets.update', ['code' => $code, 'type' => $type, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}

	/**
	 * @param        $group_id
	 * @param        $peer_id
	 * @param        $end_date
	 * @param        $reason
	 * @param        $comment
	 * @param        $comment_visible
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function groupBan($group_id, $peer_id, $end_date, $reason, $comment, $comment_visible, $token) {
		self::VKrequest('groups.ban', ['group_id' => $group_id, 'owner_id' => $peer_id, 'end_date' => $end_date, 'reason' => $reason, 'comment' => $comment, 'comment_visible' => $comment_visible, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}


	/**
	 * @param        $group
	 * @param        $text
	 * @param        $mode
	 * @param        $signed
	 * @param string $token
	 *
	 * @return bool|mixed
	 */
	public static function sendGroupPost($group, $text, $mode, $signed, $token) {
		self::VKrequest('wall.post', ['owner_id' => $group, 'message' => $text, 'from_group' => $mode, 'signed' => $signed, 'access_token' => $token], true);
		if(self::$lastcurl != false) {
			$array = json_decode(self::$lastcurl, true);
			return $array;
		} else return false;
	}


	/**
	 * @param      $method
	 * @param      $par
	 * @param bool $isVKFunctions
	 *
	 * @return mixed
	 */

	public static function VKrequest($method, $par, bool $isVKFunctions = false) {
		$par = array_merge($par, [
			"v" => "5.95"
		]);
		$data = config::curl_get_contents(self::BASE_VK_REQUEST . $method. '?' . http_build_query($par));

		if($isVKFunctions)
			if ($data) {
				self::$lastcurl = $data;
			} else self::$lastcurl = false;

		return json_decode($data, true);
	}

}