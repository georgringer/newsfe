<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Edit',
	array(
		'News' => 'list, show, new, create, edit, update, delete,preview,editMedia,moveUp,moveDown',
		'Media' => 'list, show, new, create, edit, update, delete,preview,editMedia,moveUp,moveDown',
		'RelatedLink' => 'new, create, edit, update, delete,moveUp,moveDown',
		'RelatedFile' => 'new, create, edit, update, delete,moveUp,moveDown',
	),
	array(
		'News' => 'list, show, new, create, edit, update, delete,preview,editMedia',
		'Media' => 'list, show, new, create, edit, update, delete,preview,editMedia',
		'RelatedLink' => 'new, create, edit, update, delete,editMedia',
		'RelatedFile' => 'new, create, edit, update, delete,editMedia',
	)
);

?>