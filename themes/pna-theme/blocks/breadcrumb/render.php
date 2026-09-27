<?php
/**
 * Trilha de navegação (breadcrumb).
 *
 * Exemplos:
 *   Página inicial > Pets para adoção > Rhaenyra
 *   Página inicial > Postagens > Gravidez felina
 *
 * O plugin pna-core pode acrescentar etapas (ex.: "Adotar") pelo
 * filtro 'pna_breadcrumb_itens'.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

if ( is_front_page() ) {
	return;
}

$pna_itens = array(
	array(
		'rotulo' => __( 'Página inicial', 'pna' ),
		'url'    => home_url( '/' ),
	),
);

$pna_pagina_posts = (int) get_option( 'page_for_posts' );

if ( is_home() ) {
	$pna_itens[] = array( 'rotulo' => $pna_pagina_posts ? get_the_title( $pna_pagina_posts ) : __( 'Postagens', 'pna' ) );
} elseif ( is_singular( 'post' ) ) {
	if ( $pna_pagina_posts ) {
		$pna_itens[] = array(
			'rotulo' => get_the_title( $pna_pagina_posts ),
			'url'    => get_permalink( $pna_pagina_posts ),
		);
	}
	$pna_itens[] = array( 'rotulo' => get_the_title() );
} elseif ( is_page() ) {
	foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $pna_ancestral ) {
		$pna_itens[] = array(
			'rotulo' => get_the_title( $pna_ancestral ),
			'url'    => get_permalink( $pna_ancestral ),
		);
	}
	$pna_itens[] = array( 'rotulo' => get_the_title() );
} elseif ( is_singular() ) {
	$pna_tipo = get_post_type_object( get_post_type() );
	if ( $pna_tipo && $pna_tipo->has_archive ) {
		$pna_itens[] = array(
			'rotulo' => $pna_tipo->labels->name,
			'url'    => get_post_type_archive_link( $pna_tipo->name ),
		);
	}
	$pna_itens[] = array( 'rotulo' => get_the_title() );
} elseif ( is_post_type_archive() ) {
	$pna_itens[] = array( 'rotulo' => post_type_archive_title( '', false ) );
} elseif ( is_category() || is_tag() || is_tax() ) {
	$pna_itens[] = array( 'rotulo' => single_term_title( '', false ) );
} elseif ( is_search() ) {
	$pna_itens[] = array( 'rotulo' => __( 'Resultados da busca', 'pna' ) );
} elseif ( is_404() ) {
	$pna_itens[] = array( 'rotulo' => __( 'Página não encontrada', 'pna' ) );
}

$pna_itens = (array) apply_filters( 'pna_breadcrumb_itens', $pna_itens );

if ( count( $pna_itens ) < 2 ) {
	return;
}

$pna_ultimo = count( $pna_itens ) - 1;
?>
<nav <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-breadcrumb' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php esc_attr_e( 'Trilha de navegação', 'pna' ); ?>">
	<ol class="pna-breadcrumb__lista">
		<?php foreach ( $pna_itens as $pna_i => $pna_item ) : ?>
			<li class="pna-breadcrumb__item">
				<?php if ( $pna_i === $pna_ultimo || empty( $pna_item['url'] ) ) : ?>
					<span <?php echo $pna_i === $pna_ultimo ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $pna_item['rotulo'] ); ?></span>
				<?php else : ?>
					<a href="<?php echo esc_url( $pna_item['url'] ); ?>"><?php echo esc_html( $pna_item['rotulo'] ); ?></a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
