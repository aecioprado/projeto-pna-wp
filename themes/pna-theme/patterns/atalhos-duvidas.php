<?php
/**
 * Title: Atalhos de dúvidas
 * Slug: pna/atalhos-duvidas
 * Categories: pna
 * Keywords: dúvidas, perguntas, faq, atalhos
 * Description: Título de seção e quatro atalhos para as principais dúvidas.
 *
 * @package PNA
 */

$pna_atalhos = array(
	'quem-somos'   => __( 'Quem somos?', 'pna' ),
	'como-adotar'  => __( 'Como realizar a adoção?', 'pna' ),
	'buscar-pet'   => __( 'Como buscar o pet escolhido?', 'pna' ),
	'maus-tratos'  => __( 'O que é maus-tratos e como combater?', 'pna' ),
);
?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao"><?php esc_html_e( 'Dúvidas', 'pna' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"space-between"}} -->
	<div class="wp-block-buttons">
		<?php foreach ( $pna_atalhos as $pna_ancora => $pna_texto ) : ?>
		<!-- wp:button {"className":"is-style-pna-atalho"} -->
		<div class="wp-block-button is-style-pna-atalho"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/duvidas/#' . $pna_ancora ) ); ?>"><?php echo esc_html( $pna_texto ); ?></a></div>
		<!-- /wp:button -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
