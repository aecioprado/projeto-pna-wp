<?php
/**
 * Ícones SVG do tema.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retorna o SVG de um ícone de assets/icons/, pronto para imprimir.
 *
 * Os ícones são decorativos (aria-hidden): o texto acessível fica
 * no link ou botão que os envolve.
 *
 * @param string $nome Nome do arquivo sem extensão (ex.: 'sino').
 * @return string SVG ou string vazia se o ícone não existir.
 */
function pna_icone( $nome ) {
	static $cache = array();

	$nome = sanitize_file_name( $nome );

	if ( isset( $cache[ $nome ] ) ) {
		return $cache[ $nome ];
	}

	$arquivo = get_theme_file_path( 'assets/icons/' . $nome . '.svg' );

	if ( ! file_exists( $arquivo ) ) {
		$cache[ $nome ] = '';
		return '';
	}

	$svg = (string) file_get_contents( $arquivo ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$svg = str_replace( '<svg ', '<svg aria-hidden="true" focusable="false" class="pna-icone pna-icone--' . esc_attr( $nome ) . '" ', $svg );

	$cache[ $nome ] = $svg;
	return $svg;
}
