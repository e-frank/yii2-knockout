<?php
/**
 * @copyright Elmar Frank 2015
 */

namespace x1\knockout;

class YiiAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@yii/assets';
    // public $js = [
    //     'yii.js',
    // ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
