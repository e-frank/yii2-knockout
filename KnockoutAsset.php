<?php
namespace x1\knockout;


/**
 * This asset bundle collects all javascripts for knockout input fields.
 *
 * @author Elmar Frank
 * @since 1.0
 */
class KnockoutAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@x1/knockout/assets';
	public $basePath   = '@webroot/app/knockout';

	public $css = [
	];

	public $js         = [
		'knockout.config.js',
		'knockout.proto.js',

		'binding-handlers/knockout.bindinghandlers.checkbox.js'    => 'binding-handlers/knockout.bindinghandlers.checkbox.min.js',
		'binding-handlers/knockout.bindinghandlers.fadein.js'      => 'binding-handlers/knockout.bindinghandlers.fadein.min.js',
		'binding-handlers/knockout.bindinghandlers.hiddenvalue.js' => 'binding-handlers/knockout.bindinghandlers.hiddenvalue.min.js',
		'binding-handlers/knockout.bindinghandlers.typeahead.js'   => 'binding-handlers/knockout.bindinghandlers.typeahead.min.js',
		'binding-handlers/ko.binding.select2.js',

		'extenders/knockout.extender.display.js'                   => 'extenders/knockout.extender.display.min.js',
		'extenders/knockout.extender.validators.js'                => 'extenders/knockout.extender.validators.min.js',
		'extenders/knockout.extender.datetime.js'                  => 'extenders/knockout.extender.datetime.min.js',
		'extenders/knockout.extender.decimal.js'                   => 'extenders/knockout.extender.decimal.min.js',
		'extenders/knockout.extender.bool.js'                      => 'extenders/knockout.extender.bool.min.js',

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
		'x1\assets\moment\MomentAsset',
		'yii\validators\ValidationAsset',
		'x1\knockout\KnockoutJsAsset',
	];

}
