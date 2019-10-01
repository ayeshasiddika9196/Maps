<?php

namespace Maps\MediaWiki;

use AlItem;
use ALTree;
use Maps\MediaWiki\Content\GeoJsonContent;
use Maps\Presentation\GeoJsonPage;
use SkinTemplate;

/**
 * Static class for hooks handled by the Maps extension.
 *
 * @since 0.7
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsHooks {

	/**
	 * Adds a link to Admin Links page.
	 *
	 * @since 0.7
	 *
	 * @param ALTree $admin_links_tree
	 *
	 * @return boolean
	 */
	public static function addToAdminLinks( ALTree &$admin_links_tree ) {
		$displaying_data_section = $admin_links_tree->getSection(
			wfMessage( 'smw_adminlinks_displayingdata' )->text()
		);

		// Escape if SMW hasn't added links.
		if ( is_null( $displaying_data_section ) ) {
			return true;
		}

		$smw_docu_row = $displaying_data_section->getRow( 'smw' );

		$maps_docu_label = wfMessage( 'adminlinks_documentation', 'Maps' )->text();
		$smw_docu_row->addItem(
			AlItem::newFromExternalLink( 'https://www.semantic-mediawiki.org/wiki/Extension:Maps', $maps_docu_label )
		);

		return true;
	}

	/**
	 * Adds global JavaScript variables.
	 *
	 * @since 1.0
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/MakeGlobalVariablesScript
	 *
	 * @param array &$vars Variables to be added into the output
	 *
	 * @return boolean true in all cases
	 */
	public static function onMakeGlobalVariablesScript( array &$vars ) {
		$vars['egMapsScriptPath'] = $GLOBALS['wgScriptPath'] . '/extensions/Maps/'; // TODO: wgExtensionDirectory?
		$vars['egMapsDebugJS'] = $GLOBALS['egMapsDebugJS'];
		$vars['egMapsAvailableServices'] = $GLOBALS['egMapsAvailableServices'];
		$vars['egMapsLeafletLayersApiKeys'] = $GLOBALS['egMapsLeafletLayersApiKeys'];

		$vars += $GLOBALS['egMapsGlobalJSVars'];

		return true;
	}

	public static function onSkinTemplateNavigation( SkinTemplate $skinTemplate, array &$links ) {
		if ( $skinTemplate->getTitle() === null ) {
			return true;
		}

		if ( $skinTemplate->getTitle()->getNamespace() === NS_GEO_JSON ) {
			if ( array_key_exists( 'edit', $links['views'] ) ) {
				$links['views']['edit']['text'] = wfMessage(
					$skinTemplate->getTitle()->exists() ? 'maps-geo-json-edit-source': 'maps-geo-json-create-source'
				);
			}
		}

		return true;
	}

	public static function onBeforeDisplayNoArticleText( \Article $article ) {
		return $article->getTitle()->getNamespace() !== NS_GEO_JSON;
	}

	public static function onShowMissingArticle( \Article $article ) {
		// TODO: save does not work on new page yet: API response says "missing"
		// TODO: show below UI only after clicking "create new page with visual map editor"

//		$geoJsonPage = new GeoJsonPage(
//			GeoJsonContent::newEmptyContentString()
//		);
//
//		$geoJsonPage->addToOutputPage( $article->getContext()->getOutput() );

		return true;
	}

	public static function onRegisterTags( array &$tags ) {
		$tags[] = 'maps-visual-edit';
		return true;
	}

	public static function onChangeTagsAllowedAdd( array &$allowedTags, array $tags, \User $user = null ) {
		$allowedTags[] = 'maps-visual-edit';
	}

}
