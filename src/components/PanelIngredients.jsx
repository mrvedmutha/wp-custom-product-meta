import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, TextareaControl } from '@wordpress/components';

/**
 * PanelIngredients
 *
 * Gutenberg sidebar panel for ingredient and safety meta fields.
 * Manages: INCI list, ingredients disclaimer, and allergy info.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelIngredients = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const getField = (key) => meta?.[key] ?? '';
	const setField = (key, value) => setMeta({ ...meta, [key]: value });

	return (
		<PluginDocumentSettingPanel name="eternal-product-ingredients" title="Ingredients &amp; Safety">
			<TextareaControl
				label="INCI List (HTML allowed)"
				value={getField('product_inci')}
				onChange={(value) => setField('product_inci', value)}
				rows={6}
			/>

			<TextareaControl
				label="Ingredients Disclaimer"
				value={getField('product_ingredients_disclaimer')}
				onChange={(value) => setField('product_ingredients_disclaimer', value)}
				rows={3}
			/>

			<TextControl
				label="Allergy Info"
				value={getField('product_allergy_info')}
				onChange={(value) => setField('product_allergy_info', value)}
			/>
		</PluginDocumentSettingPanel>
	);
};

export default PanelIngredients;
