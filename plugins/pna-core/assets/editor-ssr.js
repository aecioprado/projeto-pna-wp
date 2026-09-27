/**
 * Prévia no editor dos blocos do plugin (gerados no servidor).
 * Sem etapa de build: usa as bibliotecas globais do WordPress.
 */
( function ( blocks, element, blockEditor, ServerSideRender ) {
	var el = element.createElement;

	[ 'pna-core/entrar', 'pna-core/cadastro', 'pna-core/perfil', 'pna-core/formulario' ].forEach( function ( nome ) {
		blocks.registerBlockType( nome, {
			edit: function ( props ) {
				return el(
					'div',
					blockEditor.useBlockProps(),
					el( ServerSideRender, { block: nome, attributes: props.attributes } )
				);
			},
			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender );
