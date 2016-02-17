<?php
namespace x1\knockout;


/**
 * This asset bundle collects all javascripts for knockout input fields.
 *
 * @author Elmar Frank
 * @since 1.0
 */
class KnockoutJsAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@bower/knockout';

	public $css = [
	];

	public $js         = [
		'knockout-3.4.0',
	];

	public $depends = [
	];

}
