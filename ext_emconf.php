<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "ab_downloads".
 *
 * Auto generated 16-06-2014 16:18
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Modern Downloads',
	'description' => 'This is a modern download plugin based on the Modern Linklist (ab_linklist) extension.
				
				Please have a look at the documentation for all (additional) features available.

				If you like this extension please consider to rate it at:
				http://typo3.org/extensions/repository/view/ab_downloads/$CURRENT_VERSION$/rating/

				Project homepage:
				http://typo3.andreas-bulling.de/en/extensions/modern-downloads/

				Subversion repository:
				http://repos.andreas-bulling.de/ab_downloads/

				Bugtracker:
				http://typo3.andreas-bulling.de/en/bug-tracker/

				Donations:
				http://typo3.andreas-bulling.de/en/donate-money/

				A demo with the most recent features enabled can be found here:
				http://typo3.andreas-bulling.de/en/demos/modern-downloads/',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '7.6.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_abdownloads/categoryImages,uploads/tx_abdownloads/downloadImages,uploads/tx_abdownloads/files',
	'modify_tables' => 'be_groups,be_users',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Andreas Bulling',
	'author_email' => 'typo3@andreas-bulling.de',
	'author_company' => '',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' =>
	array (
		'depends' =>
		array (
			'cms' => '',
			'static_info_tables' => '2.0.0',
			'php' => '4.1.0-0.0.0',
			'typo3' => '3.6.0-0.0.0',
		),
		'conflicts' =>
		array (
		),
		'suggests' =>
		array (
		),
	),
);

