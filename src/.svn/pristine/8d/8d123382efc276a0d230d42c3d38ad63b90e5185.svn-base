<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_forgot_password
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div id="reset-password">
    <div id="close-reset-pass-required-popup" class="close-button" title="<?php echo JText::_('CLOSE') ?>">
        <img src="<?php echo JURI::base() . "templates/beez_20/images/register-form-close-icon.png"; ?>"/>
    </div>
    <div id="reset-password-title"><?php echo JText::_('FORGOT_PASSWORD') ?></div>
    <div id="reset-password-legend"><?php echo JText::_('FORGOT_PASSWORD_LEGEND') ?></div>

	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=reset.confirm'); ?>" method="post" class="form-validate">
	    <input type="text" name="jform[username]" id="jform_username" placeholder="<?php echo JText::_('ENTER_EMAIL_ADDRESS') ?>" value="" class="validate-username required forgot-text-input" size="30" aria-required="true" required="required" aria-invalid="true">
		<input type="text" name="jform[token]" id="jform_token" placeholder="Codigo de Verificacion" value="" class="required forgot-text-input" size="32" aria-required="true" required="required">
        <div id="reset-password-separator"></div>
		<button type="submit" id="reestablish-password-button" class="validate"><?php echo JText::_('RESET'); ?></button>
		<?php echo JHtml::_('form.token'); ?>
    </form>
</div>