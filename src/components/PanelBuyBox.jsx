import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, TextareaControl, PanelRow } from '@wordpress/components';

/**
 * PanelBuyBox
 *
 * Gutenberg sidebar panel for buy-box product detail fields.
 * Manages: contains (amount + unit), ingredients, cautionary text,
 * how to apply, and bonus tip.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelBuyBox = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const getField = (key) => meta?.[key] ?? '';
	const setField = (key, value) => setMeta({ ...meta, [key]: value });

	return (
		<PluginDocumentSettingPanel name="eternal-product-buy-box" title="Buy Box">
			{/* ── Contains ── */}
			<PanelRow>
				<div style={{ width: '100%' }}>
					<p
						style={{
							marginBottom: '8px',
							fontWeight: 600,
							fontSize: '11px',
							textTransform: 'uppercase',
							color: '#1e1e1e',
						}}
					>
						Contains
					</p>
					<div style={{ display: 'flex', gap: '8px', alignItems: 'flex-end' }}>
						<div style={{ flex: '0 0 80px' }}>
							<TextControl
								label="Amount"
								type="number"
								min="0"
								value={getField('product_buy_box_amount')}
								onChange={(value) => setField('product_buy_box_amount', value)}
							/>
						</div>
						<div style={{ flex: '1' }}>
							<TextControl
								label="Unit"
								placeholder="ml, capsules, g…"
								value={getField('product_buy_box_unit')}
								onChange={(value) => setField('product_buy_box_unit', value)}
							/>
						</div>
					</div>
				</div>
			</PanelRow>

			{/* ── Ingredients & Safety ── */}
			<PanelRow>
				<div style={{ width: '100%', marginTop: '8px' }}>
					<p
						style={{
							marginBottom: '8px',
							fontWeight: 600,
							fontSize: '11px',
							textTransform: 'uppercase',
							color: '#1e1e1e',
						}}
					>
						Ingredients &amp; Safety
					</p>
				</div>
			</PanelRow>

			<TextareaControl
				label="Ingredients"
				value={getField('product_buy_box_ingredients')}
				onChange={(value) => setField('product_buy_box_ingredients', value)}
				rows={5}
			/>

			<TextareaControl
				label="Cautionary Text"
				value={getField('product_buy_box_caution')}
				onChange={(value) => setField('product_buy_box_caution', value)}
				rows={3}
			/>

			{/* ── How to Apply ── */}
			<PanelRow>
				<div style={{ width: '100%', marginTop: '8px' }}>
					<p
						style={{
							marginBottom: '8px',
							fontWeight: 600,
							fontSize: '11px',
							textTransform: 'uppercase',
							color: '#1e1e1e',
						}}
					>
						How to Apply
					</p>
				</div>
			</PanelRow>

			<TextareaControl
				label="How to Use"
				value={getField('product_buy_box_how_to_apply')}
				onChange={(value) => setField('product_buy_box_how_to_apply', value)}
				rows={5}
			/>

			<TextareaControl
				label="Bonus Tip"
				placeholder="FOR AN AROMATIC EXPERIENCE: Start your body routine…"
				value={getField('product_buy_box_bonus_tip')}
				onChange={(value) => setField('product_buy_box_bonus_tip', value)}
				rows={4}
			/>
		</PluginDocumentSettingPanel>
	);
};

export default PanelBuyBox;
