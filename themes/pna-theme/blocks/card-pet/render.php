<?php
/**
 * Card de pet (estrutura em docs/componentes.md).
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

$pna_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();

if ( ! $pna_id || 'pet' !== get_post_type( $pna_id ) ) {
	return;
}

$pna_nome  = get_the_title( $pna_id );
$pna_sexo  = pna_tem_plugin_core() ? pna_core_caracteristica_do_pet( $pna_id, 'pna_sexo' ) : '';
$pna_rotulo_sexo = array(
	'macho' => __( 'Macho', 'pna' ),
	'femea' => __( 'Fêmea', 'pna' ),
);
$pna_em_processo = pna_tem_plugin_core() && 'em_processo' === pna_core_obter_status_pet( $pna_id );
?>
<article <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-card-pet' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<a class="pna-card-pet__link" href="<?php echo esc_url( get_permalink( $pna_id ) ); ?>">
		<?php if ( has_post_thumbnail( $pna_id ) ) : ?>
			<?php
			echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$pna_id,
				'medium',
				array(
					'class' => 'pna-card-pet__foto',
					/* translators: %s: nome do pet. */
					'alt'   => sprintf( __( 'Foto de %s', 'pna' ), $pna_nome ),
				)
			);
			?>
		<?php else : ?>
			<span class="pna-card-pet__foto" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: nome do pet. */ __( '%s ainda não tem foto', 'pna' ), $pna_nome ) ); ?>"></span>
		<?php endif; ?>
		<?php if ( $pna_em_processo ) : ?>
			<span class="pna-card-pet__selo"><?php esc_html_e( 'Em processo de adoção', 'pna' ); ?></span>
		<?php endif; ?>
		<span class="pna-card-pet__rodape">
			<span class="pna-card-pet__nome"><?php echo esc_html( $pna_nome ); ?></span>
			<?php if ( isset( $pna_rotulo_sexo[ $pna_sexo ] ) ) : ?>
				<span class="pna-card-pet__sexo"><?php echo pna_icone( 'sexo-' . $pna_sexo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="pna-so-leitor"><?php echo esc_html( $pna_rotulo_sexo[ $pna_sexo ] ); ?></span></span>
			<?php endif; ?>
		</span>
	</a>
</article>
