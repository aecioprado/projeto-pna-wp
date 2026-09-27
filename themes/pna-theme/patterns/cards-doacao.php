<?php
/**
 * Title: Cards de doação
 * Slug: pna/cards-doacao
 * Categories: pna
 * Keywords: doações, doar, cards
 * Description: Título de seção e os três cards de doação (alimentos, itens e dinheiro).
 *
 * @package PNA
 */

?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao"><?php esc_html_e( 'Doações', 'pna' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|80"}},"layout":{"type":"grid","columnCount":3}} -->
	<div class="wp-block-group">
		<!-- wp:group {"className":"is-style-pna-card-doacao","layout":{"type":"default"}} -->
		<div class="wp-block-group is-style-pna-card-doacao">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/doacoes/#alimentos' ) ); ?>"><?php esc_html_e( 'Doe alimentos', 'pna' ); ?></a></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Para gatos e cachorros, jovens e adultos.', 'pna' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"is-style-pna-card-doacao","layout":{"type":"default"}} -->
		<div class="wp-block-group is-style-pna-card-doacao">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/doacoes/#itens' ) ); ?>"><?php esc_html_e( 'Doe itens de pets', 'pna' ); ?></a></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Um item usado pode se tornar um presente.', 'pna' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"is-style-pna-card-doacao","layout":{"type":"default"}} -->
		<div class="wp-block-group is-style-pna-card-doacao">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="<?php echo esc_url( home_url( '/doacoes/#dinheiro' ) ); ?>"><?php esc_html_e( 'Doe em dinheiro', 'pna' ); ?></a></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Nos ajude a cuidar da saúde dos pets.', 'pna' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
