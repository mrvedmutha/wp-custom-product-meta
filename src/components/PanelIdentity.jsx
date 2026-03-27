import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, ColorPicker, PanelRow } from '@wordpress/components';

/**
 * PanelIdentity
 *
 * Gutenberg sidebar panel for product identity and card display meta fields.
 * Manages: French subtitle, eyebrow label, tagline, display tag pills,
 * and card background colour.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelIdentity = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const getField = (key) => meta?.[key] ?? '';
	const setField = (key, value) => setMeta({ ...meta, [key]: value });

	return (
		<PluginDocumentSettingPanel
			name="eternal-product-identity"
			title="Product Identity &amp; Card Display"
		>
			<PanelRow>
				<TextControl
					label="French Subtitle"
					value={getField('product_name_fr')}
					onChange={(value) => setField('product_name_fr', value)}
				/>
			</PanelRow>

			<PanelRow>
				<TextControl
					label="Eyebrow Label"
					value={getField('product_eyebrow')}
					onChange={(value) => setField('product_eyebrow', value)}
				/>
			</PanelRow>

			<PanelRow>
				<TextControl
					label="Tagline"
					value={getField('product_tagline')}
					onChange={(value) => setField('product_tagline', value)}
				/>
			</PanelRow>

			<PanelRow>
				<TextControl
					label="Display Tag Pills (comma-separated)"
					value={getField('product_display_tags')}
					onChange={(value) => setField('product_display_tags', value)}
				/>
			</PanelRow>

			<PanelRow>
				<div style={{ width: '100%' }}>
					<p style={{ marginBottom: '8px', fontWeight: 600 }}>Card Background Colour</p>
					<ColorPicker
						color={getField('product_card_bg') || '#f5f5f5'}
						onChangeComplete={(color) => setField('product_card_bg', color.hex)}
					/>
				</div>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
};

export default PanelIdentity;
