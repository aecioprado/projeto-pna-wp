<?php
/**
 * Title: Contador de adoções
 * Slug: pna/contador-adocoes
 * Categories: pna
 * Keywords: adoções, contador, destaque
 * Description: Caixa "Contagem de adoções" com os números reais e o botão "Adote um pet".
 *
 * @package PNA
 */

?>
<!-- wp:group {"className":"pna-contador","style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group pna-contador">
	<!-- wp:pna/contador-adocoes /-->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"stretch"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"width":100} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/pets/' ) ); ?>"><?php esc_html_e( 'Adote um pet', 'pna' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
