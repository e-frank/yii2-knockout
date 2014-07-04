<?
namespace efrank\knockout\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\web\View;


class DatePicker extends \yii\widgets\InputWidget {
	const TYPE_INPUT = 1;
	const TYPE_COMPONENT_PREPEND = 2;
	const TYPE_COMPONENT_APPEND = 3;
	const TYPE_INLINE = 4;

	public $type = self::TYPE_COMPONENT_PREPEND;
    public $format = 'yyyy-mm-dd';


    /**
     * @var string The size of the input - 'lg', 'md', 'sm', 'xs'
     */
    public $size;
    /**
     * @var array the HTML attributes for the input tag.
     */
    public $options = [];

    /**
     * @var string identifier for the target DateTimePicker element
     */
    private $_id;


    /**
     * Initializes the widget
     *
     * @throw InvalidConfigException
     */
    public function init()
    {
    	parent::init();
    	if ($this->type < 1 || $this->type > 4 || !is_int($this->type)) {
    		throw new InvalidConfigException("Invalid value for the property 'type'. Must be an integer between 1 and 4.");
    	}
        // if ($this->convertFormat && isset($this->pluginOptions['format'])) {
        //     $this->pluginOptions['format'] = static::convertDateFormat($this->pluginOptions['format']);
        // }
    	$this->_id = ($this->type == self::TYPE_INPUT) ? '$("#' . $this->options['id'] . '")' : '$("#' . $this->options['id'] . '").parent()';
    	// $this->registerAssets();

#var_dump($this);

    	// $this->registerScript('// sam was here');
    	echo sprintf('<div id="%1$s_wrapper" class="input-group">
    		<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
    		<input class="form-control" data-provide="datepicker" data-date-format="%4$s" id="%1$s_disp" data-bind="value: %3$s.display" />
    		<span class="input-group-addon" data-bind="click: %3$s.clear"><i class="glyphicon glyphicon-remove"></i></span>
    		<span class="input-group-addon" data-bind="click: %3$s.current"><i class="glyphicon glyphicon-time"></i></span>
    		<input type="hidden" id="%1$s" name="%2$s[%3$s]" data-bind="value: %3$s" />
		</div>', $this->options['id'], basename($this->model->className()), $this->attribute, $this->format);
    }


}

?>