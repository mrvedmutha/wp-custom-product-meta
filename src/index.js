import { registerPlugin } from '@wordpress/plugins';
import { useSelect } from '@wordpress/data';
import PanelIdentity from './components/PanelIdentity';
import PanelFragrance from './components/PanelFragrance';
import PanelIngredients from './components/PanelIngredients';
import PanelHowToUse from './components/PanelHowToUse';
import PanelIngredientCards from './components/PanelIngredientCards';
import PanelEditorial from './components/PanelEditorial';

const EternalProductMetaPlugin = () => {
	const postType = useSelect((select) => select('core/editor').getCurrentPostType(), []);

	if (postType !== 'product') {
		return null;
	}

	return (
		<>
			<PanelIdentity />
			<PanelFragrance />
			<PanelIngredients />
			<PanelHowToUse />
			<PanelIngredientCards />
			<PanelEditorial />
		</>
	);
};

registerPlugin('eternal-product-meta', {
	render: EternalProductMetaPlugin,
});
