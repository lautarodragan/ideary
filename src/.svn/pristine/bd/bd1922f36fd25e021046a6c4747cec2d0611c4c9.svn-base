<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_user_registration
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if ($params->def('prepare_content', 1))
{
	JPluginHelper::importPlugin('content');
	$module->content = JHtml::_('content.prepare', $module->content, '', 'mod_user_registration.content');
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

 $callbackUrl = '&return=' . $return;
 
    $dispatcher	= JDispatcher::getInstance();

    JPluginHelper::importPlugin('slogin_auth');

    $plugins = array();

    $dispatcher->trigger('onCreateSloginLink', array(&$plugins, $callbackUrl));

require JModuleHelper::getLayoutPath('mod_user_registration', $params->get('layout', 'default'));
