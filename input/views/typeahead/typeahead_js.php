<?php
use yii\helpers\Json;
use yii\web\JsExpression;


if ($this->beginCache('x1cache_' . $id, [
	'variations' => [Yii::$app->language],
	])) { 

$remote = [
	'url'   => $url,
];

if (!empty($filter))
$remote['filter'] = new JsExpression(sprintf('function(list) {
		  return $.map(list, %s);
		}', $filter));

$remote = array_filter($remote);
?>

var bloodhound = bloodhound || {};
bloodhound.<?= $id ?> = new Bloodhound({
	datumTokenizer: function(item) { console.log('item',item); return Bloodhound.tokenizers.obj.whitespace(item.<?= ($displayKey) ?>)},
	queryTokenizer: Bloodhound.tokenizers.whitespace,
	limit: 			<?= $limit ?>,
	remote: 		<?= Json::encode($remote, JSON_FORCE_OBJECT) ?>
});
bloodhound.<?= $id ?>.initialize();

$('#<?= $id ?>').typeahead(null, {
	name: 	  	<?= Json::encode($name) ?>,
	highlight:  true,
	displayKey: function(item) { return item.<?= ($displayKey) ?>; },
	source:     bloodhound.<?= $id ?>.ttAdapter(),
	templates:  {
		empty: <?= Json::encode(sprintf('<div class="tt-empty">%s</div>', $empty)) ?>
		<? if (!empty($suggestion)) { echo sprintf(',suggestion: Handlebars.compile(%s)', Json::encode($suggestion)); } ?>
	}
}).bind('typeahead:selected typeahead:autocompleted', <?= Json::encode($selected) ?>);

/*
.bind('change blur', function () {
	var el  = $('#<?= $id ?>');
	var val = el.val();

	if ((el.data('selected') || '').toUpperCase() == el.val().toUpperCase()) {
		el.val(el.data('selected'));
		return;
	}

    bloodhound.<?= $id ?>.get(val, function(item) {
    	if (item.length == 0) {
    		el.val('');
    	} else if (item.length == 1) {
    		el.val(item[0].<?= $displayKey ?>)
    	}
    	console.log('found item', item); return item;
	})
}).bind('typeahead:selected typeahead:autocompleted', function(e, item) {
	var el  = $('#<?= $id ?>');
	el.data('selected', el.val());
});
*/

<?
	$this->endCache();
}
?>

$('#<?= $id ?>').typeahead('val', <?= Json::encode($value) ?>);