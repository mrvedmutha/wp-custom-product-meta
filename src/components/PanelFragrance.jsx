import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, Notice } from '@wordpress/components';

/**
 * PanelFragrance
 *
 * Gutenberg sidebar panel for fragrance note meta fields.
 * Manages: top notes, middle notes, and base notes.
 * All three fields must be populated for the Key Notes block to render.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelFragrance = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const getField = (key) => meta?.[key] ?? '';
	const setField = (key, value) => setMeta({ ...meta, [key]: value });

	return (
		<PluginDocumentSettingPanel name="eternal-product-fragrance" title="Fragrance Notes">
			<Notice status="info" isDismissible={false}>
				All three fields must be filled for the Key Notes block to render.
			</Notice>

			<TextControl
				label="Top Notes"
				value={getField('product_notes_top')}
				onChange={(value) => setField('product_notes_top', value)}
			/>

			<TextControl
				label="Middle Notes"
				value={getField('product_notes_middle')}
				onChange={(value) => setField('product_notes_middle', value)}
			/>

			<TextControl
				label="Base Notes"
				value={getField('product_notes_base')}
				onChange={(value) => setField('product_notes_base', value)}
			/>
		</PluginDocumentSettingPanel>
	);
};

export default PanelFragrance;
