import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, TextControl, TextareaControl, PanelRow } from '@wordpress/components';

const EMPTY_FEATURE = { image_id: 0, image_url: '', heading: '', body: '' };

/**
 * PanelProductFeatures
 *
 * Gutenberg sidebar panel for dynamic product feature entries.
 * Each feature has an image (media library), a heading, and a body
 * (newlines rendered as paragraphs on the frontend — no HTML required).
 *
 * Stored as a JSON array in the `product_features` meta key.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelProductFeatures = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const features = (() => {
		try {
			const parsed = JSON.parse(meta?.product_features || '[]');
			return Array.isArray(parsed) ? parsed : [];
		} catch {
			return [];
		}
	})();

	const persist = (updated) => {
		setMeta({ ...meta, product_features: JSON.stringify(updated) });
	};

	const addFeature = () => persist([...features, { ...EMPTY_FEATURE }]);

	const removeFeature = (index) => persist(features.filter((_, i) => i !== index));

	const updateFeature = (index, updates) => {
		persist(features.map((f, i) => (i === index ? { ...f, ...updates } : f)));
	};

	return (
		<PluginDocumentSettingPanel name="eternal-product-features" title="Product Features">
			{features.length === 0 && (
				<PanelRow>
					<p style={{ color: '#757575', fontSize: '12px', margin: '0 0 12px' }}>
						No features added yet. Click below to add your first one.
					</p>
				</PanelRow>
			)}

			{features.map((feature, index) => (
				<div
					key={index}
					style={{
						borderTop: '1px solid #e0e0e0',
						paddingTop: '12px',
						marginBottom: '4px',
					}}
				>
					{/* Feature header row */}
					<div
						style={{
							display: 'flex',
							justifyContent: 'space-between',
							alignItems: 'center',
							marginBottom: '12px',
						}}
					>
						<strong
							style={{ fontSize: '12px', textTransform: 'uppercase', letterSpacing: '0.5px' }}
						>
							Feature {index + 1}
						</strong>
						<Button
							variant="tertiary"
							isDestructive
							onClick={() => removeFeature(index)}
							style={{ fontSize: '11px', height: 'auto', padding: '2px 6px' }}
						>
							Remove
						</Button>
					</div>

					{/* Image picker */}
					<PanelRow>
						<div style={{ width: '100%', marginBottom: '8px' }}>
							<p style={{ margin: '0 0 6px', fontSize: '11px', fontWeight: 600, color: '#1e1e1e' }}>
								Feature Image
							</p>

							{feature.image_url && (
								<img
									src={feature.image_url}
									alt=""
									style={{
										width: '100%',
										height: '130px',
										objectFit: 'cover',
										borderRadius: '3px',
										marginBottom: '6px',
										display: 'block',
									}}
								/>
							)}

							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) =>
										updateFeature(index, {
											image_id: media.id,
											image_url: media.url,
										})
									}
									allowedTypes={['image']}
									value={feature.image_id || null}
									render={({ open }) => (
										<Button
											variant="secondary"
											onClick={open}
											style={{ width: '100%', justifyContent: 'center' }}
										>
											{feature.image_url ? 'Change Image' : 'Select Image'}
										</Button>
									)}
								/>
							</MediaUploadCheck>

							{feature.image_url && (
								<Button
									variant="tertiary"
									isDestructive
									onClick={() => updateFeature(index, { image_id: 0, image_url: '' })}
									style={{ fontSize: '11px', marginTop: '4px', height: 'auto', padding: '2px 0' }}
								>
									Remove image
								</Button>
							)}
						</div>
					</PanelRow>

					{/* Heading */}
					<TextControl
						label="Heading"
						placeholder="Sensual Hydration. Polished Skin."
						value={feature.heading}
						onChange={(value) => updateFeature(index, { heading: value })}
					/>

					{/* Body */}
					<TextareaControl
						label="Body"
						help="Press Enter twice between paragraphs — they'll display as separate paragraphs on the site."
						placeholder={'First paragraph...\n\nSecond paragraph...'}
						value={feature.body}
						onChange={(value) => updateFeature(index, { body: value })}
						rows={6}
					/>
				</div>
			))}

			<Button
				variant="secondary"
				onClick={addFeature}
				style={{ width: '100%', justifyContent: 'center', marginTop: '12px' }}
			>
				+ Add Feature
			</Button>
		</PluginDocumentSettingPanel>
	);
};

export default PanelProductFeatures;
