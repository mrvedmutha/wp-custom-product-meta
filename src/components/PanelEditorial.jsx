import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, TextareaControl, Button } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';

/**
 * PanelEditorial
 *
 * Gutenberg sidebar panel for the editorial section meta fields.
 * Manages: editorial headline, editorial body copy, and editorial image.
 * Leaving the headline empty hides the entire editorial section on the front end.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelEditorial = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const getField = (key) => meta?.[key] ?? '';
	const setField = (key, value) => setMeta({ ...meta, [key]: value });

	const imageId = meta?.product_editorial_image_id ?? 0;

	return (
		<PluginDocumentSettingPanel name="eternal-product-editorial" title="Editorial Section">
			<p style={{ marginTop: 0 }}>Leave Headline empty to hide the entire editorial section.</p>

			<TextControl
				label="Editorial Headline"
				value={getField('product_editorial_headline')}
				onChange={(value) => setField('product_editorial_headline', value)}
			/>

			<TextareaControl
				label="Editorial Body (HTML allowed)"
				value={getField('product_editorial_body')}
				onChange={(value) => setField('product_editorial_body', value)}
				rows={6}
			/>

			<MediaUploadCheck>
				<MediaUpload
					allowedTypes={['image']}
					value={imageId}
					onSelect={(media) => setField('product_editorial_image_id', media.id)}
					render={({ open }) => (
						<Button variant="secondary" onClick={open}>
							{imageId > 0 ? 'Change Image' : 'Select Editorial Image'}
						</Button>
					)}
				/>
			</MediaUploadCheck>

			{imageId > 0 && <p style={{ marginTop: '8px' }}>Attachment ID: {imageId}</p>}
		</PluginDocumentSettingPanel>
	);
};

export default PanelEditorial;
