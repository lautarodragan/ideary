<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Content Component Article Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class UsersModelUser extends JModelItem
{
    /**
     * Model context string.
     *
     * @var		string
     */
    protected $_context = 'com_content.article';


    public function follow($writer_id, $user_id){

        $db = $this->getDbo();

        $query = 'INSERT INTO #__follows ( followed_id, follower_id, created_at )' .
            ' VALUES ( '.(int) $writer_id.', '.(int) $user_id.', \''.date_create()->format('Y-m-d H:i:s').'\' )';

        $db->setQuery($query);

        return $db->query();
    }

    public function getNotification($user_id, $notification_type){

        $db = $this->getDbo();

        $query = "SELECT nt.id, ntu.send_email FROM #__notification_types_users AS ntu JOIN #__notification_types AS nt ON (nt.id = ntu.notification_type_id)".
        " WHERE ntu.user_id=".$user_id." AND nt.type='".$notification_type."'";

        $db->setQuery($query);

        $object = $db->loadObject();

        return $object;

    }

	public function getNotification2($user_id, $notification_type){

        $db = $this->getDbo();

        $query = "SELECT ".$notification_type." from text_user_config_notif WHERE user_id=".$user_id;

        $db->setQuery($query);

        $object = $db->loadObject();

        return $object;

    }

    public function saveNotification($notification_type_id, $notified_id, $text_id='null', $user_id='null', $message_id='null'){

        $db = $this->getDbo();

        $query = "INSERT INTO #__notifications ( user_id, notified_id, text_id, message_id, notification_type_id, created_at )" .
            " VALUES ( ".(int) $user_id.", ".(int) $notified_id.", ".$text_id.", ".$message_id.",".$notification_type_id.", '".date_create()->format('Y-m-d H:i:s')."' )";

        $db->setQuery($query);

        return $db->query();

    }

    public function unfollow($writer_id, $user_id){

        $db = $this->getDbo();

        $query = 'DELETE FROM text_notifications WHERE user_id='.$user_id.' AND notified_id='.$writer_id.' AND notification_type_id=(SELECT id FROM text_notification_types WHERE TYPE="follow")';
        $db->setQuery($query);
        $db->query();

        $query = 'DELETE FROM #__follows WHERE followed_id='.(int) $writer_id.' AND follower_id='.(int) $user_id.'';
        $db->setQuery($query);
        return $db->query();
    }


    public function isFollowed($writer_id, $user_id){

        $db = $this->getDbo();

        $query = 'SELECT * FROM #__follows WHERE followed_id='.(int) $writer_id.' AND follower_id='.(int) $user_id;

        $db->setQuery($query);

        $followed = $db->loadObject();

        return ($followed)? true : false;

    }

    public function sendMessage($user_from, $user_to, $subject, $message){

        $db = $this->getDbo();

        $query = "INSERT INTO #__messages ( user_id_from, user_id_to, date_time, state, subject, message )" .
            " VALUES ( ".(int) $user_from.", ".(int) $user_to.", '".date_create()->format('Y-m-d H:i:s')."', true, '".$subject."', '".$message."' )";

        $db->setQuery($query);

        $db->query();

        return $db->insertid();
    }

    public function setSawAllNotifications($user_id){

        $db = $this->getDbo();

        $query = "UPDATE #__notifications SET saw=true WHERE notified_id=".(int)$user_id;

        $db->setQuery($query);

        return $db->query();
    }

    public function getUser($user_id){

        $db = $this->getDbo();

        $query = "SELECT * FROM #__users WHERE id=".(int)$user_id;

        $db->setQuery($query);

        $object = $db->loadObject();

        return $object;

    }

}

