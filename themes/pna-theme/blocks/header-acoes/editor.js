/**
 * Editor do bloco "Ações do cabeçalho".
 * Sem etapa de build: usa as bibliotecas globais do WordPress.
 * A prévia no editor é gerada pelo próprio render.php.
 */
( function ( blocks, element, blockEditor, ServerSideRender ) {
	var el = element.createElement;

	blocks.registerBlockType( 'pna/header-acoes', {
		edit: function () {
			return el(
				'div',
				blockEditor.useBlockProps(),
				el( ServerSideRender, { block: 'pna/header-acoes' } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender );
