<?php
/**
 * Title: Pets para adoção (4 mais recentes)
 * Slug: pna/grade-pets
 * Categories: pna
 * Keywords: pets, adoção, cards
 * Description: Título de seção, os 4 pets mais recentes disponíveis e o link "Ver mais".
 *
 * @package PNA
 */

?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao"><?php esc_html_e( 'Pets para adoção', 'pna' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:query {"queryId":40,"query":{"perPage":4,"pages":0,"offset":0,"postType":"pet","order":"desc","orderBy":"date","inherit":false}} -->
	<div class="wp-block-query">
		<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|60"}},"layout":{"type":"grid","columnCount":4}} -->
			<!-- wp:pna/card-pet /-->
		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Em breve, novos pets para adoção.', 'pna' ); ?></p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:query -->

	<!-- wp:paragraph {"align":"right","className":"is-style-pna-link-seta"} -->
	<p class="has-text-align-right is-style-pna-link-seta"><a href="<?php echo esc_url( home_url( '/pets/' ) ); ?>"><?php esc_html_e( 'Ver mais', 'pna' ); ?></a></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
