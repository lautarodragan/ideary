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

<div id="forgot-password">

    <div id="close-forgot-pass-required-popup" class="close-button" title="<?php echo JText::_('CLOSE') ?>">
        <img src="<?php echo JURI::base() . "templates/beez_20/images/register-form-close-icon.png"; ?>"/>
    </div>

    <div id="forgot-password-title"><?php echo JText::_('FORGOT_PASSWORD') ?></div>

    <div id="forgot-password-legend"><?php echo JText::_('FORGOT_PASSWORD_LEGEND') ?></div>



	
	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=reset.request'); ?>" method="post" class="form-validate">

	    <input type="text" name="jform[email]" id="jform_email" placeholder="<?php echo JText::_('ENTER_EMAIL_ADDRESS') ?>" value="" class="validate-username required forgot-text-input" size="30" aria-required="true" required="required" aria-invalid="true">

        <div id="forgot-password-separator"></div>

		<button type="submit" id="reestablish-password-button" class="validate"><?php echo JText::_('RESET'); ?></button>
	    <?php echo JHtml::_('form.token'); ?>


    </form>

</div>