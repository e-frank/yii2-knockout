<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace x1\knockout;

use Yii;
use yii\helpers\Json;

/**
 * This asset bundle provides the base javascript files for the Yii Framework.
 *
 * @author Elmar Frank
 * @since 1.0
 */
// class KnockoutAsset extends \x1\assets\AssetBundle
class KnockoutAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@x1/knockout/assets';
	public $basePath   = '@webroot/app/knockout';

	public $css = [
	];

	// TODO: knockout.base

	public $js         = [
		// 'requirejs/require.js',

		'knockout.js' => 'knockout-3.3.0.js',
		'knockout.config.js',

		'binding-handlers/knockout.bindinghandlers.checkbox.js'    => 'binding-handlers/knockout.bindinghandlers.checkbox.min.js',
		'binding-handlers/knockout.bindinghandlers.fadein.js'      => 'binding-handlers/knockout.bindinghandlers.fadein.min.js',
		'binding-handlers/knockout.bindinghandlers.hiddenvalue.js' => 'binding-handlers/knockout.bindinghandlers.hiddenvalue.min.js',
		'binding-handlers/knockout.bindinghandlers.typeahead.js'   => 'binding-handlers/knockout.bindinghandlers.typeahead.min.js',
		'binding-handlers/ko.binding.select2.js',

		'extenders/knockout.extender.display.js'                   => 'extenders/knockout.extender.display.min.js',
		'extenders/knockout.extender.validators.js'                => 'extenders/knockout.extender.validators.min.js',
		'extenders/knockout.extender.datetime.js'                  => 'extenders/knockout.extender.datetime.min.js',
		'extenders/knockout.extender.decimal.js'                   => 'extenders/knockout.extender.decimal.min.js',

		'extenders/knockout.extender.subscribe.js'                 => 'extenders/knockout.extender.subscribe.min.js',
		'extenders/knockout.extender.array-error.js'               => 'extenders/knockout.extender.array-error.min.js',
		'extenders/knockout.extender.select.js'                    => 'extenders/knockout.extender.select.min.js',
		'knockout.mapping.js'                                      => 'knockout.mapping.js',

		'sortable/jquery-ui.min.js'                                => 'sortable/jquery-ui.min.js',
		'sortable/jquery-ui.touchpunch.js'                         => 'sortable/jquery-ui.touchpunch.js',
		'sortable/knockout-sortable.min.js'                        => 'sortable/knockout-sortable.min.js',
	];

	public $depends = [
		'x1\base\ConfigAsset',
		// 'x1\knockout\MomentAsset',
		// 'x1\knockout\ValidationAsset',
		// 'x1\requirejs\RequireAsset',


		'x1\moment\MomentAsset',
		'yii\validators\ValidationAsset',
		// // 'x1\knockout\RequireAsset',
  //       // 'efrank\knockout\assets\HandleBarsAsset',
  //       // 'efrank\knockout\assets\TypeaheadAsset',
	];



	public function registerAssetFiles222($view) {
		parent::registerAssetFiles($view);
		$config = <<<EOD
require.config({
	baseUrl: '%1\$s',
	paths:     {
		'knockout-asset':   '%2\$s',
		'knockout-main':    '%2\$s/knockout-3.3.0',
		'knockout-mapping': '%2\$s/knockout-mapping',
		'jquery':           '%3\$s/jquery.min.js',
	}
});


define('ko', ['knockout-main', 'knockout-mapping'], function(ko, mapping) {
	console.log('!!!!!!!!!!!!!!!!i am generated', ko, mapping)
	ko.mapping = mapping;
	return ko;
});

EOD;
		$basePath   = Yii::$app->assetManager->basePath;
		$baseUrl    = Yii::$app->assetManager->baseUrl;
		$len        = strlen($basePath) + 1;
		$assetPath  = substr($this->basePath, $len);
		$jqueryPath = substr(Yii::$app->assetManager->getPublishedUrl((new \yii\web\JqueryAsset)->sourcePath), strlen($baseUrl) + 1);
		$view->registerJs(sprintf($config, $baseUrl, $assetPath, $jqueryPath), $view::POS_BEGIN);

	}


}
