<?php
/**
 * This file is part of GuestCal: http://guestcal.sourceforge.net/
 *
 * ©2012 dotplex e.K. / www.dotplex.de / info@dotplex.de
 * Licenced under GPLv3 Open Source Licence: http://www.gnu.org/licenses/gpl-3.0.html
 */

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml">';
echo '<!-- GuestCal ' . VERSION . ' - http://www.guestcal.com/ -->';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<meta http-equiv="Content-Language" content="' . LANG_ABBR . '" />';
echo '<meta name="description" content="GuestCal ' . __('title') . '" />';
echo '<meta name="robots" content="noindex, follow" />';
echo '<link rel="stylesheet" href="' . $templatePath . 'main.css" charset="utf-8" />';
echo '<link rel="stylesheet" href="css.php" charset="utf-8" />';
echo '<title>GuestCal - ' . $pageTitle . '</title>';
echo '</head>';
echo '<body>';
