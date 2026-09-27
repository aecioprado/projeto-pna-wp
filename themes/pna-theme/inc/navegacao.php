<?php
/**
 * Destaque do item ativo no menu principal.
 *
 * O bloco de navegação só marca o item atual quando o link aponta
 * para um post específico. Como o menu do PNA usa endereços
 * (/postagens/, /pets/...), este filtro compara o endereço do link
 * com a página atual e marca o item correspondente.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retorna o caminho da requisição atual, sem barras nas pontas.
 *
 * @return string
 */
function pna_caminho_atual() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	return trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
}

/**
 * Marca o item do menu cujo endereço corresponde à página atual.
 *
 * @param string $html  HTML do item renderizado.
 * @param array  $block Dados do bloco.
 * @return string
 */
function pna_marcar_item_atual( $html, $block ) {
	$url = isset( $block['attrs']['url'] ) ? $block['attrs']['url'] : '';

	if ( '' === $url || str_contains( $html, 'current-menu-item' ) ) {
		return $html;
	}

	$caminho_link  = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
	$caminho_atual = pna_caminho_atual();

	// Link da página inicial ("/"): só é o atual na própria página inicial.
	if ( '' === $caminho_link ) {
		$e_atual = '' === $caminho_atual;
	} else {
		$e_atual = $caminho_atual === $caminho_link || str_starts_with( $caminho_atual . '/', $caminho_link . '/' );
	}

	if ( ! $e_atual ) {
		return $html;
	}

	$tags = new WP_HTML_Tag_Processor( $html );

	if ( $tags->next_tag( 'li' ) ) {
		$tags->add_class( 'current-menu-item' );
	}
	if ( $tags->next_tag( 'a' ) ) {
		$tags->set_attribute( 'aria-current', 'page' );
	}

	return $tags->get_updated_html();
}
add_filter( 'render_block_core/navigation-link', 'pna_marcar_item_atual', 10, 2 );
