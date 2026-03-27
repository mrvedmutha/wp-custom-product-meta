import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { Button, TextControl, TextareaControl } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';

/**
 * PanelIngredientCards
 *
 * Gutenberg sidebar panel for managing ingredient card entries.
 * Each card contains a name, description, and an optional image attachment.
 * The array of cards is stored as a JSON string in the product_ingredient_cards
 * meta field.
 *
 * @return {JSX.Element} The rendered sidebar panel.
 */
const PanelIngredientCards = () => {
	const [meta, setMeta] = useEntityProp('postType', 'product', 'meta');

	const rawValue = meta?.product_ingredient_cards ?? '[]';

	let cards = [];
	try {
		const parsed = JSON.parse(rawValue);
		cards = Array.isArray(parsed) ? parsed : [];
	} catch {
		cards = [];
	}

	const saveCards = (updatedCards) => {
		setMeta({ ...meta, product_ingredient_cards: JSON.stringify(updatedCards) });
	};

	const updateCard = (index, field, value) => {
		const updated = cards.map((card, i) => (i === index ? { ...card, [field]: value } : card));
		saveCards(updated);
	};

	const removeCard = (index) => {
		saveCards(cards.filter((_, i) => i !== index));
	};

	const addCard = () => {
		saveCards([...cards, { name: '', description: '', image_id: 0 }]);
	};

	return (
		<PluginDocumentSettingPanel name="eternal-product-ingredient-cards" title="Ingredient Cards">
			{cards.map((card, index) => (
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
						label="Ingredient Name"
						value={card.name}
						onChange={(value) => updateCard(index, 'name', value)}
					/>

					<TextareaControl
						label="Description"
						value={card.description}
						onChange={(value) => updateCard(index, 'description', value)}
					/>

					<MediaUploadCheck>
						<MediaUpload
							allowedTypes={['image']}
							value={card.image_id}
							onSelect={(media) => updateCard(index, 'image_id', media.id)}
							render={({ open }) =>
								card.image_id > 0 ? (
									<div style={{ marginBottom: '8px' }}>
										<img
											src={card.image_id}
											alt=""
											style={{
												display: 'block',
												maxWidth: '100%',
												marginBottom: '4px',
											}}
										/>
										<Button variant="secondary" onClick={open}>
											Change Image
										</Button>
									</div>
								) : (
									<Button variant="secondary" onClick={open}>
										Select Image
									</Button>
								)
							}
						/>
					</MediaUploadCheck>

					<Button
						variant="secondary"
						isDestructive
						onClick={() => removeCard(index)}
						style={{ marginTop: '8px' }}
					>
						Remove Card
					</Button>
				</div>
			))}

			<Button variant="primary" onClick={addCard}>
				Add Card
			</Button>
		</PluginDocumentSettingPanel>
	);
};

export default PanelIngredientCards;
