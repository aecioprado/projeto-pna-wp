<?php
/**
 * Title: Contador de adoções
 * Slug: pna/contador-adocoes
 * Categories: pna
 * Keywords: adoções, contador, destaque
 * Description: Caixa "Contagem de adoções" com o botão "Adote um pet". Os números ainda são fixos; o plugin pna-core vai torná-los automáticos.
 *
 * @package PNA
 */

?>
<!-- wp:group {"className":"pna-contador","style":{"dimensions":{"minHeight":""},"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group pna-contador">
	<!-- wp:group {"className":"is-style-pna-destaque","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
	<div class="wp-block-group is-style-pna-destaque">
		<!-- wp:heading -->
		<h2 class="wp-block-heading"><?php esc_html_e( 'Contagem de adoções', 'pna' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"className":"is-style-pna-numero"} -->
			<p class="is-style-pna-numero">00</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"textColor":"primary-text","fontSize":"x-large","style":{"typography":{"fontWeight":"800"}}} -->
			<p class="has-primary-text-color has-text-color has-x-large-font-size" style="font-weight:800"><?php esc_html_e( 'Hoje', 'pna' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:paragraph {"className":"pna-destaque__rodape"} -->
		<p class="pna-destaque__rodape"><?php esc_html_e( '000 adoções desde o início', 'pna' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"stretch"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"width":100} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/pets/' ) ); ?>"><?php esc_html_e( 'Adote um pet', 'pna' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
