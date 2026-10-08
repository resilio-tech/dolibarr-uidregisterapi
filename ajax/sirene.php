<?php
/* Copyright (C) 2026 Resilio
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    uidregisterapi/ajax/sirene.php
 * \ingroup uidregisterapi
 * \brief   Query the INSEE SIRENE API for the third party form.
 */

if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', '1');
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1');
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';

if (!isModEnabled('uidregisterapi') || !$user->hasRight('uidregisterapi', 'read') || $user->socid > 0) {
	accessforbidden();
}

$mode = GETPOST('mode', 'aZ09');

if ($mode == 'siret') {
	$siren = GETPOST('siren', 'alphanohtml');
	if (!preg_match('/^[0-9]+$/', $siren)) {
		http_response_code(400);
		exit;
	}
	$url = 'https://api.insee.fr/entreprises/sirene/V3/siret?q=siren:'.$siren;
} else {
	$query = GETPOST('q', 'alphanohtml');
	$url = 'https://api.insee.fr/entreprises/sirene/V3/siren?q=periode(denominationUniteLegale%3A%22'.rawurlencode($query).'%22)&date='.date('Y-m-d').'&nombre=10';
}

$result = getURLContent($url, 'GET', '', 1, array('Accept: application/json', 'Authorization: Bearer '.getDolGlobalString('UIDREGISTERAPI_SIRENE_API_KEY')));

if ($result['http_code'] != 200) {
	http_response_code(502);
	exit;
}

top_httphead('application/json');
print $result['content'];
