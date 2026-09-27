<?php
/**
 * Contador de adoções (Figma: "Contagem de adoções · 00 Hoje · 000 adoções desde o início").
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

$pna_hoje  = pna_tem_plugin_core() ? pna_core_contar_adocoes( 'hoje' ) : 0;
$pna_total = pna_tem_plugin_core() ? pna_core_contar_adocoes( 'total' ) : 0;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'is-style-pna-destaque pna-contador__caixa' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<h2 class="wp-block-heading"><?php esc_html_e( 'Contagem de adoções', 'pna' ); ?></h2>
	<p class="pna-contador__hoje">
		<span class="is-style-pna-numero"><?php echo esc_html( str_pad( (string) $pna_hoje, 2, '0', STR_PAD_LEFT ) ); ?></span>
		<span class="pna-contador__rotulo"><?php esc_html_e( 'Hoje', 'pna' ); ?></span>
	</p>
	<p class="pna-destaque__rodape">
		<?php
		/* translators: %s: número de adoções. */
		echo esc_html( sprintf( _n( '%s adoção desde o início', '%s adoções desde o início', $pna_total, 'pna' ), str_pad( (string) $pna_total, 3, '0', STR_PAD_LEFT ) ) );
		?>
	</p>
</div>
