# Semantic Interlanguage Links

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/SemanticInterlanguageLinks.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/SemanticInterlanguageLinks)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticInterlanguageLinks/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticInterlanguageLinks/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticInterlanguageLinks/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticInterlanguageLinks/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-interlanguage-links/version.png)](https://packagist.org/packages/mediawiki/semantic-interlanguage-links)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-interlanguage-links/d/total.png)](https://packagist.org/packages/mediawiki/semantic-interlanguage-links)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:semantic-interlanguage-links/badge.png)](https://www.versioneye.com/php/mediawiki:semantic-interlanguage-links)

Semantic Interlanguage Links (a.k.a. SIL) is a [Semantic Mediawiki][smw] extension to
create and manage interlanguage links.

This extension creates interlanguage links and provides queryable annotations that can connect
pages with similar content for different languages to be accessible via the [sitelink navigation][sitelink]
by using the `INTERLANGUAGELINK` parser function.

## Requirements

- PHP 5.3.2 or later
- MediaWiki 1.23 or later
- [Semantic MediaWiki][smw] 2.1+

## Installation

The recommended way to install Semantic Interlanguage Links is by using [Composer][composer] with an entry in MediaWiki's `composer.json`.

```json
{
	"require": {
		"mediawiki/semantic-interlanguage-links": "~1.0"
	}
}
```
1. From your MediaWiki installation directory, execute
   `composer require mediawiki/semantic-interlanguage-links:~1.0`
2. Navigate to _Special:Version_ on your wiki and verify that the package
   have been successfully installed.

## Usage

The parser function `{{INTERLANGUAGELINK: language code  | interlanguage reference }}` provides in-text annotation support with the first argument being a language code (e.g `es`, `ja`) while the second argument is defined as `interlanguage reference` pointer that can be described by an arbitrary identifier to connect pages for different languages with similar content.

The parser function `{{INTERLANGUAGELIST: interlanguage reference | template }}` can generate a customizable language target link list for the selected `interlanguage reference` to be available as wikitext inclusion using a template with the following parameters:
- `target-link` being the target link
- `lang-code` the language code
- `lang-name` representing the localized language name for the language code
- `list-pos` declaring the list position

SIL expects that only one specific language is asssigned to a content page and if multiple assignments are made an error notice will be displayed.

### Example

If `Foo` and `Bar` share the same reference (`Lorem ipsum`) for a non-existing language assignment then both will link to each other and be available through the sitelink navigation and as property annotation.

```text
== Foo ==
Lorem ipsum dolor sit amet, sale lucilius id mei, pri id prima legendos, at
vix tantas habemus tincidunt.

{{INTERLANGUAGELINK:la|Lorem ipsum}}
```
```text
== Bar ==
{{INTERLANGUAGELIST:Lorem ipsum|InterlanguageLinks}}

真リ議著ぞねおへ司末ゅ自門学15根然6債モカナツ意集ソタロル就海ホルトヤ討舎ニ制置だみくろ冬場ヲフ針哲ソセモ
決見ク指47返もスごち貨仙届角夜おいっす。

{{INTERLANGUAGELINK:ja|Lorem ipsum}}
```

The `Template:InterlanguageLinks` with `<includeonly><span style="margin-right: 10px">[[{{{target-link}}}|{{{lang-name}}}]]</span></includeonly>` will output all available links to the `Lorem ipsum` reference on top of the page `Bar`.

A short [video](https://vimeo.com/108833255) demonstrates "How SIL works" without much user interaction or complex procedures.

### Other features

The page content language is set from the `INTERLANGUAGELINK` created annotation together with an auto-updated sitelink navigation for pages that point to the same `interlanguage reference`.

`Page content language`, `Interlanguage reference`, and `Has interlanguage links` are deployed as predefined properties which can be used to create customized `#ask` queries.

SIL provides a `by Language` `Special:Search` filtering option to match interlanguage property annotations for pre-selected articles. If the `by Language` profile is used together with a specific language filter then any pre-selected article (provided by the `SearchEngine`) that does not match the language will be excluded from the result list.

A category page will only display pages that match the annotated category page content language property. In cases where no language has been assigned (or filtering has been disabled), the category is to display all pages without any filtering.

### Configuration

`$GLOBALS['egSILCacheType'] = CACHE_ANYTHING;` is being set to be the default value to improve query lookups during each page view with cache invalidation being carried out during any delete, change or move action.

In case `$GLOBALS['wgHideInterlanguageLinks']` is enabled, no sitelinks or annotations are created.

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* [File an issue](https://github.com/SemanticMediaWiki/SemanticLanguageLinks/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticLanguageLinks/pulls)
* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Freenode.

### Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[contributors]: https://github.com/SemanticMediaWiki/SemanticLanguageLinks/graphs/contributors
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticLanguageLinks
[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[composer]: https://getcomposer.org/
[sitelink]: https://www.semantic-mediawiki.org/wiki/File:Extension-sil-sitelink.png
