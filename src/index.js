import { registerPlugin } from '@wordpress/plugins';
import { useSelect } from '@wordpress/data';
import PanelBuyBox from './components/PanelBuyBox';
import PanelIngredientCards from './components/PanelIngredientCards';
import PanelVideoAssets from './components/PanelVideoAssets';

const EternalProductMetaPlugin = () => {
	const postType = useSelect((select) => select('core/editor').getCurrentPostType(), []);

	if (postType !== 'product') {
		return null;
	}

	return (
		<>
			<PanelBuyBox />
			<PanelIngredientCards />
			<PanelVideoAssets />
		</>
	);
};

registerPlugin('eternal-product-meta', {
	render: EternalProductMetaPlugin,
});
