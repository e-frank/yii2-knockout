<span class="switch <?= empty($size) ? null : 'switch-' . $size ?>">
	<?php
	if (empty($model) || empty($attribute)) {
		echo \yii\helpers\Html::input('checkbox', $name, $value, array_merge($options, ['id' => $id]));
	} else {
		echo \yii\helpers\Html::activeTextInput($model, $attribute, array_merge($options, ['id' => $id]));
		echo \yii\helpers\Html::label(($label == null) ? $model->getAttributeLabel($attribute) : $label, $id);
	}
	?>
</span>
