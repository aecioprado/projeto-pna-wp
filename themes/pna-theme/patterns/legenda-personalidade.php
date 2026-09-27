<?php
/**
 * Title: Legenda de personalidade
 * Slug: pna/legenda-personalidade
 * Categories: pna
 * Inserter: no
 * Description: Legenda dos ícones de personalidade (Sociável, Brincalhão, Carinhoso), usada na galeria de pets.
 *
 * @package PNA
 */

?>
<!-- wp:html -->
<ul class="pna-legenda-tracos" aria-label="<?php esc_attr_e( 'Legenda dos ícones de personalidade', 'pna' ); ?>">
	<li><?php echo pna_icone( 'coracoes' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php esc_html_e( 'Sociável', 'pna' ); ?></li>
	<li><?php echo pna_icone( 'bola' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php esc_html_e( 'Brincalhão', 'pna' ); ?></li>
	<li><?php echo pna_icone( 'pata' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php esc_html_e( 'Carinhoso', 'pna' ); ?></li>
</ul>
<!-- /wp:html -->
