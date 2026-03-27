import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, TextareaControl } from '@wordpress/components';

/**
 * PanelHowToUse
 *
 * Gutenberg sidebar panel for usage and application meta fields.
 * Manages: how to use, storage & warnings, and dosage instructions.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelHowToUse = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const getField = (key) => meta?.[key] ?? '';
	const setField = (key, value) => setMeta({ ...meta, [key]: value });

	return (
		<PluginDocumentSettingPanel name="eternal-product-how-to-use" title="How To Use">
			<TextareaControl
				label="How to Apply / Use (HTML allowed)"
				value={getField('product_how_to_use')}
				onChange={(value) => setField('product_how_to_use', value)}
				rows={5}
			/>

			<TextareaControl
				label="Storage &amp; Warnings (HTML allowed)"
				value={getField('product_storage_warnings')}
				onChange={(value) => setField('product_storage_warnings', value)}
				rows={3}
			/>

			<TextControl
				label="Dosage Instructions"
				value={getField('product_dosage_instructions')}
				onChange={(value) => setField('product_dosage_instructions', value)}
			/>
		</PluginDocumentSettingPanel>
	);
};

export default PanelHowToUse;
