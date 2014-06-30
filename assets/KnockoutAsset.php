<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace efrank\assets;

/**
 * This asset bundle provides the base javascript files for the Yii Framework.
 *
 * @author Elmar Frank
 * @since 1.0
 */
class KnockoutAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/e-frank/yii2-knockout/assets/knockout';

	public $css = [
	];

	public $js         = [
		'knockout.js' => 'knockout-3.1.0.js',
	];

}
