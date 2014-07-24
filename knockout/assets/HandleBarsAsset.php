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
class HandlebarsAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/e-frank/yii2-knockout/knockout/assets/handlebars';

	public $css = [
	];

	public $js         = [
		'handlebars.js' => 'handlebars-v1.3.0.js',
	];

}