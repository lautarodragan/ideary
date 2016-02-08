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
<div id="forgot-username">
	<div id="close-forgot-username-required-popup" class="close-button" title="<?php echo JText::_('CLOSE') ?>">
		<img src="<?php echo JURI::base() . "templates/beez_20/images/close-button.png"; ?>"/>
	</div>
	<div class="remind">

		<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="form-validate">

					<p>Por favor, introduzca la dirección de correo electrónico asociada con su cuenta de usuario. Su nombre de usuario será enviado por correo electrónico a la dirección de correo electrónico guardada.</p>		<fieldset>
				<dl>
								<dt><label id="jform_email-lbl" for="jform_email" class="hasTip required" title="Correo electrónico::Por favor, introduzca la dirección de correo electrónico asociada con su cuenta de usuario.&lt;br /&gt; Su nombre de usuario será enviado por correo electrónico a la dirección de correo electrónico guardada.">Correo electrónico:<span class="star">&nbsp;*</span></label></dt>
					<dd><input type="email" name="jform[email]" class="validate-email required" id="jform_email" value="" size="30" aria-required="true" required="required"></dd>
								<dt></dt>
					<dd></dd>
							</dl>
			</fieldset>
					<div>
				<button type="submit" class="validate"><?php echo JText::_('JSUBMIT'); ?></button>
				<?php echo JHtml::_('form.token'); ?>
				</div>
		</form>
	</div>
</div>