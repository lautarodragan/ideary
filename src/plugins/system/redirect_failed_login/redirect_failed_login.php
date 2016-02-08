<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.event.plugin' );

/**
 * Joomla! Redirect Failed Login
 * Version 1.65
 * @author		Roger Noar
 * @package		Joomla
 * @subpackage	System
 */
class  plgSystemRedirect_Failed_Login extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemRedirect_Failed_Login(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	//joomla 1.6+ compatibility code
 	public function onUserLoginFailure($user, $options){  // joomla 1.6+ event
 	    $result = $this->onLoginFailure($user, $options);
 	    return $result;
 	}

	public function onLoginFailure()  // joomla 1.5 event
	{

		$application = JFactory::getApplication();
		$config = JFactory::getConfig();
		$language = $config->getValue('config.language');

		$redirect_destination = $this->params->def('redirect_destination', '');
		$redirect_message = $this->params->def('redirect_message', JText::_('JLIB_LOGIN_AUTHENTICATE'));
		$redirect_destination2 = $this->params->def('redirect_destination2', '');
		$redirect_message2 = $this->params->def('redirect_message2', JText::_('JLIB_LOGIN_AUTHENTICATE'));
		$redirect_destination3 = $this->params->def('redirect_destination3', '');
		$redirect_message3 = $this->params->def('redirect_message3', JText::_('JLIB_LOGIN_AUTHENTICATE'));
		$time_delay = $this->params->def('time_delay', '');
		$clear_cache = $this->params->def('clear_cache', '');
		$language_code2 = $this->params->def('language_code2', '');
		$language_code3 = $this->params->def('language_code3', '');
		$message_type = $this->params->def('message_type', ''); //joomla enqueue message type
		$debug = $this->params->def('debug', ''); //turn on debugging messages

		if ($language != '')
		{	if ($language == $language_code2)
			{	$redirect_destination = $redirect_destination2;
				$redirect_message = $redirect_message2;
			} elseif ($language == $language_code3)
				{	$redirect_destination = $redirect_destination3;
					$redirect_message = $redirect_message3;
				}
		}
		// Get current URL, if current URL matches the redirect URL, then no need to redirect
		// Prevents multiple redirections caused by onLoginFailure
		$uri = JFactory::getURI();
		$url = $uri->toString(); // convert to string
		if ($debug == "1") {
		$application->enqueueMessage('RFL: Failed login detected', 'message');
		$application->enqueueMessage('RFL: URI is = ' . $url, 'message');
		$application->enqueueMessage('RFL: Language is = ' . $language, 'message');
		}
		if ( $time_delay != "0" ) {sleep ( (int)$time_delay ); }  // If a time delay is set, wait before proceeding
		if ( ($url != $redirect_destination) && ($redirect_destination !='') ) {
			if ($clear_cache == "1") {
				$cache = JFactory::getCache();    // Reference the cache
				$cache->clean();// Clean the cache so you don't get stale page after redirection
				if ($debug == "1") {$application->enqueueMessage('RFL: Cleaned Cache', 'message'); }
			}
			if ($debug == "1") {$application->enqueueMessage('RFL: Redirected to: ' . $redirect_destination, 'message'); }
			$application->redirect( $redirect_destination , $redirect_message, $message_type  );
		}
		else {
		$application->enqueueMessage($redirect_message, $message_type);  // if no redirect, still show message
		}
		return true;
	}
}
?>
