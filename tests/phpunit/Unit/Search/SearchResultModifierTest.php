<?php

namespace SIL\Tests\Search;

use SIL\Search\SearchResultModifier;

use SMW\DIProperty;

/**
 * @covers \SIL\Search\SearchResultModifier
 *
 * @group semantic-interlanguage-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SearchResultModifierTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$languageResultMatchFinder = $this->getMockBuilder( '\SIL\Search\LanguageResultMatchFinder' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SIL\Search\SearchResultModifier',
			new SearchResultModifier( $languageResultMatchFinder )
		);
	}

	public function testAddSearchProfile() {

		$languageResultMatchFinder = $this->getMockBuilder( '\SIL\Search\LanguageResultMatchFinder' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SearchResultModifier( $languageResultMatchFinder );

		$profiles = array();

		$instance->addSearchProfile( $profiles );

		$this->assertArrayHasKey(
			'sil',
			$profiles
		);
	}

	public function testAddSearchFormForSILProfile() {

		$languageResultMatchFinder = $this->getMockBuilder( '\SIL\Search\LanguageResultMatchFinder' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SearchResultModifier( $languageResultMatchFinder );

		$request = $this->getMockBuilder( '\WebRequest' )
			->disableOriginalConstructor()
			->getMock();

		$request->expects( $this->once() )
			->method( 'getVal' )
			->with( $this->equalTo( 'languagefilter' ) )
			->will( $this->returnValue( 'vi' ) );

		$context = $this->getMockBuilder( '\IContextSource' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$context->expects( $this->once() )
			->method( 'getRequest' )
			->will( $this->returnValue( $request ) );

		$specialSearch = $this->getMockBuilder( '\SpecialSearch' )
			->disableOriginalConstructor()
			->getMock();

		$specialSearch->expects( $this->once() )
			->method( 'getContext' )
			->will( $this->returnValue( $context ) );

		$specialSearch->expects( $this->once() )
			->method( 'setExtraParam' )
			->with(
				$this->equalTo( 'languagefilter' ),
				$this->equalTo( 'vi' ) );

		$form = '';
		$opts = array( 'Foo' => 'Bar' );

		$this->assertFalse(
			$instance->addSearchProfileForm( $specialSearch, 'sil', $form, $opts )
		);

		$this->assertContains(
			'languagefilter',
			$form
		);

		$this->assertContains(
			'Foo',
			$form
		);
	}

	public function testNoSearchFormForNonSILProfile() {

		$languageResultMatchFinder = $this->getMockBuilder( '\SIL\Search\LanguageResultMatchFinder' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SearchResultModifier( $languageResultMatchFinder );

		$specialSearch = $this->getMockBuilder( '\SpecialSearch' )
			->disableOriginalConstructor()
			->getMock();

		$form = '';
		$opts = array();

		$this->assertTrue(
			$instance->addSearchProfileForm( $specialSearch, 'foo', $form, $opts )
		);
	}

	public function testNoPostFilteringForNonSILProfile() {

		$languageResultMatchFinder = $this->getMockBuilder( '\SIL\Search\LanguageResultMatchFinder' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SearchResultModifier( $languageResultMatchFinder );

		$request = $this->getMockBuilder( '\WebRequest' )
			->disableOriginalConstructor()
			->getMock();

		$request->expects( $this->once() )
			->method( 'getVal' )
			->with( $this->equalTo( 'profile' ) )
			->will( $this->returnValue( 'foo' ) );

		$titleMatches = false;
		$textMatches = false;

		$this->assertFalse(
			$instance->applyLanguageFilterToResultMatches( $request, $titleMatches, $textMatches )
		);
	}

	public function testTryPostFilteringForSILProfileByValidLanguageCode() {

		$titleMatches = $this->getMockBuilder( '\SearchResultSet' )
			->disableOriginalConstructor()
			->getMock();

		$textMatches = $this->getMockBuilder( '\SearchResultSet' )
			->disableOriginalConstructor()
			->getMock();

		$languageResultMatchFinder = $this->getMockBuilder( '\SIL\Search\LanguageResultMatchFinder' )
			->disableOriginalConstructor()
			->getMock();

		$languageResultMatchFinder->expects( $this->at( 0 ) )
			->method( 'matchResultsToLanguage' )
			->with( $this->equalTo( $titleMatches ) );

		$languageResultMatchFinder->expects( $this->at( 1 ) )
			->method( 'matchResultsToLanguage' )
			->with( $this->equalTo( $textMatches ) );

		$instance = new SearchResultModifier( $languageResultMatchFinder );

		$request = $this->getMockBuilder( '\WebRequest' )
			->disableOriginalConstructor()
			->getMock();

		$request->expects( $this->at( 0 ) )
			->method( 'getVal' )
			->with( $this->equalTo( 'profile' ) )
			->will( $this->returnValue( 'sil' ) );

		$request->expects( $this->at( 1 ) )
			->method( 'getVal' )
			->with( $this->equalTo( 'languagefilter' ) )
			->will( $this->returnValue( 'en' ) );

		$this->assertTrue(
			$instance->applyLanguageFilterToResultMatches( $request, $titleMatches, $textMatches )
		);
	}

	/**
	 * @dataProvider invalidLanguageCodeProvider
	 */
	public function testTryPostFilteringForSILProfileByInvalidLanguageCode( $invalidLanguageCode ) {

		$languageResultMatchFinder = $this->getMockBuilder( '\SIL\Search\LanguageResultMatchFinder' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SearchResultModifier( $languageResultMatchFinder );

		$request = $this->getMockBuilder( '\WebRequest' )
			->disableOriginalConstructor()
			->getMock();

		$request->expects( $this->at( 0 ) )
			->method( 'getVal' )
			->with( $this->equalTo( 'profile' ) )
			->will( $this->returnValue( 'sil' ) );

		$request->expects( $this->at( 1 ) )
			->method( 'getVal' )
			->with( $this->equalTo( 'languagefilter' ) )
			->will( $this->returnValue( $invalidLanguageCode ) );

		$titleMatches = false;
		$textMatches = false;

		$this->assertFalse(
			$instance->applyLanguageFilterToResultMatches( $request, $titleMatches, $textMatches )
		);
	}

	public function invalidLanguageCodeProvider() {

		$provider = array(
			array( null ),
			array( '' ),
			array( false ),
			array( '-' )
		);

		return $provider;
	}

}
