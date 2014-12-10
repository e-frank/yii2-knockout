<?
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
?>

var <?=$namespace?>=<?=$namespace?> || {};
<?=$namespace?>.<?=$name?> = {
	create: function(options) {
		//var self = ko.mapping.fromJS(options.data, <?= $mapping ?>);
		var self = new <?=$namespace?>.prototype();
		ko.mapping.fromJS(options.data, <?= $mapping ?>, self);
		self.init();

<?
$mappings = [];
foreach ($attributes as $attribute => $extenders) {
	$mappings[$attribute] = ArrayHelper::remove($extenders, 'mapping');
	echo $this->render('_attribute', ['attribute' => $attribute, 'extenders' => $extenders, 'model' => $model]);
}
?>

		//this.prototype = new <?=$namespace?>.prototype();
		//<?=$namespace?>.prototype.call(this);


		return self;
	}
<?
$mappings = array_filter($mappings);
foreach ($mappings as $key => $mapping) {
	echo sprintf('	,%1$s: { create: function(options) { return ko.mapping.fromJS(options.data, %2$s); }}'."\r\n", Json::encode($key), $mapping);
}
?>
}
