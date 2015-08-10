<?php
/**
 * @copyright Elmar Frank 2015
 */

namespace x1\knockout;

class ValidationAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@yii/assets';
    // public $js = [
    //     'yii.validation.js',
    // ];
    public $depends = [
        'x1\knockout\YiiAsset',
    ];
}
