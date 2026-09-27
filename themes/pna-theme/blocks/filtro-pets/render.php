<?php
/**
 * Filtro lateral da galeria de pets (Figma: "FILTRO").
 *
 * Envia os filtros pela URL (/pets/?idade=filhote&sexo=femea), que o
 * WordPress entende sozinho graças às taxonomias do plugin pna-core.
 * Funciona sem JavaScript.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

if ( ! pna_tem_plugin_core() ) {
	return;
}

$pna_caracteristicas = pna_core_caracteristicas();
$pna_url_galeria     = get_post_type_archive_link( 'pet' );

/**
 * Valor atual de um filtro na URL.
 *
 * @param string $variavel Nome na URL.
 * @return string
 */
$pna_valor = static function ( $variavel ) {
	return sanitize_key( (string) get_query_var( $variavel ) );
};

// Listas de opções (radio): idade e porte, com "Indiferente".
$pna_listas = array( 'pna_idade', 'pna_porte' );
// Botões com ícone: espécie e sexo.
$pna_icones = array(
	'pna_especie' => array(
		'gato'     => 'especie-gato',
		'cachorro' => 'especie-cachorro',
	),
	'pna_sexo'    => array(
		'macho' => 'sexo-macho',
		'femea' => 'sexo-femea',
	),
);
?>
<form <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-filtro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> action="<?php echo esc_url( $pna_url_galeria ); ?>" method="get" role="search" aria-label="<?php esc_attr_e( 'Filtrar pets', 'pna' ); ?>">
	<p class="pna-filtro__titulo"><?php esc_html_e( 'Filtro', 'pna' ); ?></p>

	<?php foreach ( $pna_listas as $pna_tax ) : ?>
		<?php
		$pna_dados = $pna_caracteristicas[ $pna_tax ];
		$pna_atual = $pna_valor( $pna_dados['variavel'] );
		$pna_termos = get_terms(
			array(
				'taxonomy'   => $pna_tax,
				'hide_empty' => false,
			)
		);
		?>
		<fieldset class="pna-filtro__grupo">
			<legend class="pna-filtro__legenda"><?php echo esc_html( $pna_dados['rotulo'] ); ?></legend>
			<div class="pna-filtro__opcoes">
				<?php if ( ! is_wp_error( $pna_termos ) ) : ?>
					<?php foreach ( $pna_termos as $pna_termo ) : ?>
						<?php
						if ( 'desconhecida' === $pna_termo->slug ) {
							continue; // Não faz sentido filtrar por "idade desconhecida".
						}
						?>
						<label class="pna-filtro__opcao"><input type="radio" name="<?php echo esc_attr( $pna_dados['variavel'] ); ?>" value="<?php echo esc_attr( $pna_termo->slug ); ?>" <?php checked( $pna_atual, $pna_termo->slug ); ?>> <?php echo esc_html( $pna_termo->name ); ?></label>
					<?php endforeach; ?>
				<?php endif; ?>
				<label class="pna-filtro__opcao"><input type="radio" name="<?php echo esc_attr( $pna_dados['variavel'] ); ?>" value="" <?php checked( $pna_atual, '' ); ?>> <?php esc_html_e( 'Indiferente', 'pna' ); ?></label>
			</div>
		</fieldset>
	<?php endforeach; ?>

	<?php foreach ( $pna_icones as $pna_tax => $pna_mapa ) : ?>
		<?php
		$pna_dados = $pna_caracteristicas[ $pna_tax ];
		$pna_atual = $pna_valor( $pna_dados['variavel'] );
		?>
		<fieldset class="pna-filtro__grupo">
			<legend class="pna-filtro__legenda"><?php echo esc_html( $pna_dados['rotulo'] ); ?></legend>
			<div class="pna-filtro__icones">
				<?php foreach ( $pna_mapa as $pna_slug => $pna_icone ) : ?>
					<?php $pna_termo = get_term_by( 'slug', $pna_slug, $pna_tax ); ?>
					<label class="pna-filtro__icone" title="<?php echo esc_attr( $pna_termo ? $pna_termo->name : $pna_slug ); ?>">
						<input type="radio" name="<?php echo esc_attr( $pna_dados['variavel'] ); ?>" value="<?php echo esc_attr( $pna_slug ); ?>" <?php checked( $pna_atual, $pna_slug ); ?>>
						<?php echo pna_icone( $pna_icone ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="pna-so-leitor"><?php echo esc_html( $pna_termo ? $pna_termo->name : $pna_slug ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>
	<?php endforeach; ?>

	<a class="pna-filtro__limpar" href="<?php echo esc_url( $pna_url_galeria ); ?>"><?php esc_html_e( 'Limpar filtro', 'pna' ); ?></a>
	<button type="submit" class="wp-element-button pna-filtro__enviar"><?php esc_html_e( 'Filtrar', 'pna' ); ?></button>
</form>
