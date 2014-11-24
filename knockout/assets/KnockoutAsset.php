<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace efrank\knockout\assets;

/**
 * This asset bundle provides the base javascript files for the Yii Framework.
 *
 * @author Elmar Frank
 * @since 1.0
 */
class KnockoutAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/e-frank/yii2-knockout/knockout/assets/knockout';

	public $css = [
	];

	public $js         = [
		'knockout.js' => 'knockout-3.1.0.js',
		'knockout.base.js',
		'knockout.bindinghandlers.js',
		
		'extenders/knockout.extender.display.js',
		'extenders/knockout.extender.errors.js',
		'extenders/knockout.extender.validators.js',
		'extenders/knockout.extender.datetime.js',
		'extenders/knockout.extender.decimal.js',
		'extenders/knockout.extender.component.js',
		'extenders/knockout.extender.list.js',
		'extenders/knockout.extender.lookup.js',
		'extenders/knockout.extender.subscribe.js',

		'knockout.mapping.js',

		'sortable/jquery-ui.min.js',
		'sortable/jquery-ui.touchpunch.js',
		'sortable/knockout-sortable.min.js',
	];

	public $depends = [
        'efrank\knockout\assets\HandleBarsAsset',
        'efrank\knockout\assets\TypeaheadAsset',
	];

}
