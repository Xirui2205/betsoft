<?php

require_once "Zend/Captcha/Image.php";

class It6_Captcha_Image extends Zend_Captcha_Image {

/**
 * Creates key for global cache from captcha ID
 * @param string $id Captcha ID
 * @return string Memcacehd key
 */
public static function getGlobalCacheKey($id) {
	return "WT:$id";
}

/**
 * Generate captcha
 *
 * @return string captcha ID
 */
public function generate()
{
	$id = parent::generate();
	$file = $this->getImgDir() . $id . $this->getSuffix();
	if (file_exists($file)) {
		$key = static::getGlobalCacheKey($id);
		$data = array(
			'mime' => 'image/png',
			'size' => filesize($file),
			'word' => $this->_word,
			'data' => file_get_contents($file),
		);
		It6_GlobalCache::setKey($key, $data, $this->getExpiration());
	}
	return $id;
}



    /**
     * Display the captcha
     *
     * @param Zend_View_Interface $view
     * @param mixed $element
     * @return string
     */
    public function render(Zend_View_Interface $view = null, $element = null)
    {
        return '<img width="'.$this->getWidth().'" height="'.$this->getHeight().'" alt="'.$this->getImgAlt().'" src="' . '/captcha.php?id=' . $this->getId() . '"/><br/>';
    }
} // class
