<?
use yii\helpers\ArrayHelper;
use yii\helpers\Json;


// $attribute = ArrayHelper::getValue($item, 'attribute', 'attribute');
// $attribute = $item[0];

// var_dump($attribute);




// $validators = [];
// if (!empty($model)) {
// 	$v = $model->getActiveValidators($attribute);
// 	foreach ($v as $key => $value) {
// 		$validators[] = $value->clientValidateAttribute($model, $attribute, $this);
// 	}
// }


// foreach ($v as $validator) {
//     foreach ($validator->attributes as $attribute) {
//         $js = $validator->clientValidateAttribute($model, $attribute, $this);
//         if (!empty($js)) {
//             if (!isset($validators[$attribute]))
//                 $validators[$attribute] = [];
//             $validators[$attribute][] = $js;
//         }
//     }
// }

// print_r($options);
// print "\r\n --- \r\n";

if (!empty($extenders)) {
?>
		if (self.<?= $attribute ?>)	self.<?= $attribute ?>	= self.<?= $attribute ?>.extend(<?= Json::encode($extenders) ?>);
<?
}
?>