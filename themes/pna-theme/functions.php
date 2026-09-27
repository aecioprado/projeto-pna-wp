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

/**
 * Carrega o CSS de componentes (o que o theme.json não cobre)
 * no site e dentro do editor, para os dois ficarem iguais.
 */
function pna_enqueue_styles() {
	$file = get_theme_file_path( 'assets/css/components.css' );

	if ( file_exists( $file ) ) {
		wp_enqueue_style(
			'pna-components',
			get_theme_file_uri( 'assets/css/components.css' ),
			array(),
			(string) filemtime( $file )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'pna_enqueue_styles' );

/**
 * Aplica o mesmo CSS dentro do editor de blocos.
 */
function pna_editor_styles() {
	add_editor_style( 'assets/css/components.css' );
}
add_action( 'after_setup_theme', 'pna_editor_styles' );

/**
 * Registra os blocos próprios do tema (pasta blocks/).
 */
function pna_register_blocks() {
	register_block_type( get_theme_file_path( 'blocks/header-acoes' ) );
	register_block_type( get_theme_file_path( 'blocks/breadcrumb' ) );
}
add_action( 'init', 'pna_register_blocks' );
