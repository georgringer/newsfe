<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
		$_EXTKEY, 'Edit', 'newsfe'
);



t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'News FE Edit');


$tempColumns = array(
    'tx_newsfe_feuser' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:newsfe/Resources/Private/Language/locallang_db.xml:tx_news_domain_model_news.tx_newsfe_feuser',
        'config' => array(
            'type' => 'select',
            'internal_type' => 'db',
			'foreign_table' => 'fe_users',
            'allowed' => 'fe_users',
            'size' => 1,
            'minitems' => 0,
            'maxitems' => 1,
        )
    ),
);

t3lib_div::loadTCA('tx_news_domain_model_news');
t3lib_extMgm::addTCAcolumns('tx_news_domain_model_news',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tx_news_domain_model_news','tx_newsfe_feuser;;;;1-1-1');

?>