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

	// TODO: knockout.base

	public $js         = [
		'knockout.js' => 'knockout-3.3.0.js',
		// 'knockout.base.js',
		'knockout.config.js',

		'binding-handlers/knockout.bindinghandlers.checkbox.js'    => 'binding-handlers/knockout.bindinghandlers.checkbox.min.js',
		'binding-handlers/knockout.bindinghandlers.fadein.js'      => 'binding-handlers/knockout.bindinghandlers.fadein.min.js',
		'binding-handlers/knockout.bindinghandlers.hiddenvalue.js' => 'binding-handlers/knockout.bindinghandlers.hiddenvalue.min.js',
		// 'binding-handlers/knockout.bindinghandlers.js'          => // 'binding-handlers/knockout.bindinghandlers.min.js',
		'binding-handlers/knockout.bindinghandlers.typeahead.js'   => 'binding-handlers/knockout.bindinghandlers.typeahead.min.js',

		'extenders/knockout.extender.display.js'                   => 'extenders/knockout.extender.display.min.js',
		// 'extenders/knockout.extender.errors.js'                 => // 'extenders/knockout.extender.errors.min.js',
		'extenders/knockout.extender.validators.js'                => 'extenders/knockout.extender.validators.min.js',
		'extenders/knockout.extender.datetime.js'                  => 'extenders/knockout.extender.datetime.min.js',
		'extenders/knockout.extender.decimal.js'                   => 'extenders/knockout.extender.decimal.min.js',
		// 'extenders/knockout.extender.component.js'              => // 'extenders/knockout.extender.component.min.js',
		// 'extenders/knockout.extender.list.js'                   => // 'extenders/knockout.extender.list.min.js',
		// 'extenders/knockout.extender.lookup.js'                 => // 'extenders/knockout.extender.lookup.min.js',
		'extenders/knockout.extender.subscribe.js'                 => 'extenders/knockout.extender.subscribe.min.js',
		'extenders/knockout.extender.array-error.js'               => 'extenders/knockout.extender.array-error.min.js',
		'extenders/knockout.extender.select.js'                    => 'extenders/knockout.extender.select.min.js',
		'knockout.mapping.js'                                      => 'knockout.mapping.js',
		'knockout.mapping.prototype.js',

		'sortable/jquery-ui.min.js'                                => 'sortable/jquery-ui.min.js',
		'sortable/jquery-ui.touchpunch.js'                         => 'sortable/jquery-ui.touchpunch.js',
		'sortable/knockout-sortable.min.js'                        => 'sortable/knockout-sortable.min.js',
	];

	public $depends = [
		'x1\knockout\MomentAsset',
		'yii\validators\ValidationAsset',
        // 'efrank\knockout\assets\HandleBarsAsset',
        // 'efrank\knockout\assets\TypeaheadAsset',
	];

}
