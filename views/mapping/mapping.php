<?
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>

var <?=$namespace?>=<?=$namespace?> || {};
<?=$namespace?>.<?=$name?> = {
	create: function(options) {
		var self = new <?= $namespace ?>.prototype();
<?
$mappings = [];
$ext = [];
foreach ($attributes as $attribute => $extenders) {
	$mappings[$attribute] = ArrayHelper::remove($extenders, 'mapping');
	$ext[] =  $this->render('_attribute', ['attribute' => $attribute, 'extenders' => $extenders, 'model' => $model]);
}
$mappings = array_filter($mappings);

$mmm = [];
foreach ($mappings as $key => $value) {
	$mmm[$key]['create'] = new JsExpression(sprintf('function(options) { return ko.mapping.fromJS(options.data, %s); }', $value));
	$mmm[$key]['key'] = new JsExpression(sprintf('function(item) { return ko.unwrap(item.id); }'));
}
?>
		ko.mapping.fromJS(options.data, $.extend(<?= $mapping ?>, <?= Json::encode($mmm, JSON_FORCE_OBJECT) ?>), self);

<?= implode("", $ext); ?>

		self.init();


		return self;
	}

}
