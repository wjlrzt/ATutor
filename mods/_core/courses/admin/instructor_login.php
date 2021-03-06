<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_COURSES);

if (isset($_POST['submit_yes'])) {
	$_POST['course'] = intval($_POST['course']);

	$admin_login = $_SESSION['login'];

	$sql = "SELECT M.member_id, M.login, M.preferences, M.language FROM %smembers M, %scourses C WHERE C.course_id=%d and C.member_id=M.member_id";
	$row_member = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_POST['course']), TRUE);
	
	if(count($row_member) > 0){
		$_SESSION['course_id']  = 0;
		$_SESSION['login']		= $row_member['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row_member['member_id']);
		unset($_SESSION['prefs']);
		if ($row['preferences'] == "")
			assign_session_prefs(unserialize(stripslashes($_config["pref_defaults"])), 1);
		else
			assign_session_prefs(unserialize(stripslashes($row_member['preferences'])), 1);
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row_member['language'];
		$_SESSION['is_super_admin'] = $admin_login;
		unset($_SESSION['message']['help']);
		session_write_close();

		header('Location: '.AT_BASE_HREF.'bounce.php?course='.$_POST['course']);
		exit;
	}
} else if (isset($_POST['submit_no'])) {

	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_core/courses/admin/courses.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

	$sql = "SELECT * FROM %scourses WHERE course_id=%d";
	$row = queryDB($sql,array(TABLE_PREFIX, $_REQUEST['course']), TRUE);

	$hidden_vars['course'] = $_GET['course'];

	$msg->addConfirm(array('LOGIN_INSTRUCTOR', SITE_NAME, $row['title']), $hidden_vars);
	$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>