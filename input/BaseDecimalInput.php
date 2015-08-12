<?
namespace x1\knockout\input;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;


abstract class BaseDecimalInput extends \yii\widgets\InputWidget {

    const THOUSANDS_SEPARATOR = 'thousandsSeparator';
    const DECIMAL_SEPARATOR   = 'decimalSeparator';

	public static $autoIdPrefix   = 'x1Decimal';
	public static $defaultFormats = [];
	
	public $decimals           = 2;
	public $percent            = false;
	public $thousandsSeparator = null;
	public $decimalSeparator   = null;


	// TODO: remove, unused?
	public function run() {

		$this->params = ArrayHelper::merge($this->params, [
			'decimals'                => $this->decimals,
			'percent'                 => $this->percent,
			self::THOUSANDS_SEPARATOR => $this->thousandsSeparator,
			self::DECIMAL_SEPARATOR   => $this->decimalSeparator,
		]);

		return parent::run();
	}

	public function init() {
		parent::init();

		//
		// get decimal settings, if not set
		//
		if (empty($this->thousandsSeparator) || empty($this->decimalSeparator)) {
	        // current regional settings
	        $decimal  = ArrayHelper::getValue(self::$defaultFormats, self::DECIMAL_SEPARATOR, null);
	        $thousand = ArrayHelper::getValue(self::$defaultFormats, self::THOUSANDS_SEPARATOR, null);


	        if ($decimal == null || $thousand == null) {

		        $locale = Yii::$app->formatter->locale;
		        if (empty($locale))
		            $locale = Yii::$app->language;


	            if ($decimal == null)
	                $decimal = Yii::$app->formatter->decimalSeparator;
	            if ($thousand == null)
	                $thousand = Yii::$app->formatter->thousandSeparator;

	            if ($decimal == null || $thousand == null) {
	                $fmt = numfmt_create($locale, \NumberFormatter::DECIMAL);
	                if ($decimal == null)
	                    $decimal = numfmt_get_symbol($fmt, \NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
	                if ($thousand == null)
	                    $thousand = numfmt_get_symbol($fmt, \NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
	            }

	            self::$defaultFormats[self::DECIMAL_SEPARATOR]   = $decimal;
	            self::$defaultFormats[self::THOUSANDS_SEPARATOR] = $thousand;
	        }

            if ($this->decimalSeparator == null)
		        $this->decimalSeparator   = $decimal;
            if ($this->thousandsSeparator == null)
		        $this->thousandsSeparator = $thousand;
		}

	}

}

?>