<?php
namespace x1\knockout;

use yii\base\BootstrapInterface;
use yii\base\Application;

class KnockoutBootstrapClass implements BootstrapInterface
{
    public function bootstrap($app)
    {
    	$app->set('yii\validator\DateValidator', ['format' => 'yyyy-M-d H:m:s'])
    }
}
?>