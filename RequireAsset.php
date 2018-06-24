<?php
namespace x1\knockout;

class RequireAsset extends \yii\web\AssetBundle 
{

	public $sourcePath = '@x1/knockout/assets/requirejs';

	public $js = [
		'require.js',
	];

	public $css = [
	];

}