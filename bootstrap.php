<?php
// add core classes
/*$common_cls_path_classes = array(
	'default' => array(
		"clsDB.php",
		"clsCore.php",
		"clsParser.php",
		"clsError.php",
	),
	'general' => array(
		"clsPage.php",
		"clsBlocks.php",
	),
	'admin' => array(
		
	),

);

// prepare core classes for init modules
$common_cls_path_classes_tmp = array();
foreach($modules_names As $module){
	if (isset($common_cls_path_classes[$module])){
		$common_cls_path_classes_tmp = array_merge($common_cls_path_classes_tmp, $common_cls_path_classes[$module]);
	}
}
$common_cls_path_classes_tmp = array_unique($common_cls_path_classes_tmp);

// init them
foreach ($common_cls_path_classes_tmp as $value){
  clsCommon::autoLoaderClass(COMMON_CLS_PATH, $value);
}

#require twig adapter.
#clsCommon::autoLoaderClass(COMMON_SYS_CLS_PATH . 'view' . DIRECTORY_SEPARATOR . 'adapter' . DIRECTORY_SEPARATOR, 'clsAbstractAdapter.php');
#clsCommon::autoLoaderClass(COMMON_SYS_CLS_PATH . 'view' . DIRECTORY_SEPARATOR . 'adapter' . DIRECTORY_SEPARATOR, 'clsTwigAdapter.php');

// add entity classes
$entity_cls_path_classes = array(
  "clsCommonService.php",
  "clsAdmin.php",
  "clsUsersAddresses.php",
  "clsSearch.php",
  "clsEvent.php",
  "clsMeta.php",
  "clsUser.php",
  "clsCompany.php",
  "clsUsersCompanies.php",
  "clsUsersSocialNetworks.php",
  "clsEmail.php",
  "clsSession.php",
  "clsClient.php",
  "clsAuthorisation.php",
  "clsStatic.php",
  "clsValidation.php",
  "clsCategory.php",
  "clsGoods.php",
  "clsCart.php",
  "clsNews.php",
  "clsProducts.php",
  "clsOrder.php",
  "clsOrderProducts.php",
  "clsDynamicBlocks.php",
  "clsPolls.php",
  "clsImages.php",
  "clsI.php",
  "clsServicesImages.php"
);

// init them
foreach ($entity_cls_path_classes as $value){
  clsCommon::autoLoaderClass(CLS_PATH, $value);
}

require_once (DICTIONARY_PATH . "errtext.php");*/