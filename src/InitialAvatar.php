<?php

namespace LasseRafn\InitialAvatarGenerator;

use Intervention\Image\Image;
use Intervention\Image\ImageCache;
use Intervention\Image\ImageManager;
use LasseRafn\Initials\Initials;
use LasseRafn\StringScript;

class InitialAvatar
{
	/** @var ImageManager */
	private $image;

	/** @var Initials */
	private $initials;

	private $parameter_cacheTime = 0;
	private $parameter_fontSize = 0.5;
	private $parameter_initials = 'JD';
	private $parameter_name = 'John Doe';
	private $parameter_size = 48;
	private $parameter_bgColor = '#000';
	private $parameter_fontColor = '#fff';
	private $parameter_rounded = false;
	private $parameter_smooth = false;
	private $parameter_autofont = false;
	private $parameter_keepCase = false;
	private $parameter_fontFile = '/fonts/OpenSans-Regular.ttf';

	public function __construct()
	{
		$this->image = new ImageManager();
		$this->initials = new Initials();
	}

	/**
	 * Set the name used for generating initials.
	 *
	 * @param string $nameOrInitials
	 *
	 * @return InitialAvatar
	 */
	public function name($nameOrInitials)
	{
		$this->parameter_name = $nameOrInitials;
		$this->initials->name($nameOrInitials);

		return $this;
	}

	/**
	 * Set the length of the generated initials.
	 *
	 * @param int $length
	 *
	 * @return InitialAvatar
	 */
	public function length($length = 2)
	{
		$this->initials->length($length);

		return $this;
	}

	/**
	 * Set time avatar/image size in pixels.
	 *
	 * @param int $size
	 *
	 * @return InitialAvatar
	 */
	public function size($size)
	{
		$this->parameter_size = (int) $size;

		return $this;
	}

	/**
	 * Set the background color.
	 *
	 * @param string $background
	 *
	 * @return InitialAvatar
	 */
	public function background($background)
	{
		$this->parameter_bgColor = (string) $background;

		return $this;
	}

	/**
	 * Set the font color.
	 *
	 * @param string $color
	 *
	 * @return InitialAvatar
	 */
	public function color($color)
	{
		$this->parameter_fontColor = (string) $color;

		return $this;
	}

	/**
	 * Set the font file by path or int (1-5).
	 *
	 * @param string|int $font
	 *
	 * @return InitialAvatar
	 */
	public function font($font)
	{
		$this->parameter_fontFile = $font;

		return $this;
	}

	/**
	 * Set cache time (in minutes)
	 * 0 = no cache.
	 *
	 * @param int $minutes
	 *
	 * @return InitialAvatar
	 */
	public function cache($minutes = 60)
	{
		$this->parameter_cacheTime = (int) $minutes;

		return $this;
	}

	/**
	 * Set if should make a round image or not.
	 *
	 * @param bool $rounded
	 *
	 * @return InitialAvatar
	 */
	public function rounded($rounded = true)
	{
		$this->parameter_rounded = (bool) $rounded;

		return $this;
	}

	/**
	 * Set if should detect character script
	 * and use a font that supports it.
	 *
	 * @param bool $autofont
	 *
	 * @return InitialAvatar
	 */
	public function autoFont($autofont = true)
	{
		$this->parameter_autofont = (bool) $autofont;

		return $this;
	}

	/**
	 * Set if should make a rounding smoother with a resizing hack.
	 *
	 * @param bool $smooth
	 *
	 * @return InitialAvatar
	 */
	public function smooth($smooth = true)
	{
		$this->parameter_smooth = (bool) $smooth;

		return $this;
	}

	/**
	 * Set if should skip uppercasing the name.
	 *
	 * @param bool $keepCase
	 *
	 * @return InitialAvatar
	 */
	public function keepCase($keepCase = true)
	{
		$this->parameter_keepCase = (bool) $keepCase;

		return $this;
	}

	/**
	 * Set the font size in percentage
	 * (0.1 = 10%).
	 *
	 * @param float $size
	 *
	 * @return InitialAvatar
	 */
	public function fontSize($size = 0.5)
	{
		$this->parameter_fontSize = number_format($size, 2);

		return $this;
	}

	/**
	 * Generate the image.
	 *
	 * @param null|string $name
	 *
	 * @return Image
	 */
	public function generate($name = null)
	{
		if ($name !== null) {
			$this->parameter_name = $name;
			$this->parameter_initials = $this->initials->keepCase($this->getParameterKeepCase())->generate($name);
		}

		if ($this->parameter_cacheTime === 0) {
			$img = $this->makeAvatar($this->image);
		} else {
			$img = $this->image->cache(function (ImageCache $image) {
				return $this->makeAvatar($image);
			}, $this->parameter_cacheTime, true);
		}

		return $img;
	}

	/**
	 * Will return the generated initials.
	 *
	 * @return string
	 */
	public function getInitials()
	{
		return $this->initials->keepCase($this->getParameterKeepCase())->name($this->parameter_name)->getInitials();
	}

	/**
	 * Will return the background color parameter.
	 *
	 * @return string
	 */
	public function getParameterBackgroundColor()
	{
		return $this->parameter_bgColor;
	}

	/**
	 * Will return the font color parameter.
	 *
	 * @return string
	 */
	public function getParameterColor()
	{
		return $this->parameter_fontColor;
	}

	/**
	 * Will return the font size parameter.
	 *
	 * @return float
	 */
	public function getParameterFontSize()
	{
		return $this->parameter_fontSize;
	}

	/**
	 * Will return the font size parameter.
	 *
	 * @return string|int
	 */
	public function getParameterFontFile()
	{
		return $this->parameter_fontFile;
	}

	/**
	 * Will return the round parameter.
	 *
	 * @return bool
	 */
	public function getParameterRounded()
	{
		return $this->parameter_rounded;
	}

	/**
	 * Will return the smooth parameter.
	 *
	 * @return bool
	 */
	public function getParameterSmooth()
	{
		return $this->parameter_smooth;
	}

	/**
	 * Will return the round parameter.
	 *
	 * @return int
	 */
	public function getParameterSize()
	{
		return $this->parameter_size;
	}

	/**
	 * Will return the keepCase parameter.
	 *
	 * @return boolean
	 */
	public function getParameterKeepCase()
	{
		return $this->parameter_keepCase;
	}

	/**
	 * Will return the autofont parameter.
	 *
	 * @return bool
	 */
	public function getParameterAutoFont()
	{
		return $this->parameter_autofont;
	}

	/**
	 * @param ImageManager|ImageCache $image
	 *
	 * @return Image|ImageCache
	 */
	private function makeAvatar($image)
	{
		$size = $this->getParameterSize();
		$bgColor = $this->getParameterBackgroundColor();
		$name = $this->getInitials();
		$fontFile = $this->findFontFile();
		$color = $this->getParameterColor();
		$fontSize = $this->getParameterFontSize();

		if ($this->getParameterRounded() && $this->getParameterSmooth()) {
			$size *= 5;
		}

		$avatar = $image->canvas($size, $size, !$this->getParameterRounded() ? $bgColor : null);

		if ($this->getParameterRounded()) {
			$avatar = $avatar->circle($size - 2, $size / 2, $size / 2, function ($draw) use ($bgColor) {
				return $draw->background($bgColor);
			});
		}

		if ($this->getParameterRounded() && $this->getParameterSmooth()) {
			$size /= 5;
			$avatar->resize($size, $size);
		}

		return $avatar->text($name, $size / 2, $size / 2, function ($font) use ($size, $color, $fontFile, $fontSize) {
			$font->file($fontFile);
			$font->size($size * $fontSize);
			$font->color($color);
			$font->align('center');
			$font->valign('center');
		});
	}

	private function findFontFile()
	{
		$fontFile = $this->getParameterFontFile();

		if ($this->getParameterAutoFont()) {
			$fontFile = $this->getFontByScript();
		}

		if (is_int($fontFile) && in_array($fontFile, [1, 2, 3, 4, 5], false)) {
			return $fontFile;
		}

		if (file_exists($fontFile)) {
			return $fontFile;
		}

		if (file_exists(__DIR__.$fontFile)) {
			return __DIR__.$fontFile;
		}

		if (file_exists(__DIR__.'/'.$fontFile)) {
			return __DIR__.'/'.$fontFile;
		}

		return 1;
	}

	private function getFontByScript()
	{
		if (StringScript::isArabic($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Arabic-Regular.ttf';
		}

		if (StringScript::isArmenian($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Armenian-Regular.ttf';
		}

		if (StringScript::isBengali($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Bengali-Regular.ttf';
		}

		if (StringScript::isGeorgian($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Georgian-Regular.ttf';
		}

		if (StringScript::isHebrew($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Hebrew-Regular.ttf';
		}

		if (StringScript::isMongolian($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Mongolian-Regular.ttf';
		}

		if (StringScript::isThai($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Thai-Regular.ttf';
		}

		if (StringScript::isTibetan($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-Tibetan-Regular.ttf';
		}

		if (StringScript::isChinese($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-CJKJP-Regular.otf';
		}

		if (StringScript::isJapanese($this->getInitials())) {
			return __DIR__.'/fonts/script/Noto-CJKJP-Regular.otf';
		}

		return $this->getParameterFontFile();
	}
}