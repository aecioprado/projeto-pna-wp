<?php
/**
 * Estilos de bloco do PNA.
 *
 * Cada estilo aparece no editor (painel do bloco → Estilos) e aplica
 * a classe "is-style-{nome}". O visual de cada um está nos arquivos
 * de assets/css/componentes/.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra os estilos de bloco.
 */
function pna_register_block_styles() {
	$estilos = array(
		'core/group'               => array(
			'pna-card'           => __( 'Card PNA', 'pna' ),
			'pna-card-postagem'  => __( 'Card de postagem', 'pna' ),
			'pna-card-doacao'    => __( 'Card de doação', 'pna' ),
			'pna-destaque'       => __( 'Caixa de destaque', 'pna' ),
		),
		'core/heading'             => array(
			'pna-titulo-secao' => __( 'Título de seção', 'pna' ),
		),
		'core/paragraph'           => array(
			'pna-link-seta' => __( 'Link com seta', 'pna' ),
			'pna-numero'    => __( 'Número em destaque', 'pna' ),
		),
		'core/button'              => array(
			'pna-atalho' => __( 'Atalho', 'pna' ),
		),
		'core/image'               => array(
			'pna-foto' => __( 'Foto com moldura', 'pna' ),
		),
		'core/post-featured-image' => array(
			'pna-foto' => __( 'Foto com moldura', 'pna' ),
		),
	);

	foreach ( $estilos as $bloco => $lista ) {
		foreach ( $lista as $nome => $rotulo ) {
			register_block_style(
				$bloco,
				array(
					'name'  => $nome,
					'label' => $rotulo,
				)
			);
		}
	}
}
add_action( 'init', 'pna_register_block_styles' );
