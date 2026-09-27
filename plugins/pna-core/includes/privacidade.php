<?php
/**
 * LGPD: integra os dados do PNA às ferramentas nativas do WordPress
 * (Ferramentas → Exportar dados pessoais / Apagar dados pessoais).
 *
 * - Exportação: perfil (telefone, CEP, nascimento…) e todas as solicitações.
 * - Exclusão: apaga os dados do perfil e anonimiza as respostas das
 *   solicitações, mantendo o registro (pet, tipo, status, datas) para o
 *   histórico do projeto.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra o exportador.
 *
 * @param array $exportadores Exportadores.
 * @return array
 */
function pna_core_registrar_exportador( $exportadores ) {
	$exportadores['pna-core'] = array(
		'exporter_friendly_name' => __( 'PNA – perfil e solicitações', 'pna' ),
		'callback'               => 'pna_core_exportar_dados',
	);
	return $exportadores;
}
add_filter( 'wp_privacy_personal_data_exporters', 'pna_core_registrar_exportador' );

/**
 * Registra o apagador.
 *
 * @param array $apagadores Apagadores.
 * @return array
 */
function pna_core_registrar_apagador( $apagadores ) {
	$apagadores['pna-core'] = array(
		'eraser_friendly_name' => __( 'PNA – perfil e solicitações', 'pna' ),
		'callback'             => 'pna_core_apagar_dados',
	);
	return $apagadores;
}
add_filter( 'wp_privacy_personal_data_erasers', 'pna_core_registrar_apagador' );

/**
 * IDs das solicitações de uma pessoa (pelo e-mail da conta).
 *
 * @param string $email E-mail.
 * @return int[]
 */
function pna_core_solicitacoes_do_email( $email ) {
	$usuario = get_user_by( 'email', $email );
	if ( ! $usuario ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'pna_solicitacao',
			'post_status'    => 'any',
			'author'         => $usuario->ID,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
}

/**
 * Exporta os dados.
 *
 * @param string $email E-mail.
 * @param int    $pagina Página (não usada: volume pequeno).
 * @return array
 */
function pna_core_exportar_dados( $email, $pagina = 1 ) {
	$itens   = array();
	$usuario = get_user_by( 'email', $email );

	if ( $usuario ) {
		$dados = array();
		foreach ( pna_core_campos_perfil() as $chave => $rotulo ) {
			$valor = get_user_meta( $usuario->ID, $chave, true );
			if ( '' !== $valor ) {
				$dados[] = array(
					'name'  => $rotulo,
					'value' => $valor,
				);
			}
		}
		if ( $dados ) {
			$itens[] = array(
				'group_id'    => 'pna-perfil',
				'group_label' => __( 'PNA – perfil', 'pna' ),
				'item_id'     => 'pna-perfil-' . $usuario->ID,
				'data'        => $dados,
			);
		}
	}

	$rotulos = pna_core_rotulos_campos_formulario();
	foreach ( pna_core_solicitacoes_do_email( $email ) as $id ) {
		$s     = pna_core_obter_solicitacao( $id );
		$dados = array(
			array(
				'name'  => __( 'Tipo', 'pna' ),
				'value' => pna_core_tipos_solicitacao()[ $s['tipo'] ] ?? $s['tipo'],
			),
			array(
				'name'  => __( 'Pet', 'pna' ),
				'value' => get_the_title( $s['pet_id'] ),
			),
			array(
				'name'  => __( 'Status', 'pna' ),
				'value' => pna_core_status_solicitacao( $s['tipo'] )[ $s['status'] ] ?? $s['status'],
			),
			array(
				'name'  => __( 'Data', 'pna' ),
				'value' => get_the_date( 'd/m/Y', $id ),
			),
		);
		foreach ( $s['dados'] as $chave => $valor ) {
			$dados[] = array(
				'name'  => $rotulos[ $chave ] ?? $chave,
				'value' => $valor,
			);
		}
		$itens[] = array(
			'group_id'    => 'pna-solicitacoes',
			'group_label' => __( 'PNA – solicitações', 'pna' ),
			'item_id'     => 'pna-solicitacao-' . $id,
			'data'        => $dados,
		);
	}

	return array(
		'data' => $itens,
		'done' => true,
	);
}

/**
 * Apaga dados do perfil e anonimiza as respostas das solicitações.
 *
 * @param string $email E-mail.
 * @param int    $pagina Página (não usada).
 * @return array
 */
function pna_core_apagar_dados( $email, $pagina = 1 ) {
	$removidos = false;
	$usuario   = get_user_by( 'email', $email );

	if ( $usuario ) {
		foreach ( array_keys( pna_core_campos_perfil() ) as $chave ) {
			if ( delete_user_meta( $usuario->ID, $chave ) ) {
				$removidos = true;
			}
		}
	}

	foreach ( pna_core_solicitacoes_do_email( $email ) as $id ) {
		update_post_meta( $id, '_pna_dados', array( 'nome' => __( '[dados removidos a pedido do titular]', 'pna' ) ) );
		delete_post_meta( $id, '_pna_nota_interna' );
		$removidos = true;
	}

	return array(
		'items_removed'  => $removidos,
		'items_retained' => false,
		'messages'       => array( __( 'As solicitações foram mantidas sem dados pessoais, para o histórico do projeto.', 'pna' ) ),
		'done'           => true,
	);
}
