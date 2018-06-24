<?php
namespace x1\knockout\helpers;

class DateFormatConverter {

	public static function convertPhpToMoment($format) {
		return strtr($format, [
			'd' => 'DD',
			'j' => 'D',
			'F' => 'MMMM',
			'M' => 'MMM',
			'm' => 'MM',
			'n' => 'M',
			'y' => 'YY',
			'Y' => 'YYYY'
			]);
	}

	public static function convertMomentToPicker($format) {
		return strtr($format, [
			'DDDD' => 'DD',
			'DDD'  => 'D',
			'DD'   => 'dd',
			'D'    => 'd',
			'MMMM' => 'MM',
			'MMM'  => 'M',
			'MM'   => 'mm',
			'M'    => 'm',
			'YYYY' => 'yyyy',
			'YY'   => 'yy',
			]);
	}

	public static function convertPhpToPicker($format) {
		return strtr($format, [
			'd' => 'dd',
			'j' => 'd',
			'F' => 'MM',
			'M' => 'M',
			'm' => 'mm',
			'n' => 'm',
			'y' => 'yy',
			'Y' => 'yyyy',
			]);
	}
}
?>