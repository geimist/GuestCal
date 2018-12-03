<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

$str['months'] 							= array ('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$str['weekdaysAbbr']					= array ('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');

$str['yes']								= 'Yes';
$str['no']								= 'No';
$str['none']							= 'None';
$str['allObjects']						= 'All objects';
$str['selectObject']					= 'Show object';
$str['title']							= 'Occupancy Calendar';

$str['error'] 							= 'Error!';
$str['errorDBAuth'] 					= 'Connection to database server couldn\'t be established. Please check values in <i>includes/config.inc.php.</i>';
$str['errorDBSetupNoDB'] 				= 'The configured database wasn\'t found. Maybe GuestCal is not installed yet. <a href="' . BASEDIR . 'admin/setup/">Start setup</a>';
$str['errorDBSetupNoTables'] 			= 'No valid tables were found in database. Maybe GuestCal is not installed yet. <a href="' . BASEDIR . 'admin/setup/">Start setup</a>';
$str['errorDBSetupVersionsDiffer'] 		= 'The version of GuestCal tables in database differs from application version. <a href="' . BASEDIR . 'admin/setup/">Start update</a>';

$str['setup'] 							= 'Setup';
$str['setupWelcome'] 					= 'Welcome!';
$str['setupIntro'] 						= 'At this site you can install GuestCal into your database or update an existing version.<br />As soon as setup has finished you should delete the directory <i>admin/setup/</i> for security purposes.';
$str['setupDBConfiguration'] 			= 'The following database configuration is registered in <i>includes/config.inc.php</i>. Any existing installation will only be recognized for updating if it has the same table-prefix.';
$str['setupServer'] 					= 'Server';
$str['setupUser'] 						= 'Username';
$str['setupPass'] 						= 'Password';
$str['setupDB'] 						= 'Database';
$str['setupPfix'] 						= 'Table-prefix';
$str['setupIndications'] 				= 'What happens during installation?';
$str['setupIndicationsBackupWarning']	= 'We don\'t assume liability for this installation script. You should always backup your database before updating.';
$str['setupIndicationSuccess']			= 'GuestCal was installed successfully. You should now delete the directory <i>admin/setup/</i> for security purposes.';
$str['setupIndicationNewDB'] 			= 'The configured database does not exist. If the database user has appropriate rights, it will be created during installation.';
$str['setupIndicationNewTables'] 		= 'No existing installation was found. The required tables will be created with configured prefix.';
$str['setupIndicationUpgrade'] 			= 'The version of GuestCal tables in database differs from application version. If the version of the application is newer, the database will be upgraded automatically.';
$str['setupIndicationUpgradeFrom1'] 	= 'An installation of the old version 1.x was found. Data from this installation will be copied into the new database structure. Old tables won\'t be deleted, you\'ll have to do this manually. You can later import data from additional 1.x installations in the administration panel.';
$str['setupCreateDBFailed'] 			= 'Database could not be created:';
$str['setupStart'] 						= 'Start setup / update';
$str['setupContinueToAdmin'] 			= 'Continue to Administration panel';

$str['admin']							= 'Administration panel';
$str['adminNo']							= 'No.';
$str['adminName']						= 'Name';
$str['adminDesc']						= 'Description';
$str['adminEdit']						= 'Edit';
$str['adminDelete']						= 'Delete';
$str['adminSubmit']						= 'Save';
$str['adminSubmitAndEdit']				= 'Save and continue editing';
$str['adminContinue']					= 'Continue';
$str['adminCancel']						= 'Cancel';
$str['adminObjects']					= 'Objects';
$str['adminObject']						= 'Object';
$str['adminNewObject']					= 'Create new object';
$str['adminEditObject']					= 'Edit object';
$str['adminDeleteObject']				= 'Delete object';
$str['adminDeleteObjectReally']			= 'If you continue, this object and all associated calendar entries will be deleted irrevocably.';
$str['adminPrefs']						= 'Preferences';
$str['adminClasses']					= 'Entry classes';
$str['adminClass']						= 'Entry class';
$str['adminNewClass']					= 'Create new entry class';
$str['adminEditClass']					= 'Edit entry class';
$str['adminDeleteClass']				= 'Delete entry class';
$str['adminDeleteClassReally']			= 'If you continue, this entry class and all associated calendar entries will be deleted irrevocably.';
$str['adminCellContent']				= 'Content';
$str['adminColor']						= 'Color';
$str['adminEntries']					= 'Entries';
$str['adminEntryDates']					= 'Dates';
$str['adminEntryStatic']				= 'non-recurrent';
$str['adminEntryReturning']				= 'recurrent';
$str['adminEntryFrom']					= 'From';
$str['adminEntryTo']					= 'To';
$str['adminEntryWeekly']				= 'Weekly';
$str['adminNewEntry']					= 'Create new entry';
$str['adminEditEntry']					= 'Edit entry';
$str['adminDeleteEntry']				= 'Delete entry';
$str['adminEntryHelpEdit']				= 'Click on an entry to edit or delete';
$str['adminEntryHelpNew']				= 'Click on Cancel to create a new entry';
$str['adminUsers']						= 'User';
$str['adminImport']						= 'Import';
$str['adminImportv1']					= 'Import from GuestCal v.1';
$str['adminImportv1NoticeSuccess']		= '% tables were imported.';
$str['adminImportv1NoticeFail']			= 'No tables were imported. Maybe you entered a wrong table prefix.';
$str['adminImportNoticeMissingObject']	= 'Please select a valid object.';
$str['adminImportv1Desc']				= 'With GuestCal 1.x several objects could only be managed with several installations of GuestCal. With this tool you an import the data of additional installations of 1.x. The tables to be imported have to be in the same database.';
$str['adminImportv1Prefix']				= 'Table prefix of GuestCal 1.x tables';
$str['adminImportToObject']				= 'Import to object';

$str['prefsLanguageDefault']			= 'Default language';
$str['prefsLanguagesActive']			= 'Active languages';
$str['prefsObjectDefault']				= 'Default object';
$str['prefsObjectShowSelect']			= 'Users may switch between objects';
$str['prefsClassDefault']				= 'Default entry class';
$str['prefsClassFirstAndLastDay']		= 'Special class for first and last days of non-recurrent entries';
$str['prefsHideExpiredDates']			= 'Hide expired dates in frontend';
$str['prefsShowTitleInFrontend']		= 'Show entries description in frontend';
