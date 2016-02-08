<?php
/**
 * Social Login
 *
 * @version 	1.0
 * @author		SmokerMan, Arkadiy, Joomline
 * @copyright	© 2012. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access.
defined('_JEXEC') or die('(@)|(@)');
?>

<script type="text/javascript">

    $(document).ready(function(){

        $('#login-form-button').click(function(event){
            $('.register-form-error-small').hide();
            var email = $('#modlgn-username');
            var pass = $('#modlgn-passwd');
            var success = true;

            if($.trim($(email).val()) == ""){
                var login_input_container = $(email).parents('.login-input-container');
                $(login_input_container).find('.register-form-error-center-small').html('Requerido');
                $(login_input_container).find('.register-form-error-small').css('right', '-73px');
                $(login_input_container).find('.register-form-error-small').fadeIn('slow');
                success = false;
            }

            if($.trim($(pass).val()) == ""){
                var login_input_container = $(pass).parents('.login-input-container');
                $(login_input_container).find('.register-form-error-center-small').html('Requerido');
                $(login_input_container).find('.register-form-error-small').css('right', '-73px');
                $(login_input_container).find('.register-form-error-small').fadeIn('slow');
                success = false;
            }

            if(success){
                $('#login-form').submit();
            }

            event.preventDefault();
        });
    });

</script>

<?php if ($type == 'logout') : ?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
    <div class="login-greeting">
        <?php echo JText::sprintf('MOD_SLOGIN_HINAME', htmlspecialchars($user->get('name')));	 ?>
    </div>
    <div class="logout-button">
        <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
        <input type="hidden" name="option" value="com_users" />
        <input type="hidden" name="task" value="user.logout" />
        <input type="hidden" name="return" value="<?php echo $return; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<?php else : ?>

    <div id="login-to-ideary"><?php echo JText::_('LOG_IN_TO_IDEARY') ?>!</div>

    <!--<a href="<?php //echo $plugins[0]['link']?>" style="text-decoration: none;">
        <div class="social-button fb-social-button" style="padding: 12px 0 12px 40px; width: 150px; margin-bottom: 10px; float: none;"><?php //echo JText::_('LOG_IN_WITH_FACEBOOK') ?></div>
    </a>

    <a href="<?php //echo $plugins[1]['link']?>" style="text-decoration: none;">
        <div class="social-button tw-social-button" style="padding: 12px 0 12px 40px; width: 150px; margin-bottom: 15px; float: none;"><?php //echo JText::_('LOG_IN_WITH_TWITTER') ?></div>
    </a>-->

    <!-- Descomentar en general.css #enter-with-your-email-address el border-top -->
    <div id="enter-with-your-email-address"><?php echo JText::_('LOG_IN_WITH_YOUR_EMAIL') ?></div>


<?php if ($params->get('inittext')): ?>

    <div class="pretext">
        <p><?php echo $params->get('inittext'); ?></p>
    </div>
    <?php endif; ?>

<!--<div id="slogin-buttons" class="slogin-buttons <?php //echo $moduleclass_sfx?>">
    <?php //if (count($plugins)): ?>
        <?php //foreach($plugins as $link): ?>
            <a href="<?php //echo JRoute::_($link['link']);?>"><span class="<?php //echo $link['class'];?>">&nbsp;</span></a>
        <?php //endforeach; ?>
    <?php //endif; ?>
</div>-->

<div class="slogin-clear"></div>

    <?php if ($params->get('pretext')): ?>
    <div class="pretext">
        <p><?php echo $params->get('pretext'); ?></p>
    </div>
    <?php endif; ?>

<?php if ($params->get('show_login_form')): ?>
    <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
		<?php $extrastyle=""; ?>
		<?php session_start(); ?>
        <?php if($_SESSION["login-error-code"] != 0): ?>

            <?php
            switch ($_SESSION["login-error-code"]) {
                case 1:
                    $login_error_msg = "Contraseña invalida";
                    $right = '-133px';
                    break;
                case 2:
                    $login_error_msg = "Usuario inexistente";
                    $right = '-127px';
                    break;
                case 3:
                    $login_error_msg = "Usuario sin activar";
                    $right = '-126px';
                    break;
            }
            ?>

			<script>
                $(document).ready(function(){
                    $('.register-form-error-small').hide();
                    var email = $('#modlgn-username');
                    var login_input_container = $(email).parents('.login-input-container');
                    $(login_input_container).find('.register-form-error-center-small').html('<?php echo $login_error_msg; ?>');
                    $(login_input_container).find('.register-form-error-small').css('right', '<?php echo $right; ?>');
                    $(login_input_container).find('.register-form-error-small').fadeIn('slow');
                    $('#login-container').slideDown('fast');
                });
            </script>
            <?php $_SESSION["login-error-code"] = 0; ?>
        <?php endif; ?>

        <div class="login-input-container" style="position: relative;">
            <input id="modlgn-username" type="text" name="username" class="inputbox login-to-ideary-input"  size="18" placeholder="<?php echo JText::_('ENTER_EMAIL') ?>"/>
            <div class="register-form-error-small">
                <div class="register-form-error-left-small"></div>
                <div class="register-form-error-center-small">Requerido</div>
                <div class="register-form-error-right-small"></div>
            </div>
        </div>

        <div class="login-input-container" style="position: relative;">
            <input id="modlgn-passwd" type="password" name="password" class="inputbox login-to-ideary-input" size="18" placeholder="<?php echo JText::_('ENTER_PASSWORD') ?>" />
            <div class="register-form-error-small">
                <div class="register-form-error-left-small"></div>
                <div class="register-form-error-center-small">Requerido</div>
                <div class="register-form-error-right-small"></div>
            </div>
        </div>

        <div id="forgot_password_buttton">
            <?php echo JText::_('FORGOT_PASSWORD'); ?>
        </div>

            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
            <div id="forgot_password_buttton" style="    font-size: 10px;    float: right;">
                <label for="modlgn-remember" style="height: 13px; width: 160px; text-align: right;" ><?php echo JText::_('STAY_LOGGED_IN') ?></label>
                <input id="modlgn-remember"  type="checkbox" name="remember" class="inputbox" style="float: right;" value="yes" checked="checked"/>
            
			</div>
            <?php endif; ?>


            <input type="submit" name="Submit" id="login-form-button" value="<?php echo JText::_('SIGN_IN') ?>!" />
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <input type="hidden" name="return" value="<?php echo $return; ?>" />
            <?php echo JHtml::_('form.token'); ?>

        <!--<ul>

            <?php
            //$usersConfig = JComponentHelper::getParams('com_users');
            //if ($usersConfig->get('allowUserRegistration')) : ?>
                <li>
                    <a href="<?php //echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
                        <?php //echo JText::_('MOD_SLOGIN_REGISTER'); ?></a>
                </li>
                <?php //endif; ?>
        </ul>
        <?php //if ($params->get('posttext')): ?>
        <div class="posttext">
            <p><?php //echo $params->get('posttext'); ?></p>
        </div>-->
        <?php //endif; ?>
    </form>
    <?php endif; ?>

<?php endif; ?>