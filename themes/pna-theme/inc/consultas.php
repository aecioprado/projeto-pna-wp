<?php
/**
 * Quantidade de itens por página nas listas do site.
 *
 * Fica no tema porque depende do layout: a lista de postagens tem
 * 2 colunas e a galeria de pets tem 4.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Postagens: 6 por página (3 linhas de 2). Pets: 12 por página (3 linhas de 4).
 *
 * @param WP_Query $query Consulta.
 */
function pna_itens_por_pagina( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_home() ) {
		$query->set( 'posts_per_page', 6 );
	} elseif ( $query->is_post_type_archive( 'pet' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'pna_itens_por_pagina' );
