<?php
/**
 * @copyright Elmar Frank 2015
 */

namespace x1\knockout;

class MomentAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/moment/moment/min';

	// public $js         = [
	// 	'moment.js' => 'moment-with-locales.min.js',
	// ];

	public $require = [
		'map' => [
			// 'moment'        => 'moment-with-locales.min',
			// 'moment-asset/moment' => 'moment-asset/moment-with-locales.min',
			// 'moment/moment' => 'moment-asset/moment-with-locales.min',
		],
		'path' => [
			'moment-asset/moment'          => 'moment-asset/moment-with-locales.min',
			// 'moment-asset/moment' => 'moment-asset/moment-with-locales.min',
			// 'moment/moment'    => 'moment-with-locales.min',
		],
		// 'shim' => [
		// 	'moment' => [
		// 		'exports' => 'moment', 
		// 		// 'deps'    => []
		// 	],
		// ]
	];
}
