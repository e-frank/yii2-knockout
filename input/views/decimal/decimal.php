<input class="form-control text-right" <?= empty($maxlength) ? '' : 'maxlength="'.$maxlength.'"' ?> <?= empty($size) ? '' : 'size="'.$size.'"' ?> data-bind="'value': <?= $attribute ?>.display" />
<!-- <?= \yii\helpers\Json::encode($options, JSON_PRETTY_PRINT) ?> -->

