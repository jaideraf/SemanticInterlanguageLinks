<?php

namespace SIL\Search;

use Html;
use LanguageNames;
use Language;
use XmlSelect;
use Xml;
use SearchResultSet;
use SpecialSearch;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SearchResultModifier {

	/**
	 * @var LanguageResultMatchFinder|null
	 */
	private $languageResultMatchFinder = null;

	/**
	 * @since 1.0
	 *
	 * @param LanguageResultMatchFinder $languageResultMatchFinder
	 */
	public function __construct( LanguageResultMatchFinder $languageResultMatchFinder ) {
		$this->languageResultMatchFinder = $languageResultMatchFinder;
	}

	/**
	 * @since 1.0
	 *
	 * @param array &$profiles
	 */
	public function addSearchProfile( array &$profiles ) {

		$profiles['sil'] = array(
			'message' => 'sil-search-profile',
			'tooltip' => 'sil-search-profile-tooltip',
			'namespaces' => SpecialSearch::NAMESPACES_CURRENT
		);

		return true;
	}

	/**
	 * @since 1.0
	 *
	 * @param SpecialSearch $search
	 * @param string $profile,
	 * @param string &$form
	 * @param array $opts
	 *
	 * @return boolean
	 */
	public function addSearchProfileForm( SpecialSearch $search, $profile, &$form, $opts ) {

		if ( $profile !== 'sil' ) {
			return true;
		}

		$hidden = '';

		foreach ( $opts as $key => $value ) {
			$hidden .= Html::hidden( $key, $value );
		}

		// $code = $search->getContext()->getLanguage()->getCode();
		$languagefilter = $search->getContext()->getRequest()->getVal( 'languagefilter' );

		if ( $languagefilter !== '' && $languagefilter !== null ) {
			$search->setExtraParam( 'languagefilter', $languagefilter );
		}

		$languages = Language::fetchLanguageNames();

		ksort( $languages );

		$selector = new XmlSelect( 'languagefilter', 'languagefilter' );
		$selector->setDefault( $languagefilter );
		$selector->addOption( wfMessage( 'sil-search-nolanguagefilter' )->text(), '-' );

		foreach ( $languages as $code => $name ) {
			$selector->addOption( "$code - $name", $code );
		}

		$selector = $selector->getHTML();

		$label = Xml::label(
			wfMessage( 'sil-search-languagefilter-label' )->text(),
			'languagefilter'
		) . '&#160;';

		$params = array( 'id' => 'mw-searchoptions' );

		$form = Xml::fieldset( false, false, $params ) .
			$hidden . $label . $selector .
			Html::closeElement( 'fieldset' );

		return false;
	}

	/**
	 * @since 1.0
	 *
	 * @param WebRequest $request
	 * @param SearchResultSet|false $titleMatches
	 * @param SearchResultSet|false $textMatches
	 *
	 * @return boolean
	 */
	public function applyLanguageFilterToResultMatches( $request, &$titleMatches, &$textMatches ) {

		if ( $request->getVal( 'profile' ) !== 'sil' ) {
			return false;
		}

		$languageCode = $request->getVal( 'languagefilter' );

		if ( in_array( $languageCode, array( null, '', '-' ) ) ) {
			return false;
		}

		if ( $titleMatches instanceOf SearchResultSet ) {
			$titleMatches = $this->languageResultMatchFinder->matchResultsToLanguage(
				$titleMatches,
				$languageCode
			);
		}

		if ( $textMatches instanceOf SearchResultSet ) {
			$textMatches = $this->languageResultMatchFinder->matchResultsToLanguage(
				$textMatches,
				$languageCode
			);
		}

		return true;
	}

}
