<?php
// $Id: views-view-fields.tpl.php,v 1.6 2008/09/24 22:48:21 merlinofchaos Exp $
/**
 * @file views-view-fields.tpl.php
 * Default simple view template to all the fields as a row.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $field->content: The output of the field.
 *   - $field->raw: The raw data for the field, if it exists. This is NOT output safe.
 *   - $field->class: The safe class id to use.
 *   - $field->handler: The Views field handler object controlling this field. Do not use
 *     var_export to dump this object, as it can't handle the recursion.
 *   - $field->inline: Whether or not the field should be inline.
 *   - $field->inline_html: either div or span based on the above flag.
 *   - $field->separator: an optional separator that may appear before a field.
 * - $row: The raw result object from the query, with all data it fetched.
 *
 * @ingroup views_templates
 */

//printr($user); exit;

// did not find a way do to this with views so:

$uid = arg(3);

$activities = array();

if($user->uid == $uid)
{
	$sql = "SELECT {activity}.aid AS aid,
	   COALESCE(activity_personal_messages.message, activity_messages.message) AS activity_messages_message,
	   activity.created AS activity_created,
	   activity.op AS activity_op,
	   activity.type AS activity_type,
	   activity.aid AS activity_aid,
	   og_ancestry.group_nid AS gid,
	   node_activity.nid as nid
	 FROM {activity} activity
	 LEFT JOIN {node} node_activity ON activity.nid = node_activity.nid
	 INNER JOIN {og_ancestry} og_ancestry ON node_activity.nid = og_ancestry.nid
	 INNER JOIN {og_uid} og_uid ON og_ancestry.group_nid = og_uid.nid AND og_uid.uid = %d
	 INNER JOIN {users} users_activity ON activity.uid = users_activity.uid
	 INNER JOIN {activity_targets} activity_targets ON activity.aid = activity_targets.aid AND (activity_targets.uid = 0 AND activity_targets.language = 'en')
	 INNER JOIN {activity_messages} activity_messages ON activity_targets.amid = activity_messages.amid
	 LEFT JOIN {activity_targets} activity_personal_targets ON activity.aid = activity_personal_targets.aid AND (activity_personal_targets.uid = %d AND activity_personal_targets.language = 'en')
	 LEFT JOIN {activity_messages} activity_personal_messages ON activity_personal_targets.amid = activity_personal_messages.amid
	  WHERE activity.status <> 0 AND activity.uid <> %d AND activity.created >= %d
	   ORDER BY activity_aid DESC LIMIT 487 # some bullshit number
	";

	$result = db_query($sql, $uid, $uid, $uid, $user->previous_login - (2 * 60 * 60));

	while($row = db_fetch_array($result))
	{
		//printr($row);

		preg_match('#<span class="cmtls-group-name">(.*)</span>#', $row['activity_messages_message'], $matches);
		$group_name = strip_tags($matches[0]);
		unset($matches);

		preg_match('#<span class="cmtls-app-name">(.*)</span>#', $row['activity_messages_message'], $matches);
		$app_name = strip_tags($matches[0]);
		unset($matches);

		preg_match('#<span class="cmtls-app-id">(.*)</span>#', $row['activity_messages_message'], $matches);
		$app_id = strip_tags($matches[0]);
		unset($matches);

		$activities[$app_id]['title'] = $app_name;

		$activities[$app_id]['group']['title'] = $group_name;
		$activities[$app_id]['group']['id'] = $row['gid'];

		$activities[$app_id]['activities'][$row['aid']] = $row;
	}

	//printr($activities);
	//exit;

}

$user_activities = array();

$sql = "SELECT {activity}.aid AS aid,
   COALESCE(activity_personal_messages.message, activity_messages.message) AS activity_messages_message,
   activity.created AS activity_created,
   activity.op AS activity_op,
   activity.type AS activity_type,
   activity.aid AS activity_aid,
   og_ancestry.group_nid AS gid,
   node_activity.nid as nid
 FROM {activity} activity
 LEFT JOIN {node} node_activity ON activity.nid = node_activity.nid
 INNER JOIN {og_ancestry} og_ancestry ON node_activity.nid = og_ancestry.nid
 INNER JOIN {og_uid} og_uid ON og_ancestry.group_nid = og_uid.nid AND og_uid.uid = %d
 INNER JOIN {users} users_activity ON activity.uid = users_activity.uid
 INNER JOIN {activity_targets} activity_targets ON activity.aid = activity_targets.aid AND (activity_targets.uid = 0 AND activity_targets.language = 'en')
 INNER JOIN {activity_messages} activity_messages ON activity_targets.amid = activity_messages.amid
 LEFT JOIN {activity_targets} activity_personal_targets ON activity.aid = activity_personal_targets.aid AND (activity_personal_targets.uid = %d AND activity_personal_targets.language = 'en')
 LEFT JOIN {activity_messages} activity_personal_messages ON activity_personal_targets.amid = activity_personal_messages.amid
  WHERE activity.status <> 0 AND activity.uid = %d
   ORDER BY activity_aid DESC LIMIT 10
";

$result = db_query($sql, $uid, $uid, $uid);

while($row = db_fetch_array($result))
{
	//printr($row);

	preg_match('#<span class="cmtls-group-name">(.*)</span>#', $row['activity_messages_message'], $matches);
	$group_name = strip_tags($matches[0]);
	unset($matches);

	preg_match('#<span class="cmtls-app-name">(.*)</span>#', $row['activity_messages_message'], $matches);
	$app_name = strip_tags($matches[0]);
	unset($matches);

	preg_match('#<span class="cmtls-app-id">(.*)</span>#', $row['activity_messages_message'], $matches);
	$app_id = strip_tags($matches[0]);
	unset($matches);

	$user_activities[$app_id]['title'] = $app_name;

	$user_activities[$app_id]['group']['title'] = $group_name;
	$user_activities[$app_id]['group']['id'] = $row['gid'];

	$user_activities[$app_id]['activities'][$row['aid']] = $row;
}

$tabs = array();

if($user->uid == arg(3) && sizeof($activities))
{
	$tabs[] = array(
		'title' => t('Updates'),
		'rel' => '.cmtls-member-group-updates',
		'anchor' => 'updates'
	);
}

if(sizeof($user_activities))
{
	$tabs[] = array(
		'title' => t('My content'),
		'rel' => '.cmtls-member-activities',
		'anchor' => 'activities'
	);
}

if($user->uid == 1 || $user->uid == $fields['uid']->raw)
{
	$tabs[] = array(
		'title' => t('Notifications'),
		'rel' => '.cmtls-member-notifications', 
		'anchor' => 'notifications'
	);
}

//printr($user_activities);
//exit;
?>

<div class="content-toolbar">
	<div class="toolbar-description">
		<div class="avatar">
			<?php print _cmtls_member_avatar($user->uid == $fields['uid']->raw ? $user : $fields['uid']->raw, 80); ?>
		</div>

		<ul class="cmtls-member-profile-overview">
			<li><h1><?php print _cmtls_member_name($fields); ?></h1></li>
			<?php if(og_is_group_admin($cmtls['current_group'], $user->uid == $fields['uid']->raw ? $user : user_load($fields['uid']->raw))): ?>
				<li><?php print t('Group administrator') ?> <?php if(og_is_group_admin($cmtls['current_group']) && og_is_group_admin($cmtls['current_group'], user_load($fields['uid']->raw))): print ' &middot; '.l(t('Change'), 'cmtls/'.$cmtls['current_group']->nid.'/member/'.$fields['uid']->raw.'/remove_admin', array('attributes' => array('class' => 'modalframe-child'))); endif; ?></li>
			<?php endif; ?>
			<?php if(og_is_group_admin($cmtls['current_group']) && !og_is_group_admin($cmtls['current_group'], user_load($fields['uid']->raw))): ?><li><?php print t('Member').' &middot; '.l(t('Change'), 'cmtls/'.$cmtls['current_group']->nid.'/member/'.$fields['uid']->raw.'/create_admin', array('attributes' => array('class' => 'modalframe-child'))); ?></li><?php endif; ?>

			<?php if($user->uid): ?>
			<li><?php print $fields['mail']->content ;?></li>
			
			<?php if(cmtls_member_is_online($fields['uid']->raw)) print '<li class="cmtls-member-online">'.t('Online').'</li>'; ?>

			<?php if($fields['value_2']->raw): ?><li><?php print t('Address') ?>: <?php print check_plain($fields['value_2']->raw) ;?></li><?php endif; ?>
			<?php if($fields['value_5']->raw): ?><li><?php print t('Phone') ?>: <a href="callto:<?php print check_plain($fields['value_5']->raw) ;?>"><?php print check_plain($fields['value_5']->raw) ;?></a></li><?php endif; ?>
			<?php if($fields['value_1']->raw): ?><li><?php print t('Skype') ?>: <a href="skype:<?php print check_plain($fields['value_1']->raw) ;?>"><?php print check_plain($fields['value_1']->raw) ;?></a></li><?php endif; ?>
			<?php if($fields['value_3']->raw): ?><li><?php print t('Description') ?>: <?php print check_plain($fields['value_3']->raw) ;?></li><?php endif; ?>
			<?php if($fields['value_4']->raw): ?><li><?php print t('Homepage') ?>: <?php print $fields['value_4']->content ;?></li><?php endif; ?>
			<? php /*
			<li><?php if($user->uid == 1 || $user->uid == $fields['uid']->raw): print l(t('Delete'), 'cmtls/'.$cmtls['main_group']->nid.'/member/'.$fields['uid']->raw.'/delete', array('attributes' => array('class' => 'modalframe-child'))); endif; ?></li>
			       */ ?>
			<?php endif; ?>

		</ul>

		<?php if($user->uid == 1 || $user->uid == $fields['uid']->raw): print l(t('Edit profile'), 'cmtls/'.$cmtls['main_group']->nid.'/member/'.$fields['uid']->raw.'/edit', array('attributes' => array('class' => 'edit-profile-section modalframe-child cmtls-button'))); endif; ?>

	</div> <!-- .toolbar-description -->
	<div class="clear"></div>


	<?php if(sizeof($tabs) > 1): ?>

		<ul class="toolbar-tabs">
		<?php foreach($tabs as &$tab): ?>
			<li><a href="#<?php print $tab['anchor'] ?>" rel="<?php print $tab['rel'] ?>"><?php print $tab['title'] ?></a></li>
		<?php endforeach; ?>
		</ul>
			
	<?php endif; ?>

</div> <!-- .content-toolbar -->


<?php /************ NEW POSTS IN USER GROUPS **************/ ?>
<?php if($user->uid == arg(3) && sizeof($activities)): ?>
<div class="cmtls-member-group-updates cmtls-dashboard-container<?php if(sizeof($tabs) > 1) print ' hidden' ?>">
	<h2><?php print t('Recent activity in my groups') ?></h2>

	<?php foreach($activities as $app_id => $app): ?>
		<div class="content-group-header">
			<span><?php print l($app['group']['title'], 'cmtls/'.$app['group']['id'], array('attributes' => array('class' => 'group-name'))); ?> > <?php print l($app['title'], 'cmtls/'.$app['group']['id'].'/'.$app_id, array('attributes' => array('class' => 'tool-name'))); ?></span>
		</div>

		<div class="text-node">
			<?php foreach($app['activities'] as $activity): ?>
				<div class="dashboard-item"><?php print $activity['activity_messages_message']; ?></div>
			<?php endforeach; ?>
		</div>

	<?php endforeach; ?>

</div> <!-- .cmtls-member-group-updates -->
<?php endif; ?>

<?php /************ USER ACTIVITIES **************/ ?>
<?php if(sizeof($user_activities)): ?>
<div class="cmtls-member-activities cmtls-dashboard-container<?php if(sizeof($tabs) > 1) print ' hidden' ?>">

	<h2><?php print t('My recent activities') ?></h2>

	<?php foreach($user_activities as $app_id => $app): ?>
		<div class="content-group-header">
			<span><?php print l($app['group']['title'], 'cmtls/'.$app['group']['id'], array('attributes' => array('class' => 'group-name'))); ?> > <?php print l($app['title'], 'cmtls/'.$app['group']['id'].'/'.$app_id, array('attributes' => array('class' => 'tool-name'))); ?></span>
		</div>

		<div class="text-node">
			<?php foreach($app['activities'] as $activity): ?>
				<div class="dashboard-item"><?php print $activity['activity_messages_message']; ?></div>
			<?php endforeach; ?>
		</div>

	<?php endforeach; ?>

</div> <!-- cmtls-member-activities -->
<?php endif; ?>

<?php /************ USER NOTIFICATIONS **************/ ?>
<?php if($user->uid == 1 || $user->uid == $fields['uid']->raw): ?>
	<div class="cmtls-member-notifications<?php if(sizeof($tabs) > 1) print ' hidden' ?>">
		<h2><?php print t('Notifications'); ?></h2>
		<p><?php print t('Send me e-mail notifications on').':'; ?></p>
		<?php print drupal_get_form('cmtls_member_notification_settings_form', $fields['uid']->raw); ?>
	</div> <!-- .cmtls-member-notifications -->
<?php endif; ?>