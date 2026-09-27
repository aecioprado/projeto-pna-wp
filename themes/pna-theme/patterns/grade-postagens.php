<?php
/**
 * Title: Grade de postagens
 * Slug: pna/grade-postagens
 * Categories: pna
 * Keywords: postagens, blog, cards
 * Description: Título de seção, as 4 postagens mais recentes em cards (2 colunas) e o link "Ver mais".
 *
 * @package PNA
 */

?>
<!-- wp:group {"className":"pna-secao-postagens","layout":{"type":"constrained"}} -->
<div class="wp-block-group pna-secao-postagens">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao"><?php esc_html_e( 'Postagens', 'pna' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:query {"queryId":20,"query":{"perPage":4,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false}} -->
	<div class="wp-block-query">
		<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|60"}},"layout":{"type":"grid","columnCount":2}} -->
			<!-- wp:group {"className":"is-style-pna-card-postagem","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"stretch"}} -->
			<div class="wp-block-group is-style-pna-card-postagem">
				<!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium"} /-->

				<!-- wp:group {"className":"pna-card-postagem__texto","layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group pna-card-postagem__texto">
					<!-- wp:post-title {"level":3,"isLink":true} /-->
					<!-- wp:post-excerpt {"excerptLength":22} /-->
					<!-- wp:read-more {"content":"<?php esc_attr_e( 'Ler mais', 'pna' ); ?>"} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Ainda não há postagens.', 'pna' ); ?></p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:query -->

	<!-- wp:paragraph {"align":"right","className":"is-style-pna-link-seta"} -->
	<p class="has-text-align-right is-style-pna-link-seta"><a href="<?php echo esc_url( home_url( '/postagens/' ) ); ?>"><?php esc_html_e( 'Ver mais', 'pna' ); ?></a></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
