<?php
/**
 * Elementos compartilhados das telas do painel.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Etiqueta visual de status para as listas do painel.
 *
 * @param string $status Chave do status.
 * @param string $rotulo Texto exibido.
 * @return string HTML.
 */
function pna_core_etiqueta_status( $status, $rotulo ) {
	return sprintf(
		'<span class="pna-etiqueta pna-etiqueta--%1$s">%2$s</span>',
		esc_attr( $status ),
		esc_html( $rotulo )
	);
}

/**
 * Estilos das telas do PNA no painel (etiquetas e quadros).
 */
function pna_core_estilos_admin() {
	$tela = get_current_screen();
	if ( ! $tela || ! in_array( $tela->post_type, array( 'pet', 'pna_solicitacao' ), true ) ) {
		return;
	}
	?>
	<style>
		.pna-etiqueta { display: inline-block; padding: 2px 10px; border-radius: 10px; background: #e1f3e2; color: #1f7a5e; font-weight: 600; white-space: nowrap; }
		.pna-etiqueta--enviada { background: #fcf0d4; color: #7a5200; }
		.pna-etiqueta--em_analise { background: #dfeefb; color: #0b4f8a; }
		.pna-etiqueta--recusada, .pna-etiqueta--cancelada, .pna-etiqueta--encerrada { background: #eee; color: #555; }
		.pna-etiqueta--adotado, .pna-etiqueta--concluida { background: #1f7a5e; color: #fff; }
		.pna-quadro-campos { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 24px; }
		.pna-quadro-campos label { display: block; font-weight: 600; margin-bottom: 4px; }
		.pna-quadro-campos select { width: 100%; }
		.pna-quadro-dados th { width: 35%; text-align: left; vertical-align: top; padding: 6px 12px 6px 0; }
		.pna-quadro-dados td { padding: 6px 0; white-space: pre-line; }
		.pna-historico { margin: 0; }
		.pna-historico li { margin-bottom: 6px; }
		.column-pna_foto { width: 64px; }
		.column-pna_foto img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
	</style>
	<?php
}
add_action( 'admin_head', 'pna_core_estilos_admin' );
