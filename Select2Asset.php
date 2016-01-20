<?php
namespace x1\knockout;

use Yii;
use yii\helpers\Json;

/**
 * This asset bundle loads select2 resources.
 *
 * @author Elmar Frank
 * @since 1.0
 */
class Select2Asset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/select2/select2/dist';
	
	public $js         = [
		'js\select2.min.js',
	];

	public $css        = [
		'css\select2.min.css',
	];

	public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'x1\assets\JqueryHelpersAsset',
	];
}