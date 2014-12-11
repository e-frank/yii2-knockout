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
class MomentAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/moment/moment/min';

	public $css = [
	];

	public $js         = [
		'moment.js' => 'moment.min.js',
	];

	public $depends = [
	];

}
