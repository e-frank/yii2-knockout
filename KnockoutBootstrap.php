<?php
namespace x1\knockout;

use yii\base\BootstrapInterface;
use yii\base\Application;

class KnockoutBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
    	$app->set('yii\validators\DateValidator', [
    		'class'  => 'yii\validators\DateValidator',
    		'format' => 'yyyy-MM-dd HH:mm:ss',
    		]);
    }
}
?>