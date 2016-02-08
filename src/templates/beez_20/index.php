<?php
/**
 * @package                Joomla.Site
 * @subpackage	Templates.beez_20
 * @copyright        Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

// check modules
$showRightColumn	= ($this->countModules('position-3') or $this->countModules('position-6') or $this->countModules('position-8'));
$showbottom			= ($this->countModules('position-9') or $this->countModules('position-10') or $this->countModules('position-11'));
$showleft			= ($this->countModules('position-4') or $this->countModules('position-7') or $this->countModules('position-5'));

if ($showRightColumn==0 and $showleft==0) {
	$showno = 0;
}

JHtml::_('behavior.framework', true);


// get params
$color				= $this->params->get('templatecolor');
$logo				= $this->params->get('logo');
$navposition		= $this->params->get('navposition');
$app				= JFactory::getApplication();
$doc				= JFactory::getDocument();
$templateparams		= $app->getTemplate(true)->params;

$doc->addStyleSheet($this->baseurl.'/templates/system/css/system.css');
$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/position.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/layout.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/print.css', $type = 'text/css', $media = 'print');

$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/general.css?v=26', $type = 'text/css', $media = 'screen,projection');
//$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/jquery.jscrollpane.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/jquery.mCustomScrollbar.css', $type = 'text/css', $media = 'screen,projection');

//$files = JHtml::_('stylesheet', 'templates/'.$this->template.'/css/general.css', null, false, true);
//$files[] = JHtml::_('stylesheet', 'templates/'.$this->template.'/css/jquery.jscrollpane.css', null, false, true);

if ($files):
	if (!is_array($files)):
		$files = array($files);
	endif;
	foreach($files as $file):
		$doc->addStyleSheet($file . "?v=4");
	endforeach;
endif;

$doc->addStyleSheet('templates/'.$this->template.'/css/'.htmlspecialchars($color).'.css');
if ($this->direction == 'rtl') {
	$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/template_rtl.css');
	if (file_exists(JPATH_SITE . '/templates/' . $this->template . '/css/' . $color . '_rtl.css')) {
		$doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/css/'.htmlspecialchars($color).'_rtl.css');
	}
}

//$doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/md_stylechanger.js', 'text/javascript');
$doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/hide.js', 'text/javascript');

$user = JFactory::getUser();
$lang_tag = JFactory::getLanguage()->getTag();
$actual_langCode = substr($lang_tag, 0, 2);
	
	
//$userstatus=$app->getUserState('users.login.form.errorlogin');
$resetpass=$app->getUserState('users.login.form.resetpass');
//var_dump($baduserregis);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"  xmlns:fb="http://www.facebook.com/2008/fbml" itemscope itemtype="http://schema.org/Article" >
<head>

    <link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Philosopher:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

	<meta name="viewport" content="width=device-width">
    <link href='http://fonts.googleapis.com/css?family=Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<jdoc:include type="head" />
	<script type="text/javascript">
	var errorlogin = false;
	<?php if ($userstatus===true):?>
		errorlogin = true;
	<?php endif;?>
	</script>

	
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery-1.6.2.min.js', 'text/javascript'); ?>
<?php $doc->addScript("//code.jquery.com/ui/1.10.4/jquery-ui.js", 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/underscore-min.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.lightbox_me.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/helper.js?v=7', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/mousewheel.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/sugar-1.3.9.min.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.dotdotdot-1.5.9.min.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/jquery/iphone-style-checkboxes.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.wysiwyg.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/wysiwyg.rmFormat.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.placeholder.js', 'text/javascript'); ?>
<?php //$doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.mousewheel.js', 'text/javascript'); ?>
<?php //$doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.jscrollpane.min.js', 'text/javascript'); ?>
<?php $doc->addScript($this->baseurl.'/templates/'.$this->template.'/javascript/jquery.mCustomScrollbar.concat.min.js', 'text/javascript'); ?>

<?php $doc->addStyleSheet($this->baseurl.'/templates/'.$this->template.'/style.css', $type = 'text/css', $media = 'screen'); ?>

<!--<link href="<?php //echo $this->baseurl ?>/templates/<?php //echo $this->template; ?>/css/dropzone.css" rel="stylesheet" type="text/css" />-->
<!--[if lte IE 6]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<?php if ($color=="personal") : ?>
<style type="text/css">
#line {
	width:98% ;
} 
.logoheader {
	height:200px;
}
#header ul.menu {
	display:block !important;
	width:98.2% ;
}
</style>
<?php endif; ?>
<![endif]-->

<!--[if IE 7]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/ie7only.css" rel="stylesheet" type="text/css" />
<![endif]-->

<?php if($user): ?>
<style type="text/css">
    #contentarea{
        <?php echo ideary::getUserBackground($user->get('id')) ?>
    }
</style>
<?php endif ?>

<script type="text/javascript">
	var big ='<?php echo (int)$this->params->get('wrapperLarge');?>%';
	var small='<?php echo (int)$this->params->get('wrapperSmall'); ?>%';
	var altopen='<?php echo JText::_('TPL_BEEZ2_ALTOPEN', true); ?>';
	var altclose='<?php echo JText::_('TPL_BEEZ2_ALTCLOSE', true); ?>';
	var bildauf='<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/plus.png';
	var bildzu='<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/minus.png';
	var rightopen='<?php echo JText::_('TPL_BEEZ2_TEXTRIGHTOPEN', true); ?>';
	var rightclose='<?php echo JText::_('TPL_BEEZ2_TEXTRIGHTCLOSE', true); ?>';
	var fontSizeTitle='<?php echo JText::_('TPL_BEEZ2_FONTSIZE', true); ?>';
	var bigger='<?php echo JText::_('TPL_BEEZ2_BIGGER', true); ?>';
	var reset='<?php echo JText::_('TPL_BEEZ2_RESET', true); ?>';
	var smaller='<?php echo JText::_('TPL_BEEZ2_SMALLER', true); ?>';
	var biggerTitle='<?php echo JText::_('TPL_BEEZ2_INCREASE_SIZE', true); ?>';
	var resetTitle='<?php echo JText::_('TPL_BEEZ2_REVERT_STYLES_TO_DEFAULT', true); ?>';
	var smallerTitle='<?php echo JText::_('TPL_BEEZ2_DECREASE_SIZE', true); ?>';

    var base_url = '<?php echo $this->baseurl; ?>';
    var profile_url = '<?php echo JRoute::_($this->baseurl."/index.php?option=com_users&view=profile&user_id=" . $user->id . "&lang=" . $actual_langCode); ?>';
    var clap_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=clap" ?>';
    var add_to_favorites_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=add_to_favorites" ?>';
    var undo_add_to_favorites_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=undo_add_to_favorites" ?>';
    var add_to_saved_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=add_to_saved" ?>';
    var unarchive_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=unarchive" ?>';
    var denounce_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=denounce" ?>';
    var send_denounce_mail = '<?php echo $this->baseurl."/index.php?option=com_content&task=sendDenounceMail" ?>';
    var follow_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=follow" ?>';
    var unfollow_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=unfollow" ?>';
    var invited_to_write_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=invited_to_write" ?>';
    var send_message_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=send_message" ?>';
    var send_message2_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=send_message2" ?>';
    var set_saw_all_notifications = '<?php echo $this->baseurl."/index.php?option=com_users&task=set_saw_all_notifications" ?>';
    var comment_text_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=comment_text" ?>';
    var comment_vote_up_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=comment_vote_up" ?>';
    var comment_vote_down_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=comment_vote_down" ?>';
    var get_texts_ranking_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=get_texts_ranking" ?>';
    var my_profile_url = '<?php echo $this->baseurl."/index.php?option=com_users&view=profile&user_id=".$user->get('id') ?>';
    var get_texts_of_author_url = '<?php echo $this->baseurl."/index.php?option=com_contact&task=get_texts_of_author" ?>';
    var delete_message_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=delete_message" ?>';
    var get_message_to_user_form_content_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_message_to_user_form_content" ?>';
    var save_message_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=save_message" ?>';
    var get_users_for_msg_combo_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_users_for_msg_combo" ?>';
    var get_message_to_user_form_with_input_content_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_message_to_user_form_with_input_content" ?>';
    var delete_text_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=delete_text" ?>';
    var get_users_who_applauded_text_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_users_who_applauded_text" ?>';
    var get_users_who_applauded_text_page_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_users_who_applauded_text_page" ?>';
    var get_users_who_follow_me_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_users_who_follow_me" ?>';
    var get_users_who_follow_me_page_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_users_who_follow_me_page" ?>';
    var get_users_who_i_am_following_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_users_who_i_am_following" ?>';
    var get_users_who_i_am_following_page_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_users_who_i_am_following_page" ?>';
    var home_url = '<?php echo $this->baseurl."/index.php" ?>';
    var get_more_texts_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=get_more_texts" ?>';
    var get_more_texts_of_author_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=get_more_texts_of_author" ?>';
    var get_more_texts_ranking_url = '<?php echo $this->baseurl."/index.php?option=com_content&task=get_more_texts_ranking" ?>';

    
	var get_texts_favourites_of_author_url = '<?php echo $this->baseurl."/index.php?option=com_contact&task=get_texts_favourites_of_author_url&authorsection=0" ?>';
	var get_texts_archived_of_author_url = '<?php echo $this->baseurl."/index.php?option=com_contact&task=get_texts_archived_of_author_url&authorsection=0" ?>';
	var get_texts_draft_of_author_url = '<?php echo $this->baseurl."/index.php?option=com_contact&task=get_texts_draft_of_author_url" ?>';
	
    var get_texts_applauded_by_author_url = '<?php echo $this->baseurl."/index.php?option=com_contact&task=get_texts_applauded_by_author&authorsection=0" ?>';
    var delete_user_image_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=delete_user_image" ?>';
    var delete_user_bg_image_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=delete_user_bg_image" ?>';
    var check_email_existence_url = '<?php echo $this->baseurl."/index.php?option=com_users&task=check_email_existence" ?>';

    var ALREADY_APPLAUDED = "<?php echo JText::_('ALREADY_APPLAUDED'); ?>";
    var APPLAUDED = "<?php echo JText::_('APPLAUDED'); ?>";
    var JCLAP = "<?php echo JText::_('JCLAP'); ?>";
    var ADD_TO_FAVORITES = "<?php echo JText::_('ADD_TO_FAVORITES') ?>";
    var UNDO_ADD_TO_FAVORITES = "<?php echo JText::_('UNDO_ADD_TO_FAVORITES') ?>";
    var SAVE = "<?php echo JText::_('SAVE') ?>";
    var UNARCHIVE = "<?php echo JText::_('UNARCHIVE') ?>";
    var YOU_ALREADY_REPORTED_THIS_TEXT = "<?php echo JText::_('YOU_ALREADY_REPORTED_THIS_TEXT') ?>";
    var FOLLOW = "<?php echo JText::_('FOLLOW') ?>";
    var UNFOLLOW = "<?php echo JText::_('UNFOLLOW') ?>";
    var FIELD_IS_REQUIRED = "<?php echo JText::_('FIELD_IS_REQUIRED') ?>";
    var SUBJECT = "<?php echo JText::_('SUBJECT') ?>";
    var MESSAGE = "<?php echo JText::_('MESSAGE') ?>";
    var SHOW_NOTIFICATIONS = "<?php echo JText::_('SHOW_NOTIFICATIONS') ?>";
    var HIDE_NOTIFICATIONS = "<?php echo JText::_('HIDE_NOTIFICATIONS') ?>";
    var SEARCH = "<?php echo JText::_('SEARCH') ?>";
    var FOLLOW_AUTHOR = "<?php echo JText::_('FOLLOW_AUTHOR') ?>";
    var FOLLOWING = "<?php echo JText::_('FOLLOWING') ?>";
    var CONTINUE_READING = "<?php echo JText::_('CONTINUE_READING') ?>";
    var READ_LESS = "<?php echo JText::_('READ_LESS') ?>";
    var READ_MORE = "<?php echo JText::_('READ_MORE') ?>";
    var DELETE_IMAGE = '<?php echo JText::_('DELETE_IMAGE') ?>';
    var SELECT_IMAGE = '<?php echo JText::_('SELECT_IMAGE') ?>';
    var JYES = '<?php echo JText::_('JYES') ?>';
    var CHECK_NO = '<?php echo JText::_('CHECK_NO') ?>';
    var THANKS = '<?php echo JText::_('THANKS') ?>';
    var XCOMMENTS = '<?php echo JText::_('X-COMMENTS') ?>';
    var XCOMMENT = '<?php echo JText::_('X-COMMENT') ?>';



    var USER_LOGGED = <?php echo (JFactory::getUser()->get('id') == 0)? 'false' : 'true' ?>;
    var USER_ID = <?php echo JFactory::getUser()->get('id') ?>;
    var USER_NAME = "<?php echo Ideary::getUserName(JFactory::getUser()->get('id')) ?>";

    var comment_img = "<?php echo JURI::base()."templates/beez_20/images/commenter1.png" ?>";
	
    
	<? if ($user->id) { ?>
	var no_read_notifications_count = 0;
	var lastNotificationId = <?=ideary::getNotificationMaxIdByUser($user->id)?>;
	var recentUnseenNotifications = <?=json_encode(ideary::getLastSeenNotificationsByUser($user->id))?>;
	<? } ?>
	
	function submitlogoutform(){
		document.logoutForm.submit();
	}

	function updateScrollbarPosition(){
		var scrollLeft = parseInt($("#h-scroll-container > div").scrollLeft());
		var screenWidth = parseInt($("body").width());
		var homeWidth = parseInt($("#h-scroll-container > div ul.ul-text-list").width());
		var screenRelativePosition = scrollLeft / homeWidth;

		var scrollbarPosition = screenRelativePosition * screenWidth;
		$("#scrollbarPosition").css("left", scrollbarPosition);
	}
	
    $(document).ready(function(){
        $.preloadImages(
            "<?php echo JURI::base()."templates/beez_20/images/user-box-bg.png"?>",
            "<?php echo JURI::base()."templates/beez_20/images/text-bg-hover.png"?>",
            "<?php echo JURI::base()."templates/beez_20/images/register-form-error-center.png"?>",
            "<?php echo JURI::base()."templates/beez_20/images/register-form-error-center-small.png"?>"
        );

        $('input, textarea').placeholder();

        var screen_height = $(window).height();
        var topbar_height = $('#topbar').height();
        var notifications_container_height = Math.round((screen_height-topbar_height)/2);
        $('#notifications-container').css('max-height', notifications_container_height+'px');

		<?php if ($_GET["view"]=="featured"){ ?>
		window.scrollVelocity = 1;
		window.scrollIntervalId = 0;
		
		$("#moveForward").mouseenter(function(event){
			scrollIntervalId = setInterval(window.autoScroll, 20);
		
		});
		
		$("#moveForward").mouseleave(function(event){
			clearInterval(window.scrollIntervalId);
		
		});
		
		$("#moveForward").mousemove(function(event){
			event.stopPropagation();
			
			var offsetX = event.offsetX;
			
			if (offsetX == undefined) {
			     offsetX = event.pageX - $("#moveForward").offset().left;
			}
			
			window.scrollVelocity = parseInt(offsetX / 2);
		
		});
		
		window.autoScroll = function() {
			$("#h-scroll-container > div").scrollLeft($("#h-scroll-container > div").scrollLeft() + scrollVelocity)
		};
		
		$("#wrapper2").mousemove(function(event){
			var distance = $("#wrapper2").width() - event.pageX - $("#moveForward").width();
			var relativeDistance = distance / ($("#wrapper2").width() - $("#moveForward").width());
			
			var opacity = .6 - (relativeDistance * 3);
			
			if (opacity < .01) {
				$("#moveForward").css("display", "none");
			} else {
				$("#moveForward").css("display", "block");
				$("#moveForward").css("opacity", opacity);
			}
			
		});
		
		$(document).keydown(function(event){
			if (event.keyCode == 36) {
				$("#h-scroll-container > div").scrollLeft(0);
			}
		});
		
		
		<?php } ?>
    });
	
	<? if($user->id){ ?>
	$(document).ready(function(){
		hookNotifications();
	});
	<? } ?>
	
</script>

<?php 
if ($_GET["option"]=="com_content"){
	echo Ideary::printOpenGraphContent($_GET["id"]);?>
	<meta property="og:url" content="<?php echo "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "&from=facebook_destacado";?>"/>
<?php }else{ ?>
	<meta property="og:description" content="Ideary"/>
	<meta property="og:url" content="<?php echo "http://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];?>"/>
<?php }  ?>
	<meta property="og:site_name" content="Ideary"/>
	<meta property="og:type" content="website"/>
	<meta property="fb:app_id" content="136326986564464"/>
	<meta property="fb:admins" content="1325677070"/>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-42633149-1', 'ideary.co');

    <? if($user->id){ ?>
        ga('set', 'dimension1', 'logged-in');
    <? } else { ?>
        ga('set', 'dimension1', 'not-logged-in');
    <? } ?>

    ga('send', 'pageview');

</script>
</head>

<? if(!$user->id){ ?>
<body class="guest" >
<? }else{ ?>
<body class="user" >
<? } ?>

<div id="topbar">
    <div id="logo-container">
        <a id="logo-link" href="index.php">
            <div id="ideary-logo"></div>
        </a>
    </div>
    <div class="topbar-divisor topbar-divisor-first" style="float: left; margin-left: 12px; margin-right: 12px;"></div>

	<!-- Begin Filters-->
	<?php 	$menu = $app->getMenu(); ?>
	
	<form id="text-filters-form" action="/index.php" method="post" autocomplete="off" >
        <?php
            $text_search_post = ($_POST["text-search"])? $_POST["text-search"] : '';
            $category_post = $_POST["category"];
            $period_post = $_POST["period"];
        ?>
        <div id="text-search">
            <input type="text" name="text-search" id="text-search-input" value="<?php echo $text_search_post ?>" placeholder="<?php echo JText::_('SEARCH','es-ES') ?>">
            <span id="magnifying-glass">
                <img src="<?php echo JURI::base() . "templates/beez_20/images/magnifying-glass.png"; ?>" alt="magnifying glass">
            </span>
        </div>

        <div class="topbar-divisor" style="float: left;  margin-left: 12px; margin-right: 5px;"></div>

		<?php //if ($menu->getActive() == $menu->getDefault( JFactory::getLanguage()->getTag())): ?>
		<?php //si saco lo de abajo y pongo lo de arriba, lo mostramos en el home?>
		<?php if ($text_search_post!=""): ?>
			<?php $filter_arrow_class = "filter-right-arrow"; ?>
			<?php if(!((is_null($category_post) || ($category_post=="")) && (is_null($period_post) || ($period_post == "")))): ?>
				<style type="text/css">
					#combos{
						display: block;
					}
				</style>
				<script type="text/javascript">
					combo_hidden = false;
				</script>
				<?php $filter_arrow_class = "filter-left-arrow"; ?>
			<?php endif ;?>
			
			<div id="filters">
				<div id="filter-icon"></div>
				<div id="filter-text"><?php echo JText::_("JGLOBAL_FILTER_LABEL") ?></div>
				<div id="filter-arrow" class="<?php echo $filter_arrow_class ?>"></div>
			</div>
			<div id="combos">
				<div id="category-combo">
					<?php
					$categories = ideary::getCategoriesForCombo();
					?>
					<select name="category" id="category" class="combo">
						<option value=""><?php echo JText::_("SELECT") ?></option>
						<option value="-1" <?php echo ($category_post == -1)? 'selected="selected"' : '' ?>><?php echo JText::_("JALL") ?></option>
						<?php foreach($categories as $category): ?>
							<?php $selected = ($category->id == $category_post)? 'selected="selected"' : '' ?>
							<option value="<?php echo $category->id ?>" <?php echo $selected ?>><?php echo $category->title ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<?php if ($text_search_post!=""):?>
				<div id="period-combo">
					<?php
					$periods = array(
						'' => JText::_("SELECT"),
						'TODAY' => JText::_("TODAY"),
						'YESTERDAY' => JText::_("YESTERDAY"),
						'LAST-WEEK' => JText::_("LAST-WEEK"),
						'LAST-MONTH' => JText::_("LAST-MONTH"),
						'LAST-YEAR' => JText::_("LAST-YEAR")
					);

					$disabled = (is_null($category_post) || ($category_post == ""))? 'disabled="disabled"' : '';
					?>
					<select name="period" id="period" class="combo" <?php echo $disabled ?>>
						<?php foreach($periods as $key => $value): ?>
							<?php $selected = ($key == $period_post)? 'selected="selected"' : '' ?>
							<option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $value ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<?php endif;?>
			</div>
			<?php endif; ?>
    </form>
	
	<!-- End Filters-->
	<div class="gmailIcon socialIcon">
		<a href="mailto:escribime@ideary.co" target="blank" ></a>
	</div>
	<div class="facebookIcon socialIcon">
		<a href="http://on.fb.me/18lWVPa" target="blank" ></a>
	</div>
	<div class="twitterIcon socialIcon">
		<a href="http://bit.ly/1ajJ9fA" target="blank" ></a>
	</div>
	
    <div id="footer-link-container">
        <div id="footer-link">
            <img src="<?php echo JURI::base() . "templates/beez_20/images/footer-icon.png"; ?>" alt="Footer" title="Footer">
        </div>

        <div id="footer-container" class="menu-slider">
            <jdoc:include type="modules" name="position-14" />
            <div class="footer-container-item menu-slider-item">
                <a href="#">
                    <div class="footer-container-item-content">
                    <?php echo JText::_('ABOUT_IDEARY') ?>
                    </div>
                </a>
            </div>
            <div class="footer-container-item menu-slider-item">
                <a href="#">
                    <div class="footer-container-item-content">
                        <?php echo JText::_('TERMS_AND_CONDITIONS') ?>
                    </div>
                </a>
            </div>
            <div class="footer-container-item menu-slider-item">
                <a href="#">
                    <div class="footer-container-item-content">
                        <?php echo JText::_('COPYRIGHT') ?>
                    </div>
                </a>
            </div>
            <div class="footer-container-item menu-slider-item">
                <a href="#">
                    <div class="footer-container-item-content">
                        <?php echo JText::_('CONTACT') ?>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="topbar-divisor" style="float: right; margin-left: 5px; margin-right: 5px;"></div>

    <?php if(!$user->id): ?>
        <div id="registersite_link">
            <?php echo JText::_('JOIN_IDEARY') ?>
        </div>

        <div class="topbar-divisor" style="float: right; margin-left: 5px; margin-right: 5px;"></div>
    <?php endif ?>

    <div id="login-link-wrapper">
		<div id="login-link-container">
			<?php if(!$user->id){?>
				<div id="username" class="username_login_class">
					<?php echo JText::_('LOGIN') ?>
				</div>
				
			<?php }else{
				$session = JFactory::getSession();
				//$user_picture = $session->get('user_picture');
				$user_picture = Ideary::getUserImage($user->id, "50", $user->get('name'));
				
			?>

			<div id="user-logo">
				<?php echo $user_picture; ?>
			</div>
			<div id="username">
				<?php echo Ideary::getUserName($user->id);?>
			</div>

			<?php }?>
		</div>


        <?php if(!$user->id):?>
			<div id="login-container" class="menu-slider" style="width: 190px; padding: 17px 20px 20px 20px;">
                <jdoc:include type="modules" name="loginpopupbox" style="beezDivision" headerLevel="3" />
            </div>
        <?php else: ?>

            <div id="login-container" class="menu-slider" style="width: 100%; padding: 0;">
                <hr/>
                
                <div class="profile-slider-item-container menu-slider-item">
                    <a href="<?php echo JRoute::_('index.php?option=com_users&mytexts=1&view=profile&user_id='.$user->id);?>">
                        <div class="profile-slider-item my-texts">
                            <div class="profile-slider-item-icon"></div>
                            <div class="profile-slider-item-text"><?php echo JTEXT::_('MYTEXTS') ?></div>
                        </div>
                    </a>
                </div>
				<div style="display:none;">
				<?php  echo JRoute::_($this->baseurl."/index.php?option=com_users&view=profile&user_id=" . $user->id . "&lang=es");?>
				<?php  echo JRoute::_($this->baseurl."/index.php?option=com_users&view=profile&user_id=" . $user->id . "&lang=en");?>
				<?php  echo JRoute::_($this->baseurl."/index.php?option=com_users&view=profile&user_id=" . $user->id . "&lang=pt");?>
				</div>
                <div class="profile-slider-item-container menu-slider-item">
                    <a href="<?php echo 'index.php?option=com_users&view=profile&user_id='.$user->id . '&draft=1';?>">
                        <div class="profile-slider-item my-drafts">
                            <div class="profile-slider-item-icon"></div>
                            <div class="profile-slider-item-text"><?php echo JTEXT::_('MYDRAFTS') ?></div>
                        </div>
                    </a>
                </div>

				<!-- <div class="profile-slider-item-container menu-slider-item">
                    <a href="<?php //echo 'index.php?option=com_users&view=message&user_id='.$user->id;?>">

                        <div class="profile-slider-item messages">
                            <div class="profile-slider-item-icon"></div>
                            <div class="profile-slider-item-text"><?php //echo JTEXT::_('MESSAGES') ?></div>
                        </div>
                    </a>                    
					<?php /*$unread =Ideary::getTextUnread($user->id);
						if ($unread>0){
							echo '<div id="messages-count">+' . $unread . '</div>';
						}*/
					?>
                </div>-->
				
				<hr/>
				
				<div class="profile-slider-item-container menu-slider-item">
                    <a href="<?php echo JRoute::_('index.php?option=com_users&view=profile&layout=edit&user_id='.$user->id);?>">
                        <div class="profile-slider-item profile">
                            <div class="profile-slider-item-icon"></div>
                            <div class="profile-slider-item-text"><?php echo JTEXT::_('EDIT_PROFILE') ?></div>
                        </div>
                    </a>
                </div>	
                			
                <div class="profile-slider-item-container menu-slider-item">
                    <a href="<?php echo JRoute::_('index.php?option=com_users&view=profile&layout=userconfiguration&user_id='.$user->id);?>">
                        <div class="profile-slider-item configuration">
                            <div class="profile-slider-item-icon"></div>
                            <div class="profile-slider-item-text"><?php echo JTEXT::_('CONFIGURATION') ?></div>
                        </div>
                    </a>
                </div>
				
				<hr/>
				
				
                <div class="profile-slider-item-container menu-slider-item">
					<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" name = "logoutForm" id="logoutForm">
						<div>
							<input type="hidden" name="option" value="com_users" />
							<input type="hidden" name="task" value="user.logout" />
							<input type="hidden" name="return" value="<?php echo $return; ?>" />
							<?php echo JHtml::_('form.token');?>	
						</div>
					</form>		
					<a href="#" onclick="submitlogoutform();return false;" id="profile-slider-logout">
                        <div class="profile-slider-item logout">
                            <div class="profile-slider-item-icon"></div>
                            <div class="profile-slider-item-text"><?php echo JTEXT::_('LOGOUT') ?></div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endif; ?>
	</div>
    <div class="topbar-divisor" style="float: right; margin-left: 5px; margin-right: 5px;"></div>

    <?php if($user->id) { ?>

        <div id="notifications-wrapper">
			<div id="notifications-containera">
				<div id="notifications-icon">
					<div id="notifications-red-box" style="display: none; " >0</div>
				</div>
			</div>
			
            <div id="notifications-container" class="menu-slider"></div>

        </div>

        <script type="text/javascript">
            var amount_notifications = <?php echo count($notifications); ?>;
        </script>

        <div class="topbar-divisor" style="float: right; margin-right: 5px; margin-left: 5px"></div>

		<?php } ?>
		
		<a id="stumble-link" href="<?php echo JRoute::_("index.php?option=com_content&view=article&task=stumble") ?>">
            <div id="stumble-text">
                <div id="stumble-icon"></div>
                <div style="clear: both;"></div>
            </div>
		</a>
		
		<a id="write-link" href="<?php echo JRoute::_("index.php?option=com_content&view=form&layout=edit&new=1") ?>">
            <div id="write-text-2">
                <div id="write-text-2-icon"></div>
                <div id="write-text-2-label">Inspirá</div>
                <div style="clear: both;"></div>
            </div>
		</a>
	</div>


	<div id="all">
		<div id="back">
			<div id="<?php echo $showRightColumn ? 'contentarea2' : 'contentarea'; ?>">
					<div id="breadcrumbs">
						<jdoc:include type="modules" name="position-2" />
					</div>
				<?php if ($navposition=='left' and $showleft) : ?>
						<div class="left1 <?php if ($showRightColumn==NULL){ echo 'leftbigger';} ?>" id="nav">
								<jdoc:include type="modules" name="position-7" style="beezDivision" headerLevel="3" />
								<jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
								<jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />
						</div><!-- end navi -->
				<?php endif; ?>

					<div id="<?php echo $showRightColumn ? 'wrapper' : 'wrapper2'; ?>" <?php if (isset($showno)){echo 'class="shownocolumns"';}?>>
						<?php if ($_GET["view"]=="featured"){ ?>
						<div id="moveForward"></div>
						
						<div id="scrollbar">
							<div id="scrollbarPosition">
							
							</div>
						</div>
						
						<?php } ?>
						<div id="main">
						<?php if ($this->countModules('position-12')): ?>
								<div id="top">
								<jdoc:include type="modules" name="position-12"   />
								</div>
						<?php endif; ?>
								<jdoc:include type="component" />
								<jdoc:include type="modules" name="homelist" />
						</div><!-- end main -->
					</div><!-- end wrapper -->

				<?php if ($showRightColumn) : ?>
						<h2 class="unseen">
								<?php echo JText::_('TPL_BEEZ2_ADDITIONAL_INFORMATION'); ?>
						</h2>
						<div id="close">
								<a href="#" onclick="auf('right')">
										<span id="bild">
												<?php echo JText::_('TPL_BEEZ2_TEXTRIGHTCLOSE'); ?></span></a>
						</div>


						<div id="right">
								<a id="additional"></a>
								<jdoc:include type="modules" name="position-6" style="beezDivision" headerLevel="3"/>
								<jdoc:include type="modules" name="position-8" style="beezDivision" headerLevel="3"  />
								<jdoc:include type="modules" name="position-3" style="beezDivision" headerLevel="3"  />
						</div><!-- end right -->
					<?php endif; ?>

				<?php if ($navposition=='center' and $showleft) : ?>
						<div class="left <?php if ($showRightColumn==NULL){ echo 'leftbigger';} ?>" id="nav" >
								<jdoc:include type="modules" name="position-7"  style="beezDivision" headerLevel="3" />
								<jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
								<jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />
						</div><!-- end navi -->
				<?php endif; ?>
					<div class="wrap"></div>
			</div> <!-- end contentarea -->
		</div><!-- back -->
	</div><!-- all -->
	<jdoc:include type="modules" name="perfilpublico" />
	<div id="footer-outer">
		<?php if ($showbottom) : ?>
		<div id="footer-inner">
			<div id="bottom">
					<div class="box box1"> <jdoc:include type="modules" name="position-9" style="beezDivision" headerlevel="3" /></div>
					<div class="box box2"> <jdoc:include type="modules" name="position-10" style="beezDivision" headerlevel="3" /></div>
					<div class="box box3"> <jdoc:include type="modules" name="position-11" style="beezDivision" headerlevel="3" /></div>
			</div>
		</div>
		<?php endif ; ?>		  
	</div>
	<jdoc:include type="modules" name="debug" />
	<div id="login-required-popup">
		<div id="close-login-required-popup" class="close-button" title="<?php echo JText::_('CLOSE') ?>">
			<img src="<?php echo JURI::base() . "templates/beez_20/images/close-button.png"; ?>"/>
		</div>
		Debes Logearte
	</div>
	<?php if(!$user->id){?>
		<jdoc:include type="modules" name="forgot_password" />
		<jdoc:include type="modules" name="reset_password" />
		<jdoc:include type="modules" name="user_registration" />		
	<?php }?>
	<div id="ajax_messages">
	
	</div>
	<?php
	if(isset($_GET['errorcode'])){ 
		if($_GET['errorcode'] == "111"){ ?>
			<script type="text/javascript">wrong_registration('wrong_registration','<?php echo $actual_langCode;?>');</script>
	<?php } 
	}
	
	if(isset($_GET['forgot'])){ 
		if($_GET['forgot'] == "confirm"){ ?>
			<script type="text/javascript">forgot_confirm('forgot_confirm','<?php echo $actual_langCode;?>');</script>
	<?php }
		if($_GET['forgot'] == "complete"){?>
			<script type="text/javascript">forgot_complete('forgot_complete','<?php echo $actual_langCode;?>');</script>
	<?php }	
	
	if($_GET['forgot'] == "success"){
	?>
			<script type="text/javascript">forgot_success('forgot_success','<?php echo $actual_langCode;?>');</script>
	<?php }	
	}
	
	if(isset($_GET['codesuccess'])){ 
		if($_GET['codesuccess'] == 111){?>
			<script type="text/javascript">success_registration('success_registration','<?php echo $actual_langCode;?>');</script>
	<?php }elseif($_GET['codesuccess'] == 110){?>
			<script type="text/javascript">success_registration_pre('success_registration','<?php echo $actual_langCode;?>');</script>
	<?php } 
	}
	?>
	
	<?php if ($resetpass===true):?>
			<script type="text/javascript">
				hideAllTopBarSliders();
				$('#reset-password').lightbox_me({
						centered: true,
						closeSelector: '#close-reset-pass-required-popup'
				});
			</script>
	<?php endif;?>

	<?php if ($app->getUserState('users.login.form.errorlogin')===true && $app->getUserState('users.login.errorlogincount')<=1):?>
			<script type="text/javascript">
				//$('#login-container').slideDown('fast');
			</script>
			<?php $cont = (int) $app->getUserState('users.login.errorlogincount');
				$cont+=1;
				$app->setUserState('users.login.errorlogincount',$cont);
			?>
	<?php endif;?>
	<?php if ($app->getUserState('users.registration.errorregis')===true && $app->getUserState('users.registration.errorregiscount')<=1):?>
			<script type="text/javascript">
				hideAllTopBarSliders();
				login_container_showed = true;
				document.getElementById("message_not_logged_in_action").style.display="none";
				$('#register_form').lightbox_me({
						centered: true,
						closeSelector: '#close-register_form-required-popup',
                        onClose: function(){
                            $('.register-form-error').hide();
                        }
					});
			</script>
			<?php $cont = (int) $app->getUserState('users.registration.errorregiscount');
				$cont+=1;
				$app->setUserState('users.registration.errorregiscount',$cont);
			?>
	<?php endif;?>

    <div id="message-send-popup-container" class="message-popup-container">
        <div class="message-popup-close-icon message-popup-cancel" title="<?php echo JTEXT::_('CLOSE') ?>"></div>

        <div id="message-popup-title">
            <div id="message-popup-title-bg"></div>
            <div id="message-popup-title-text"><?php echo str_replace('{USER}', $GLOBALS["userName"], JTEXT::_('SEND_MESSAGE_TO_USER')) ?></div>
            <div style="clear: both;"></div>
        </div>
        <textarea id="message-popup-msg" placeholder="<?php echo JTEXT::_('WRITE_MESSAGE') ?>..."></textarea>

        <div id="message-popup-buttons-container">
            <div id="message-popup-send-button" class="standard-button"><?php echo JTEXT::_('SEND') ?></div>
            <div id="message-popup-cancel-button" class="standard-button message-popup-cancel"><?php echo JTEXT::_('JCANCEL') ?></div>
        </div>
    </div>

    <div id="message-sent-popup-container" class="message-popup-container" style="padding-top: 70px; padding-bottom: 78px;">
        <div class="message-popup-close-icon message-popup-cancel" title="<?php echo JTEXT::_('CLOSE') ?>"></div>

        <div id="message-sent-center">
            <div id="message-sent-img"></div>
            <div id="message-sent-text"><?php echo JTEXT::_('MESSAGE_SENT') ?></div>
        </div>
    </div>

    <div id="user-box">

        <div id="user-box-close-icon" title="<?php echo JText::_('CLOSE')?>"></div>

        <div id="user-box-title"></div>

        <div id="user-list"></div>

        <div id="user-box-pagination">
            <span class="user-box-pagination-page user-applauses-page" data-text-id="1148" data-page-number="0">1</span><span class="user-box-pagination-pipe">|</span>
            <span class="user-box-pagination-page" data-page-number="1">2</span><span class="user-box-pagination-pipe">|</span>
            <span class="user-box-pagination-page" data-page-number="2">3</span><span class="user-box-pagination-pipe">|</span>
            <span class="user-box-pagination-page" data-page-number="3">4</span><span class="user-box-pagination-pipe">|</span>
            <span class="user-box-pagination-page" data-page-number="4">5</span><span class="user-box-pagination-pipe">|</span>
            <span class="user-box-pagination-page" data-page-number="5">6</span><span class="user-box-pagination-pipe">|</span>
            <span class="user-box-pagination-page" data-page-number="6">7</span>
        </div>

    </div>

    <?php session_start(); ?>
    <?php if(isset($_SESSION['flash'])): ?>
        <div id="flash-msg" class="<?php echo $_SESSION['flash']['class'] ?>"><?php echo $_SESSION['flash']['msg'] ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

</body>
</html>