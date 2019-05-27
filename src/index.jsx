const { registerBlockType } = wp.blocks;
import './style.scss';
import './editor.scss';

registerBlockType( 'first-block/my-first-block', {
	title: 'Basic Example',
	icon: 'smiley',
	category: 'layout',
	edit: () => <div>Namaste!</div>,
	save: () => <div>Namaste!</div>,
} );
