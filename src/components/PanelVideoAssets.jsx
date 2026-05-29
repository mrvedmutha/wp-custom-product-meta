import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { Button, TextControl } from '@wordpress/components';

/* global eternalProductMeta */

const HOME_URL =
	typeof eternalProductMeta !== 'undefined' && eternalProductMeta.homeUrl
		? eternalProductMeta.homeUrl
		: '';

/**
 * Returns 'YouTube', 'Vimeo', 'Local', or null based on the URL shape.
 *
 * @param {string} url - The video URL to inspect.
 * @return {string|null} Detected type label, or null if unrecognised.
 */
const detectType = (url) => {
	if (!url) {
		return null;
	}
	if (/youtube\.com|youtu\.be/.test(url)) {
		return 'YouTube';
	}
	if (/vimeo\.com/.test(url)) {
		return 'Vimeo';
	}
	if (url.startsWith('/')) {
		return 'Local';
	}
	return null;
};

const TYPE_STYLES = {
	YouTube: { backgroundColor: '#cc0000', color: '#fff' },
	Vimeo: { backgroundColor: '#1ab7ea', color: '#fff' },
	Local: { backgroundColor: '#2e7d32', color: '#fff' },
};

/**
 * PanelVideoAssets
 *
 * Gutenberg sidebar panel for managing product video assets.
 * Accepts an unlimited number of video URLs — YouTube, Vimeo, or
 * local /wp-content paths. Local paths are resolved against the
 * WordPress home URL supplied by the server via wp_localize_script.
 *
 * Stored as JSON string in the product_video_assets meta field.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelVideoAssets = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const rawValue = meta?.product_video_assets ?? '[]';

	let videos = [];
	try {
		const parsed = JSON.parse(rawValue);
		videos = Array.isArray(parsed) ? parsed : [];
	} catch {
		videos = [];
	}

	const saveVideos = (updated) => {
		setMeta({ ...meta, product_video_assets: JSON.stringify(updated) });
	};

	const updateUrl = (index, value) => {
		const updated = videos.map((v, i) => (i === index ? { ...v, url: value } : v));
		saveVideos(updated);
	};

	const removeVideo = (index) => {
		saveVideos(videos.filter((_, i) => i !== index));
	};

	const addVideo = () => {
		saveVideos([...videos, { url: '' }]);
	};

	return (
		<PluginDocumentSettingPanel name="eternal-product-video-assets" title="Video Assets">
			{videos.map((video, index) => {
				const type = detectType(video.url);
				const resolvedUrl = type === 'Local' && HOME_URL ? HOME_URL + video.url : video.url;

				return (
					<div
						key={index}
						style={{
							border: '1px solid #ddd',
							borderRadius: '4px',
							padding: '12px',
							marginBottom: '12px',
						}}
					>
						<TextControl
							label={`Video ${index + 1} URL`}
							value={video.url}
							onChange={(value) => updateUrl(index, value)}
							placeholder="/wp-content/uploads/video.mp4 · YouTube · Vimeo"
						/>

						{type && (
							<span
								style={{
									display: 'inline-block',
									padding: '2px 8px',
									borderRadius: '3px',
									fontSize: '11px',
									fontWeight: '600',
									marginBottom: '6px',
									...TYPE_STYLES[type],
								}}
							>
								{type}
							</span>
						)}

						{type === 'Local' && resolvedUrl && (
							<p
								style={{
									fontSize: '11px',
									color: '#757575',
									margin: '0 0 6px',
									wordBreak: 'break-all',
								}}
							>
								{resolvedUrl}
							</p>
						)}

						<Button
							variant="secondary"
							isDestructive
							onClick={() => removeVideo(index)}
							style={{ display: 'block', marginTop: '4px' }}
						>
							Remove
						</Button>
					</div>
				);
			})}

			<Button variant="primary" onClick={addVideo}>
				Add Video
			</Button>
		</PluginDocumentSettingPanel>
	);
};

export default PanelVideoAssets;
