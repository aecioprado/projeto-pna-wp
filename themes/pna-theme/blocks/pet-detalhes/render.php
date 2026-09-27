<?php
/**
 * Detalhes do pet (Figma: página "Rhaenyra").
 *
 * Coluna esquerda: foto e botões. Coluna direita: nome, espécie, sexo,
 * descrição, porte, idade, saúde, personalidade e padrinhos.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

$pna_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();

if ( ! $pna_id || 'pet' !== get_post_type( $pna_id ) || ! pna_tem_plugin_core() ) {
	printf( '<p %s>%s</p>', get_block_wrapper_attributes(), esc_html__( 'Os detalhes aparecem na página de cada pet.', 'pna' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$pna_nome    = get_the_title( $pna_id );
$pna_status  = pna_core_obter_status_pet( $pna_id );
$pna_termo   = static function ( $taxonomia ) use ( $pna_id ) {
	$termos = get_the_terms( $pna_id, $taxonomia );
	return ( $termos && ! is_wp_error( $termos ) ) ? $termos[0] : null;
};
$pna_especie = $pna_termo( 'pna_especie' );
$pna_sexo    = $pna_termo( 'pna_sexo' );
$pna_porte   = $pna_termo( 'pna_porte' );
$pna_idade   = $pna_termo( 'pna_idade' );
$pna_saude   = pna_core_opcoes_saude();
$pna_femea   = $pna_sexo && 'femea' === $pna_sexo->slug;
$pna_castr   = (string) get_post_meta( $pna_id, '_pna_castrado', true );
$pna_vacina  = (string) get_post_meta( $pna_id, '_pna_vacinado', true );
$pna_padr    = pna_core_contar_padrinhos( $pna_id );
$pna_icones_tracos = array(
	'sociavel'   => 'coracoes',
	'brincalhao' => 'bola',
	'carinhoso'  => 'pata',
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-pet pna-pet--' . $pna_status ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="pna-pet__midia">
		<?php if ( has_post_thumbnail( $pna_id ) ) : ?>
			<?php
			echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$pna_id,
				'large',
				array(
					'class' => 'pna-pet__foto',
					/* translators: %s: nome do pet. */
					'alt'   => sprintf( __( 'Foto de %s', 'pna' ), $pna_nome ),
				)
			);
			?>
		<?php else : ?>
			<span class="pna-pet__foto" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: nome do pet. */ __( '%s ainda não tem foto', 'pna' ), $pna_nome ) ); ?>"></span>
		<?php endif; ?>

		<div class="pna-pet__acoes">
			<?php if ( 'disponivel' === $pna_status ) : ?>
				<div class="wp-block-button pna-pet__botao"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( pna_core_url_formulario( 'adocao', $pna_id ) ); ?>"><?php esc_html_e( 'Adotar', 'pna' ); ?></a></div>
			<?php elseif ( 'em_processo' === $pna_status ) : ?>
				<p class="pna-pet__aviso"><?php echo esc_html( sprintf( /* translators: %s: nome do pet. */ __( '%s já está em processo de adoção.', 'pna' ), $pna_nome ) ); ?></p>
			<?php else : ?>
				<p class="pna-pet__aviso"><?php echo esc_html( sprintf( /* translators: %s: nome do pet. */ __( '%s já encontrou um lar!', 'pna' ), $pna_nome ) ); ?></p>
			<?php endif; ?>

			<?php if ( 'adotado' !== $pna_status ) : ?>
				<div class="wp-block-button is-style-outline pna-pet__botao"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( pna_core_url_formulario( 'apadrinhamento', $pna_id ) ); ?>"><?php esc_html_e( 'Apadrinhar', 'pna' ); ?></a></div>
			<?php endif; ?>
		</div>
	</div>

	<div class="pna-pet__info">
		<div class="pna-pet__cabecalho">
			<h1 class="pna-pet__nome"><?php echo esc_html( $pna_nome ); ?></h1>
			<?php if ( $pna_especie ) : ?>
				<span class="pna-pet__marca"><?php echo pna_icone( 'especie-' . $pna_especie->slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="pna-so-leitor"><?php echo esc_html( $pna_especie->name ); ?></span></span>
			<?php endif; ?>
			<?php if ( $pna_sexo ) : ?>
				<span class="pna-pet__marca"><?php echo pna_icone( 'sexo-' . $pna_sexo->slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="pna-so-leitor"><?php echo esc_html( $pna_sexo->name ); ?></span></span>
			<?php endif; ?>
		</div>

		<div class="pna-pet__descricao">
			<?php echo wp_kses_post( wpautop( get_post_field( 'post_content', $pna_id ) ) ); ?>
		</div>

		<dl class="pna-pet__dados">
			<div class="pna-pet__dado">
				<dt><?php esc_html_e( 'Porte', 'pna' ); ?> <?php echo pna_icone( 'pata' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></dt>
				<dd><?php echo esc_html( $pna_porte ? $pna_porte->name : __( 'Sem informação', 'pna' ) ); ?></dd>
			</div>
			<div class="pna-pet__dado">
				<dt><?php esc_html_e( 'Idade', 'pna' ); ?> <?php echo pna_icone( 'bolo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></dt>
				<dd><?php echo esc_html( $pna_idade ? $pna_idade->name : __( 'Sem informação', 'pna' ) ); ?></dd>
			</div>
			<div class="pna-pet__dado">
				<dt><?php esc_html_e( 'Saúde', 'pna' ); ?> <?php echo pna_icone( 'saude' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></dt>
				<dd>
					<?php echo esc_html( $pna_femea ? __( 'Castrada:', 'pna' ) : __( 'Castrado:', 'pna' ) ); ?>
					<strong><?php echo esc_html( $pna_saude[ $pna_castr ] ?? $pna_saude['sem_info'] ); ?></strong><br>
					<?php esc_html_e( 'Vacinas:', 'pna' ); ?>
					<strong><?php echo esc_html( $pna_saude[ $pna_vacina ] ?? $pna_saude['sem_info'] ); ?></strong>
				</dd>
			</div>
		</dl>

		<div class="pna-pet__rodape">
			<ul class="pna-pet__tracos">
				<?php foreach ( pna_core_tracos() as $pna_traco => $pna_rotulo ) : ?>
					<?php $pna_nivel = min( 3, max( 0, (int) get_post_meta( $pna_id, '_pna_' . $pna_traco, true ) ) ); ?>
					<li class="pna-pet__traco">
						<span class="pna-pet__nivel" aria-hidden="true">
							<?php for ( $pna_i = 1; $pna_i <= 3; $pna_i++ ) : ?>
								<span class="<?php echo $pna_i <= $pna_nivel ? 'is-ativo' : 'is-inativo'; ?>"><?php echo pna_icone( $pna_icones_tracos[ $pna_traco ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endfor; ?>
						</span>
						<span class="pna-pet__traco-nome"><?php echo esc_html( $pna_rotulo ); ?></span>
						<span class="pna-so-leitor"><?php echo esc_html( sprintf( /* translators: %d: nível de 0 a 3. */ __( 'nível %d de 3', 'pna' ), $pna_nivel ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="is-style-pna-destaque pna-pet__padrinhos">
				<p class="pna-pet__padrinhos-titulo"><?php esc_html_e( 'Padrinhos:', 'pna' ); ?></p>
				<p class="is-style-pna-numero pna-pet__padrinhos-numero"><?php echo esc_html( str_pad( (string) $pna_padr, 2, '0', STR_PAD_LEFT ) ); ?></p>
			</div>
		</div>
	</div>
</div>
