<?
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
?>

var <?=$namespace?>=<?=$namespace?> || {};
<?=$namespace?>.<?=$name?> = {
	create: function(options) {

		var self = ko.mapping.fromJS(options.data, <?= $mapping ?>);

<?
$mappings = [];
foreach ($attributes as $attribute => $extenders) {
	$mappings[$attribute] = ArrayHelper::remove($extenders, 'mapping');
	echo $this->render('_attribute', ['attribute' => $attribute, 'extenders' => $extenders, 'model' => $model]);
}
?>

		self.prototype = new <?=$namespace?>.prototype();
		<?=$namespace?>.prototype.call(self);


		return self;
	}
<?
$mappings = array_filter($mappings);
foreach ($mappings as $key => $mapping) {
	echo sprintf("	,%s: { create: function(options) { return ko.mapping.fromJS(options.data, %s); }}\r\n", Json::encode($key), $mapping);
}
?>
}
