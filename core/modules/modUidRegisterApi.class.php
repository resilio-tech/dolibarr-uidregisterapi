<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2022 Amael Parreaux-Ey <amael.parreaux-ey@resilio-solutions.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   uidregisterapi     Module UidRegisterApi
 *  \brief      UidRegisterApi module descriptor.
 *
 *  \file       htdocs/uidregisterapi/core/modules/modUidRegisterApi.class.php
 *  \ingroup    uidregisterapi
 *  \brief      Description and activation file for module UidRegisterApi
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module UidRegisterApi
 */
class modUidRegisterApi extends DolibarrModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 302650; // Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'uidregisterapi';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "interface";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Module label (no space allowed), used if translation string 'ModuleUidRegisterApiName' not found (UidRegisterApi is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModuleUidRegisterApiDesc' not found (UidRegisterApi is name of module).
		$this->description = "This module allows you to connect to the Swiss UID Register when editing third parties from frontend.";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "This module allows you to connect to the Swiss UID Register when editing third parties from frontend. This module is NOT provided by the Swiss Administration.";

		// Author
		$this->editor_name = 'Amael Parreaux-Ey - Resilio SA';
		$this->editor_url = 'https://www.resilio-solutions.com';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.3.0';

		// Key used in llx_const table to save module status enabled/disabled (where UIDREGISTERAPI is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'generic';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				'/uidregisterapi/css/uidregisterapi.css.php',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				'/uidregisterapi/js/uidregisterapi.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/uidregisterapi/temp","/uidregisterapi/subdir");
		$this->dirs = array("/uidregisterapi/temp");

		// Config pages. Put here list of php page, stored into uidregisterapi/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@uidregisterapi");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
		$this->depends = array();
		$this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

		// The language file dedicated to your module
		$this->langfiles = array("uidregisterapi@uidregisterapi");

		// Prerequisites
		$this->phpmin = array(5, 6); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(11, -3); // Minimum version of Dolibarr required by module

		// Messages at activation
		$this->warnings_activation = array();
		$this->warnings_activation_ext = array();

		// Constants
		$this->const = array();

		if (!isset($conf->uidregisterapi) || !isset($conf->uidregisterapi->enabled)) {
			$conf->uidregisterapi = new stdClass();
			$conf->uidregisterapi->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array();

		// Dictionaries
		$this->dictionaries = array();

		// Boxes/Widgets
		$this->boxes = array();

		// Cronjobs
		$this->cronjobs = array();

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'ReadUidRegisterApi'; // Permission label (translation key)
		$this->rights[$r][4] = 'read';
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'WriteUidRegisterApi'; // Permission label (translation key)
		$this->rights[$r][4] = 'write';
		$r++;

		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		$this->menu[$r++] = array(
			'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'top', // This is a Top menu entry
			'titre'=>'ModuleUidRegisterApiName',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'uidregisterapi',
			'leftmenu'=>'',
			'url'=>'/uidregisterapi/uidregisterapiindex.php',
			'langs'=>'uidregisterapi@uidregisterapi', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000 + $r,
			'enabled'=>'$conf->uidregisterapi->enabled', // Define condition to show or hide menu entry. Use '$conf->uidregisterapi->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->hasRight("uidregisterapi", "read")', // Permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$result = $this->_load_tables('/uidregisterapi/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries
		}

		// Permissions
		$this->remove($options);

		$sql = array();

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from Dolibarr database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
