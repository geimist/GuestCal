<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

$str['months'] 							= array ('', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember');
$str['weekdaysAbbr']					= array ('Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So');

$str['yes']								= 'Ja';
$str['no']								= 'Nein';
$str['none']							= 'Keine';
$str['allObjects']						= 'Alle Objekte';
$str['selectObject']					= 'Objekt anzeigen';
$str['title']							= 'Belegungskalender';

$str['error'] 							= 'Fehler!';
$str['errorDBAuth'] 					= 'Es konnte keine Verbindung zum Datenbankserver hergestellt werden. Prüfen Sie die Werte in <i>includes/config.inc.php.</i>';
$str['errorDBSetupNoDB'] 				= 'Die angegebene Datenbank wurde nicht gefunden, möglicherweise ist GuestCal nicht installiert. <a href="' . BASEDIR . 'admin/setup/">Installation starten</a>';
$str['errorDBSetupNoTables'] 			= 'Es wurden keine gültigen Tabellen in der Datenbank gefunden, möglicherweise ist GuestCal nicht installiert. <a href="' . BASEDIR . 'admin/setup/">Installation starten</a>';
$str['errorDBSetupVersionsDiffer'] 		= 'Die Version der GuestCal-Tabellen in der Datenbank stimmt nicht mit der Programmversion überein. <a href="' . BASEDIR . 'admin/setup/">Aktualisierung starten</a>';

$str['setup'] 							= 'Installation';
$str['setupWelcome'] 					= 'Willkommen!';
$str['setupIntro'] 						= 'Hier können Sie GuestCal in Ihrer Datenbank installieren bzw. eine ältere Version aktualisieren.<br />Sobald die Installation abgeschlossen ist, sollten Sie das Verzeichnis <i>admin/setup/</i> aus Sicherheitsgründen löschen.';
$str['setupDBConfiguration'] 			= 'Es ist folgende Datenbank-Konfiguration in <i>includes/config.inc.php</i> eingetragen. Eine vorhandene Installation wird zur Aktualisierung nur erkannt, wenn sie dasselbe Tabellenpräfix trägt.';
$str['setupServer'] 					= 'Server';
$str['setupUser'] 						= 'Benutzername';
$str['setupPass'] 						= 'Paßwort';
$str['setupDB'] 						= 'Datenbank';
$str['setupPfix'] 						= 'Tabellenpräfix';
$str['setupIndications'] 				= 'Was passiert während der Installation?';
$str['setupIndicationsBackupWarning']	= 'Wir übernehmen keine Haftung für die Fehlerfreiheit dieses Installationsprogramms. Vor einem Upgrade sollten Sie in jedem Fall ein Backup der Datenbank machen.';
$str['setupIndicationSuccess']			= 'GuestCal wurde erfolgreich installiert. Sie sollten das Verzeichnis <i>admin/setup/</i> aus Sicherheitsgründen löschen.';
$str['setupIndicationNewDB'] 			= 'Die angegebene Datenbank existiert nicht. Wenn der Benutzer dazu ausreichende Rechte hat, wird sie bei der Installation angelegt.';
$str['setupIndicationNewTables'] 		= 'Es wurde keine vorhandene Installation gefunden. Die für GuestCal benötigten Tabellen werden mit dem angegebenen Präfix in der Datenbank angelegt.';
$str['setupIndicationUpgrade'] 			= 'Die Version der GuestCal-Tabellen in der Datenbank stimmt nicht mit der Programmversion überein. Wenn die Programmversion neuer als Ihre alte Version ist, wird die Datenbank automatisch aktualisiert.';
$str['setupIndicationUpgradeFrom1'] 	= 'Es wurde eine vorhandene GuestCal-1.x-Version gefunden. Deren Daten werden automatisch übernommen, die alte Datenbankstruktur dabei jedoch nicht gelöscht. Sie können später im Admin-Bereich Daten aus weiteren 1.x-Installationen importieren.';
$str['setupCreateDBFailed'] 			= 'Die Datenbank konnte nicht angelegt werden:';
$str['setupStart'] 						= 'Installation / Aktualisierung starten';
$str['setupContinueToAdmin'] 			= 'Weiter zum Admin-Bereich';

$str['admin']							= 'Admin-Bereich';
$str['adminNo']							= 'Nr.';
$str['adminName']						= 'Name';
$str['adminDesc']						= 'Beschreibung';
$str['adminEdit']						= 'Bearbeiten';
$str['adminDelete']						= 'Löschen';
$str['adminSubmit']						= 'Speichern';
$str['adminSubmitAndEdit']				= 'Speichern und weiter bearbeiten';
$str['adminContinue']					= 'Fortfahren';
$str['adminCancel']						= 'Abbrechen';
$str['adminObjects']					= 'Objekte';
$str['adminObject']						= 'Objekt';
$str['adminNewObject']					= 'Neues Objekt hinzufügen';
$str['adminEditObject']					= 'Objekt bearbeiten';
$str['adminDeleteObject']				= 'Objekt löschen';
$str['adminDeleteObjectReally']			= 'Wenn Sie fortfahren, werden das Objekt und alle mit ihm verbundenen Kalendereinträge endgültig gelöscht.';
$str['adminPrefs']						= 'Einstellungen';
$str['adminClasses']					= 'Belegungsarten';
$str['adminClass']						= 'Belegungsart';
$str['adminNewClass']					= 'Neue Belegungsart hinzufügen';
$str['adminEditClass']					= 'Belegungsart bearbeiten';
$str['adminDeleteClass']				= 'Belegungsart löschen';
$str['adminDeleteClassReally']			= 'Wenn Sie fortfahren, werden die Belegungsart und alle mit ihr verbundenen Kalendereinträge endgültig gelöscht.';
$str['adminCellContent']				= 'Inhalt';
$str['adminColor']						= 'Farbe';
$str['adminEntries']					= 'Belegungen';
$str['adminEntryDates']					= 'Termine';
$str['adminEntryStatic']				= 'einmalig';
$str['adminEntryReturning']				= 'regelmäßig';
$str['adminEntryFrom']					= 'Von';
$str['adminEntryTo']					= 'Bis';
$str['adminEntryWeekly']				= 'Wöchentlich';
$str['adminNewEntry']					= 'Neue Belegung hinzufügen';
$str['adminEditEntry']					= 'Belegung bearbeiten';
$str['adminDeleteEntry']				= 'Belegung löschen';
$str['adminEntryHelpEdit']				= 'Klicken Sie auf eine Belegung, um diese zu editieren';
$str['adminEntryHelpNew']				= 'Klicken Sie auf Abbrechen, um eine neue Belegung einzutragen';
$str['adminUsers']						= 'Benutzer';
$str['adminImport']						= 'Import';
$str['adminImportv1']					= 'Import von GuestCal v.1';
$str['adminImportv1NoticeSuccess']		= 'Es wurden % Tabellen importiert.';
$str['adminImportv1NoticeFail']			= 'Es wurden keine Tabellen importiert. Möglicherweise stimmt das Tabellenpräfix nicht.';
$str['adminImportNoticeMissingObject']	= 'Bitte geben Sie ein gültiges Objekt an.';
$str['adminImportv1Desc']				= 'In GuestCal 1.x konnten mehrere Objekte nur über mehrere GuestCal-Installationen verwaltet werden. Hier können Sie Daten aus weiteren 1.x-Installationen in ein weiteres Objekt importieren. Die zu importierenden Tabellen müssen sich in derselben Datenbank wie GuestCal 2 befinden. Wenn dies nicht der Fall ist, kopieren Sie die Tabellen z.B. in phpMyAdmin in die aktuelle Datenbank.';
$str['adminImportv1Prefix']				= 'Tabellenpräfix der GuestCal-1.x-Tabellen';
$str['adminImportToObject']				= 'Importieren in Objekt';

$str['prefsLanguageDefault']			= 'Standardsprache';
$str['prefsLanguagesActive']			= 'Aktive Sprachen';
$str['prefsObjectDefault']				= 'Standardobjekt';
$str['prefsObjectShowSelect']			= 'Besucher darf zwischen Objekten wechseln';
$str['prefsClassDefault']				= 'Standardbelegungsart';
$str['prefsClassFirstAndLastDay']		= 'Besondere Art für ersten und letzten Tag einmaliger Belegungen';
$str['prefsHideExpiredDates']			= 'Abgelaufene Termine im Frontend ausblenden';
$str['prefsShowTitleInFrontend']		= 'Beschreibung der Termine im Frontend anzeigen';
