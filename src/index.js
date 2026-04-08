import { registerPlugin } from '@wordpress/plugins';
import { useSelect } from '@wordpress/data';
import PanelBuyBox from './components/PanelBuyBox';
import PanelIngredientCards from './components/PanelIngredientCards';

const EternalProductMetaPlugin = () => {
	const postType = useSelect((select) => select('core/editor').getCurrentPostType(), []);

	if (postType !== 'product') {
		return null;
	}

	return (
		<>
			<PanelBuyBox />
			<PanelIngredientCards />
		</>
	);
};

registerPlugin('eternal-product-meta', {
	render: EternalProductMetaPlugin,
});
