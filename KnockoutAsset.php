<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace x1\knockout;

/**
 * This asset bundle provides the base javascript files for the Yii Framework.
 *
 * @author Elmar Frank
 * @since 1.0
 */
class KnockoutAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@x1/knockout/assets';

	public $css = [
	];

	public $js         = [
		'knockout.js' => 'knockout-3.2.0.js',
		'knockout.base.js',

		'binding-handlers/knockout.bindinghandlers.checkbox.js',
		'binding-handlers/knockout.bindinghandlers.fadein.js',
		'binding-handlers/knockout.bindinghandlers.hiddenvalue.js',
		'binding-handlers/knockout.bindinghandlers.js',
		'binding-handlers/knockout.bindinghandlers.typeahead.js',
		
		'extenders/knockout.extender.display.js',
		'extenders/knockout.extender.errors.js',
		'extenders/knockout.extender.validators.js',
		'extenders/knockout.extender.datetime.js',
		'extenders/knockout.extender.decimal.js',
		'extenders/knockout.extender.component.js',
		'extenders/knockout.extender.list.js',
		'extenders/knockout.extender.lookup.js',
		'extenders/knockout.extender.subscribe.js',
		'extenders/knockout.extender.array-error.js',

		'knockout.mapping.js',
		// 'mappings/knockout.mapping.base.js',

		'sortable/jquery-ui.min.js',
		'sortable/jquery-ui.touchpunch.js',
		'sortable/knockout-sortable.min.js',
	];

	public $depends = [
		'x1\knockout\MomentAsset',
        // 'efrank\knockout\assets\HandleBarsAsset',
        // 'efrank\knockout\assets\TypeaheadAsset',
	];

}
