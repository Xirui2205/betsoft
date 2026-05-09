<?php

class Models_Banner {

	public static function getSuperhomeForms($updateSectionId, $locations) {
		$banners		= Zend_Registry::get('ws')->Banner->getByController(44);
		$banners		= It6_ArrayWrapper::toAssocLikeArray($banners, 'languageId');
		$bannerForms	= array();

		foreach($banners as $langId => $lang) {
			foreach($lang as $i => $banner) {
				$form = self::getSuperhomeForm($updateSectionId, $i);
				$form->setDescription(i18n::tr('Position').': '.$locations[$banner['locationId']]);
				$form->populate($banner);
				$bannerForms[$langId][$banner['locationId']][$banner['bannerId']] = $form;
			}
		}

		return $bannerForms;
	}



	public static function getSuperhomeForm($updateSectionId, $bannerId) {
		$form = new Models_Form_Banner($updateSectionId, array(), $bannerId);
		$form->removeElement('locationId');
		$form->addElement('hidden','locationId');
		$form->removeElement('languageId');
		$form->addElement('hidden','languageId');
		$form->removeElement('validFrom');
		$form->removeElement('validTo');
		$form->removeElement('order');

		return $form;
	}



	public static function getIndexForms($updateSectionId, $locations) {
		$banners		= Zend_Registry::get('ws')->Banner->getByController(1);
		$banners		= It6_ArrayWrapper::toAssocLikeArray($banners, 'languageId');
		$bannerForms	= array();

		foreach($banners as $langId => $lang) {
			foreach($lang as $banner) {
				$form = self::getIndexForm($updateSectionId, $locations);
				$form->populate($banner);
				$bannerForms[$langId][$banner['locationId']][$banner['bannerId']] = $form;
			}
		}

		return $bannerForms;
	}



	public static function getIndexForm($updateSectionId, $locations) {
		$form = new Models_Form_Banner($updateSectionId, $locations);
		$form->removeElement('languageId');
		$form->addElement('hidden','languageId');
		$form->removeElement('text');

		return $form;
	}
}
