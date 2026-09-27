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

/**
 * Carrega o CSS de componentes (o que o theme.json não cobre).
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
