/**
 * Editor do "Card de pet": mostra um card de exemplo.
 * No site, o render.php usa os dados reais de cada pet.
 */
( function ( blocks, element, blockEditor ) {
	var el = element.createElement;

	blocks.registerBlockType( 'pna/card-pet', {
		edit: function () {
			return el(
				'div',
				blockEditor.useBlockProps( { className: 'pna-card-pet' } ),
				el( 'span', { className: 'pna-card-pet__link' },
					el( 'span', { className: 'pna-card-pet__foto' } ),
					el( 'span', { className: 'pna-card-pet__rodape' },
						el( 'span', { className: 'pna-card-pet__nome' }, 'Nome do pet' )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
