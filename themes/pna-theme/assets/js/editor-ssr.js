/**
 * Prévia no editor para blocos do tema gerados no servidor (render.php).
 * Sem etapa de build: usa as bibliotecas globais do WordPress.
 * Para um bloco novo usar esta prévia, acrescente o nome dele à lista
 * e use "editorScript": "pna-editor-ssr" no block.json.
 */
( function ( blocks, element, blockEditor, ServerSideRender ) {
	var el = element.createElement;
	var nomes = [ 'pna/filtro-pets', 'pna/pet-detalhes', 'pna/contador-adocoes' ];

	nomes.forEach( function ( nome ) {
		blocks.registerBlockType( nome, {
			edit: function () {
				return el( 'div', blockEditor.useBlockProps(), el( ServerSideRender, { block: nome } ) );
			},
			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.serverSideRender );
