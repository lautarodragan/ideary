<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Base controller class for Users.
 *
 * @package		Joomla.Site
 * @subpackage	com_users
 * @since		1.5
 */
class UsersController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		
		// Get the document object.
		$document	= JFactory::getDocument();
		
		
		// Set the default view name and format from the Request.
		$vName	 = JRequest::getCmd('view', 'login');
		$vFormat = $document->getType();
		$lName	 = JRequest::getCmd('layout', 'default');
		

		
		if ($view = $this->getView($vName, $vFormat)) {
			// Do any specific processing by view.
			switch ($vName) {
				case 'registration':
					// If the user is already logged in, redirect to the profile page.
					$user = JFactory::getUser();
					if ($user->get('guest') != 1) {
						// Redirect to profile page.
						$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile', false));
						return;
					}

					// Check if user registration is enabled
            		if(JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0) {
            			// Registration is disabled - Redirect to login page.
						$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
						return;
            		}

					// The user is a guest, load the registration model and show the registration page.
					$model = $this->getModel('Registration');
					break;

				// Handle view specific models.
				case 'profile':

					// If the user is a guest, redirect to the login page.
					$user = JFactory::getUser();
					if ($user->get('guest') == 1) {
						// Redirect to login page.
						$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
						return;
					}
					$model = $this->getModel($vName);
					break;

				// Handle the default views.
				case 'login':
					$model = $this->getModel($vName);
					break;

				case 'reset':
					// If the user is already logged in, redirect to the profile page.
					$user = JFactory::getUser();
					if ($user->get('guest') != 1) {
						// Redirect to profile page.
						$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile', false));
						return;
					}

					$model = $this->getModel($vName);
					break;

				case 'remind':
					// If the user is already logged in, redirect to the profile page.
					$user = JFactory::getUser();
					if ($user->get('guest') != 1) {
						// Redirect to profile page.
						$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile', false));
						return;
					}

					$model = $this->getModel($vName);
					break;

				default:
					$model = $this->getModel('Login');
					break;
			}

			// Push the model into the view (as default).
			$view->setModel($model, true);
			$view->setLayout($lName);

			// Push document object into the view.
			$view->assignRef('document', $document);

			$view->display();
		}
	}

	
	public function forgot_confirm(){
        $model = $this->getModel('reset'); 
		$lang = JRequest::getString('lang');		
		$result = $model->forgot_confirm($lang);			
		echo $result;
    }
	
	public function forgot_complete(){
        $model = $this->getModel('reset'); 
		$lang = JRequest::getString('lang');		
		$result = $model->forgot_complete($lang);			
		echo $result;
    }
	
	public function forgot_success(){
        $model = $this->getModel('reset'); 
		$lang = JRequest::getString('lang');		
		$result = $model->forgot_success($lang);			
		echo $result;
    }

    public function editInterest(){
        $app = JFactory::getApplication();
        $app->setUserState('users.editinterest', 1);
        $this->setRedirect(JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.$_GET["user_id"], false));
    }

    public function follow(){

        // Get the application object.
        $app = JFactory::getApplication();

        $writer_id = JRequest::getInt('writer_id');

        $user_id = JFactory::getUser()->get('id');

        $user_id = ($user_id)? $user_id : 949;

        $user_model = $this->getModel('user');

        if(ideary::checkIfFollowing($writer_id, $user_id)){
            echo json_encode(array('success' => true));
        }
        else{

            $result = $user_model->follow($writer_id, $user_id);

            //$notification = $user_model->getNotification($writer_id, 'follow');
            $notification = $user_model->getNotification2($writer_id, 'follow');

            /*if($result && $notification){

                if($notification->send_email){

                    $writer = $user_model->getUser($writer_id);
                    $subject = JText::_("FOLLOW_EMAIL_SUBJECT");
                    $content = JText::_("FOLLOW_EMAIL_BODY");
                    $content = str_replace('{user}', JFactory::getUser()->get('username'), $content);
                    $recipient = $writer->email;

                    $this->sendMail($subject, $content, $recipient);
                }

                $user_model->saveNotification($notification->id, $writer_id, 'null', $user_id);
            }*/

            $user_model->saveNotification(1, $writer_id, 'null', $user_id);
            echo json_encode(array('success' => $result));

        }

        $app->close();

    }

    public function invited_to_write(){

        // Get the application object.
        $app = JFactory::getApplication();

        $writer_id = JRequest::getInt('writer_id');

        $user_id = JFactory::getUser()->get('id');

        $user_id = ($user_id)? $user_id : 949;

        $user_model = $this->getModel('user');

        $notificationType = Ideary::getNotificationTypeByType('invited_to_write');

        $result = $user_model->saveNotification($notificationType->id, $writer_id, 'null', $user_id);

        echo json_encode(array('success' => $result));

        $app->close();
    }

    public function unfollow(){

        // Get the application object.
        $app = JFactory::getApplication();

        $writer_id = JRequest::getInt('writer_id');

        $user_id = JFactory::getUser()->get('id');

        $user_id = ($user_id)? $user_id : 949;

        $user_model = $this->getModel('user');

        $result = $user_model->unfollow($writer_id, $user_id);

        echo json_encode(array('success' => $result));

        $app->close();

    }

    public function get_users_who_applauded_text(){

        // Get the application object.
        $app = JFactory::getApplication();

        $text_id = JRequest::getInt('text_id');
        $limit = JRequest::getInt('limit');
        $offset = JRequest::getInt('offset');

        $users = Ideary::getUsersWhoApplaudedText($text_id, $offset, $limit);

        $usersHtml = Ideary::generateUserListContent($users);

        $clapCount = Ideary::getCountApplausesByTextId($text_id);

        echo json_encode(array('usersHtml' => $usersHtml, 'clapCount' => $clapCount));

        $app->close();

    }

    public function get_users_who_applauded_text_page(){

        // Get the application object.
        $app = JFactory::getApplication();

        $text_id = JRequest::getInt('text_id');
        $limit = JRequest::getInt('limit');
        $offset = JRequest::getInt('offset');

        $users = Ideary::getUsersWhoApplaudedText($text_id, $offset, $limit);

        $usersHtml = Ideary::generateUserListContent($users);

        echo json_encode(array('usersHtml' => $usersHtml));

        $app->close();

    }

    public function get_users_who_follow_me(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_id = JRequest::getInt('user_id');
        $user_id = ($user_id)? $user_id : JFactory::getUser()->get('id');
        $limit = JRequest::getInt('limit');
        $offset = JRequest::getInt('offset');

        $users = Ideary::getUsersWhoFollowMe($user_id, $offset, $limit);

        $usersHtml = Ideary::generateUserListContent($users);

        $countUsers = Ideary::getCountUsersWhoFollowMe($user_id);

        echo json_encode(array('countUsers' => $countUsers, 'usersHtml' => $usersHtml));

        $app->close();

    }

    public function get_users_who_follow_me_page(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_id = JRequest::getInt('user_id');
        $user_id = ($user_id)? $user_id : JFactory::getUser()->get('id');
        $limit = JRequest::getInt('limit');
        $offset = JRequest::getInt('offset');

        $users = Ideary::getUsersWhoFollowMe($user_id, $offset, $limit);

        $usersHtml = Ideary::generateUserListContent($users);

        echo json_encode(array('usersHtml' => $usersHtml));

        $app->close();

    }

    public function get_users_who_i_am_following(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_id = JRequest::getInt('user_id');
        $user_id = ($user_id)? $user_id : JFactory::getUser()->get('id');
        $limit = JRequest::getInt('limit');
        $offset = JRequest::getInt('offset');

        $users = Ideary::getUsersWhoIAmFollowing($user_id, $offset, $limit);

        $usersHtml = Ideary::generateUserListContent($users);

        $countUsers = Ideary::getCountUsersWhoIAmFollowing($user_id);

        echo json_encode(array('countUsers' => $countUsers, 'usersHtml' => $usersHtml));

        $app->close();

    }


    public function get_users_who_i_am_following_page(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_id = JRequest::getInt('user_id');
        $user_id = ($user_id)? $user_id : JFactory::getUser()->get('id');
        $limit = JRequest::getInt('limit');
        $offset = JRequest::getInt('offset');

        $users = Ideary::getUsersWhoIAmFollowing($user_id, $offset, $limit);

        $usersHtml = Ideary::generateUserListContent($users);

        echo json_encode(array('usersHtml' => $usersHtml));

        $app->close();

    }

	
	public function wrong_registration(){
        $model = $this->getModel('registration'); 
	    $lang = JRequest::getString('lang');
		$result = $model->wrong_registration($lang);	
		echo $result;
    }
	
	public function success_registration(){
        $model = $this->getModel('registration'); 
		$lang = JRequest::getString('lang');		
		$result = $model->success_registration($lang);			
		echo $result;
    }

	public function success_registration_pre(){
        $model = $this->getModel('registration'); 
		$lang = JRequest::getString('lang');		
		$result = $model->success_registration_pre($lang);			
		echo $result;
    }


    public function send_message(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_from = JFactory::getUser()->get('id', 949);
        $user_to = JRequest::getInt('user_to');
        $subject = JRequest::getString('subject');
        $message = JRequest::getString('message');

        $user_model = $this->getModel('user');

        $result = $user_model->sendMessage($user_from, $user_to, $subject, $message);

        $message_id = $result;

        $result = ($result == 0)? false : true;

        $notification = $user_model->getNotification($user_to, 'message');

        if($result && $notification){

            if($notification->send_email){

                $user_to_object = $user_model->getUser($user_to);
                $subject = JText::_("MESSAGE_EMAIL_SUBJECT");
                $content = JText::_("MESSAGE_EMAIL_BODY");
                $content = str_replace('{user}', JFactory::getUser()->get('username'), $content);
                $content = str_replace('{subject}', $subject, $content);
                $recipient = $user_to_object->email;

                $this->sendMail($subject, $content, $recipient);
            }

            $user_model->saveNotification($notification->id, $user_to, 'null', $user_from, $message_id);
        }

        echo json_encode(array('success' => $result));

        $app->close();

    }

    public function send_message2(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_from = JFactory::getUser()->get('id');
        $user_to = JRequest::getInt('user_to');
        $message = JRequest::getString('message');

        $user_model = $this->getModel('user');

        $result = Ideary::sendMessage($user_from, $user_to, $message);

        $message_id = $result;

        $result = ($result == 0)? false : true;

        $notificationType = Ideary::getNotificationTypeByType('message');

        if($result){
            Ideary::saveNotification($notificationType->id, $user_to, 'null', $user_from, $message_id);
        }

        echo json_encode(array('success' => $result));

        $app->close();

    }

    public function set_saw_all_notifications(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_id = JFactory::getUser()->get('id');

        $user_model = $this->getModel('user');

        $result = $user_model->setSawAllNotifications($user_id);

        echo json_encode(array('success' => $result));

        $app->close();
    }

    public function delete_user_image(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_id = JFactory::getUser()->get('id');

        /*$image_file = ideary::getUserImageName($user_id);
        $dir1 = "media/plg_user_profilepicture/images/50/".$image_file;
        $dir2 = "media/plg_user_profilepicture/images/200/".$image_file;
        $dir3 = "media/plg_user_profilepicture/images/original/".$image_file;*/

        $success = ideary::deleteUserImg($user_id);

        if($success){
            /*unlink($dir1);
            unlink($dir2);
            unlink($dir3);*/

            $dir = "templates/beez_20/images/user_profile/".$user_id;
            Ideary::rrmdir($dir);
        }

        echo json_encode(array('success' => $success));

        $app->close();
    }

    public function delete_user_bg_image(){

        // Get the application object.
        $app = JFactory::getApplication();

        $user_id = JFactory::getUser()->get('id');

        $success = ideary::deleteUserBgImage($user_id);

        echo json_encode(array('success' => $success));

        $app->close();
    }

    public function check_email_existence(){

        // Get the application object.
        $app = JFactory::getApplication();

        $email = JRequest::getString('email');

        $exist = ideary::checkEmailExistence($email);

        echo json_encode(array('exist' => $exist));

        $app->close();
    }

    public function delete_message(){
        $app = JFactory::getApplication();

        $message_id = JRequest::getInt('message_id');

        $success = ideary::deleteMessage($message_id);

        echo json_encode(array('success' => $success));

        $app->close();
    }

    public function delete_text(){
        $app = JFactory::getApplication();

        $text_id = JRequest::getInt('text_id');

        $success = ideary::deleteText($text_id);

        echo json_encode(array('success' => $success));

        $app->close();
    }

    public function get_message_to_user_form_content(){
        $app = JFactory::getApplication();

        $user_id = JRequest::getInt('user_id');
        $username = JRequest::getString('username');
        $msg = JRequest::getString('msg');

        $html = ideary::getMessageToUserFormContent($user_id, $username, $msg);

        echo $html;

        $app->close();
    }

    public function get_message_to_user_form_with_input_content(){
        $app = JFactory::getApplication();

        $html = ideary::getMessageToUserFormWithInputContent();

        echo $html;

        $app->close();
    }

    public function save_message(){
        $app = JFactory::getApplication();
        $loggedUser = JFactory::getUser()->get('id');

        $user_id = JRequest::getInt('user_id');
        $message = JRequest::getString('message');

        $result = Ideary::sendMessage($loggedUser, $user_id, $message);

        $message_id = $result;

        $result = ($result == 0)? false : true;

        $notificationType = Ideary::getNotificationTypeByType('message');

        if($result){
            Ideary::saveNotification($notificationType->id, $user_id, 'null', $loggedUser, $message_id);

            $sentMessagesList = Ideary::getMessagesSentByUserId($loggedUser);
            $sentMessagesListHtml = Ideary::getMessageListContent($sentMessagesList);
            echo json_encode(array('success' => $result, 'sentMessagesListHtml' => $sentMessagesListHtml));
        }
        else{
            echo json_encode(array('success' => $result));
        }

        $app->close();
    }

    public function get_users_for_msg_combo(){

        $app = JFactory::getApplication();

        $search = JRequest::getString('search');

        $users = ideary::getUsersForMsgCombo($search);

        $usersComboHtml = ideary::generateUsersForMsgCombo($users);

        echo $usersComboHtml;

        $app->close();
    }

    private function sendMail($subject, $content, $recipient){

        $mailThis =& JFactory::getMailer();
        $mailThis->SetFrom('lleonardis@dbsoftwaresolutions.com', 'Ideary');
        $mailThis->AddReplyTo('lleonardis@dbsoftwaresolutions.com', 'Ideary');

        $mailThis->setSubject($subject);
        $mailThis->isHTML(true);
        $mailThis->Encoding = 'base64';

        $mailThis->setBody($content);
        $mailThis->addRecipient($recipient);
        $mailThis->Send();

    }

    public function get_more_texts_of_author(){

        // Get the application object.
        $app = JFactory::getApplication();

        $userId = JRequest::getInt('userId');
        $offset = JRequest::getInt('offset');
        $text_type = JRequest::getString('text_type');

        switch ($text_type) {
            case "published":
                $texts = ideary::getTextsOfUser($userId, 1, $offset);
                $profile = 1;
                $authorsection = 1;
                $type = 'mine';
                break;
            case "draft":
                $texts = ideary::getTextsOfUser($userId, 0, $offset);
                $profile = 1;
                $authorsection = 1;
                $type = 'draft';
                break;
            case "favourites":
                $texts = ideary::getFavArchTexts(1, $offset);
                $profile = 1;
                $authorsection = 0;
                $type = 'default';
                break;
            case "applauded":
                $texts = ideary::getTextsApplaudedByUser($userId, $offset);
                $profile = 1;
                $authorsection = 0;
                $type = 'default';
                break;
            case "archived":
                $texts = ideary::getFavArchTexts(0, $offset);
                $profile = 0;
                $authorsection = 0;
                $type = 'default';
                break;
        }

        $textHtmlArray = array();

        if(!empty($texts)){
            foreach($texts as $index => $text){
                $textHtmlArray[] = ideary::removeNewLinesFromString(ideary::generateTextContent($text, $userId, ($offset+$index), $profile, $authorsection, false, $type));
            }

        }

        echo json_encode(array('texts' => $textHtmlArray));

        $app->close();

    }
	
	function get_new_notifications() {
        
		$app = JFactory::getApplication();
		
		$user = JFactory::getUser();
		$lastNotificationId = JRequest::getString("lastNotificationId");
		
		$notifications = ideary::getNoSawNotificationsByUser($user->id, $lastNotificationId);
		
		echo json_encode($notifications);
		
		$app->close();
		
	}

}
