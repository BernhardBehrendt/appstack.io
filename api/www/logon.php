<?php

/**
 * oauth-php: Example OAuth server
 *
 * Simple logon for consumer registration at this server.
 *
 * @author Arjan Scherpenisse <arjan@scherpenisse.net>
 *
 *
 * The MIT License
 *
 * Copyright (c) 2007-2008 Mediamatic Lab
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

set_include_path(realpath('../../library/'));

require_once '../core/init.php';

// Try to get Access on Zend Settings
require_once ('../../library/Zend/Config.php');
require_once ('../../library/Zend/Config/Ini.php');

$oSettings = new Zend_Config_Ini('../../application/configs/application.ini', 'production');

if ((isset($_POST['username']) && isset($_POST['password'])) || isset($_SESSION['ACCOUNT']['userdata']['UID'])) {
	($dboauth = mysql_connect($oSettings -> resources -> db -> params -> host, $oSettings -> resources -> db -> params -> username, $oSettings -> resources -> db -> params -> password)) || die(mysql_error());
	mysql_select_db($oSettings -> resources -> db -> params -> dbname, $dboauth) || die(mysql_error());

	if (isset($_POST['username']) && isset($_POST['password'])) {
		$sUsername = Preprocessor_String::filterBadChars($_POST['username']);
		$sPassword = md5(md5($oSettings -> user -> security -> salt) . md5($_POST['password']));
		$sAuthQuery = mysql_query("SELECT * FROM accounts
								   where username='" . $sUsername . "' AND password='" . $sPassword . "' AND activated=1", $dboauth);

	} elseif (isset($_SESSION['ACCOUNT']['userdata']['UID'])) {
		$sAuthQuery = mysql_query("SELECT * FROM accounts where accounts.idaccount=" . ((int)$_SESSION['ACCOUNT']['userdata']['UID']) . " AND activated=1", $dboauth);
	}

	$oRow = mysql_fetch_object($sAuthQuery);

	if (is_object($oRow)) {
		if (isset($oRow -> username) && isset($oRow -> idaccount)) {
			$_SESSION['authorized'] = true;
			$_SESSION['account_idaccount'] = $oRow -> idaccount;
			if (!empty($_REQUEST['goto'])) {
				header('Location: ' . $_REQUEST['goto']);
				die ;
			}

			echo "Logon succesfull.";
			die ;
		}
	} else {
		sleep(5);
		echo 'No budget for this account';
		die ;
	}
}
if (isset($_REQUEST['goto'])) {
	$smarty = session_smarty();
	$smarty -> display('logon.tpl');
} else {
	header('Location: ' . $oSettings -> website -> site -> basehref);
	die();
}
?>