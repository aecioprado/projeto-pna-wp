<?php
/**
 * Funções do tema PNA.
 *
 * Regra do projeto: o tema cuida só do VISUAL. Regras de negócio
 * (pets, adoções, papéis) ficam no plugin pna-core.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

require_once get_theme_file_path( 'inc/icones.php' );
require_once get_theme_file_path( 'inc/navegacao.php' );
require_once get_theme_file_path( 'inc/estilos-de-bloco.php' );

/**
 * Lista os arquivos CSS do tema, na ordem em que devem ser carregados:
 * primeiro base.css, depois cada componente em ordem alfabética.
 *
 * Para criar um componente novo, basta adicionar um arquivo em
 * assets/css/componentes/: ele é carregado automaticamente.
 *
 * @return string[] Caminhos relativos à pasta do tema.
 */
function pna_arquivos_css() {
	$arquivos = array( 'assets/css/base.css' );

	$componentes = glob( get_theme_file_path( 'assets/css/componentes/*.css' ) );
	sort( $componentes );

	foreach ( $componentes as $caminho ) {
		$arquivos[] = 'assets/css/componentes/' . basename( $caminho );
	}

	return $arquivos;
}

/**
 * Carrega o CSS do tema no site.
 */
function pna_enqueue_styles() {
	foreach ( pna_arquivos_css() as $relativo ) {
		$caminho = get_theme_file_path( $relativo );

		wp_enqueue_style(
			'pna-' . basename( $relativo, '.css' ),
			get_theme_file_uri( $relativo ),
			array(),
			(string) filemtime( $caminho )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'pna_enqueue_styles' );

/**
 * Aplica o mesmo CSS dentro do editor, para os dois ficarem iguais.
 */
function pna_editor_styles() {
	add_editor_style( pna_arquivos_css() );
}
add_action( 'after_setup_theme', 'pna_editor_styles' );

/**
 * Registra os blocos próprios do tema: cada pasta em blocks/ com um
 * block.json vira um bloco. Blocos novos são registrados sozinhos.
 *
 * O script "pna-editor-ssr" mostra no editor a prévia gerada pelo
 * render.php de blocos que não têm editor próprio.
 */
function pna_register_blocks() {
	wp_register_script(
		'pna-editor-ssr',
		get_theme_file_uri( 'assets/js/editor-ssr.js' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render' ),
		(string) filemtime( get_theme_file_path( 'assets/js/editor-ssr.js' ) ),
		true
	);

	foreach ( glob( get_theme_file_path( 'blocks/*/block.json' ) ) as $arquivo ) {
		register_block_type( dirname( $arquivo ) );
	}
}
add_action( 'init', 'pna_register_blocks' );

/**
 * O plugin pna-core está ativo? Blocos que mostram pets dependem dele.
 *
 * @return bool
 */
function pna_tem_plugin_core() {
	return function_exists( 'pna_core_obter_status_pet' );
}

/**
 * Categoria "PNA" na lista de padrões do editor.
 */
function pna_register_pattern_category() {
	register_block_pattern_category( 'pna', array( 'label' => __( 'PNA', 'pna' ) ) );
}
add_action( 'init', 'pna_register_pattern_category' );
