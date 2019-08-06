const { registerBlockType } = wp.blocks;

import Save from './Components/Save';
import Edit from './Components/Edit';

registerBlockType( 'am/wp-block-boilerplate', {
	title: 'Basic Example',
	icon: 'smiley',
	category: 'layout',
	edit: () => <Edit />,
	save: () => <Save />,
});