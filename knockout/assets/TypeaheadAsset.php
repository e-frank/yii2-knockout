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
class TypeaheadAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/twitter/typeahead.js/dist';

	public $css = [
	];

	public $js         = [
		'bloodhound.min.js',
		'typeahead.jquery.min.js',
	];

}
