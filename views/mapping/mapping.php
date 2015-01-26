<?
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>
// generated at: <?= date('Y-m-d H:i:s') ?>

var <?=$namespace?>=<?=$namespace?> || {};
<?=$namespace?>.<?=$name?> = {
	create: function(options) {
		var self = new <?= $namespace ?>.prototype();

<?
$mappings = [];
$ext = [];
foreach ($attributes as $attribute => $extenders) {
	// $mappings[$attribute] = ArrayHelper::remove($extenders, 'mapping');
	$mappings[$attribute] = ['mapping' => ArrayHelper::remove($extenders, 'mapping'), 'observable' => ArrayHelper::remove($extenders, 'observable', false)];
	$ext[] =  $this->render('_attribute', ['attribute' => $attribute, 'extenders' => $extenders, 'model' => $model]);
}
$mappings = array_filter($mappings);

$mmm = [];
foreach ($mappings as $key => $value) {
	if (isset($value['mapping'])) {
		$observable 			= ArrayHelper::getValue($value, 'observable', true);

		if ($observable)
			$mmm[$key]['create'] 	= new JsExpression(sprintf('function(options) { return ko.observable(ko.mapping.fromJS(options.data, %1$s)); }', ArrayHelper::getValue($value, 'mapping', 'null')));
		else
			$mmm[$key]['create'] 	= new JsExpression(sprintf('function(options) { return ko.mapping.fromJS(options.data, %1$s); }', ArrayHelper::getValue($value, 'mapping', 'null')));

		$mmm[$key]['key'] 		= new JsExpression(sprintf('function(item) { return ko.unwrap(item.id); }'));
	}
}
?>
		ko.mapping.fromJS(options.data, $.extend(<?= $mapping ?>, <?= Json::encode($mmm, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT) ?>), self);

<?= implode("", $ext); ?>

		self.init();

		return self;
	}

}
