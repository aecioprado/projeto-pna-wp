<?php
/**
 * Solicitações de adoção e de apadrinhamento.
 *
 * Uma única entidade ("pna_solicitacao") com um campo de tipo. Cada
 * solicitação é privada, pertence à pessoa que a enviou (autor) e
 * guarda o pet, o status, as respostas do formulário e o histórico.
 *
 * Regra: o status só muda pela função pna_core_alterar_status_solicitacao(),
 * que valida a mudança, registra o histórico, atualiza o pet e avisa
 * o resto do sistema (ação 'pna_core_solicitacao_status_alterado').
 *
 * Referência completa: docs/modelo-de-dados.md
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tipos de solicitação.
 *
 * @return array
 */
function pna_core_tipos_solicitacao() {
	return array(
		'adocao'         => __( 'Adoção', 'pna' ),
		'apadrinhamento' => __( 'Apadrinhamento', 'pna' ),
	);
}

/**
 * Status de cada tipo de solicitação, na ordem do fluxo.
 *
 * @param string $tipo 'adocao' ou 'apadrinhamento'. Vazio = todos.
 * @return array
 */
function pna_core_status_solicitacao( $tipo = '' ) {
	$adocao = array(
		'enviada'    => __( 'Enviada', 'pna' ),
		'em_analise' => __( 'Em análise', 'pna' ),
		'aprovada'   => __( 'Aprovada', 'pna' ),
		'recusada'   => __( 'Recusada', 'pna' ),
		'concluida'  => __( 'Concluída (pet entregue)', 'pna' ),
		'cancelada'  => __( 'Cancelada', 'pna' ),
	);

	$apadrinhamento = array(
		'enviada'    => __( 'Enviada', 'pna' ),
		'em_analise' => __( 'Em análise', 'pna' ),
		'aprovada'   => __( 'Ativo (aprovado)', 'pna' ),
		'recusada'   => __( 'Recusada', 'pna' ),
		'encerrada'  => __( 'Encerrado', 'pna' ),
		'cancelada'  => __( 'Cancelada', 'pna' ),
	);

	if ( 'adocao' === $tipo ) {
		return $adocao;
	}
	if ( 'apadrinhamento' === $tipo ) {
		return $apadrinhamento;
	}
	return array_merge( $adocao, $apadrinhamento );
}

/**
 * Status que exigem a permissão de decidir (Gestor PNA ou Administrador).
 *
 * @return string[]
 */
function pna_core_status_de_decisao() {
	return array( 'aprovada', 'recusada', 'concluida', 'encerrada' );
}

/**
 * Registra o tipo de conteúdo das solicitações.
 */
function pna_core_registrar_solicitacoes() {
	register_post_type(
		'pna_solicitacao',
		array(
			'labels'          => array(
				'name'          => __( 'Solicitações', 'pna' ),
				'singular_name' => __( 'Solicitação', 'pna' ),
				'menu_name'     => __( 'Solicitações', 'pna' ),
				'all_items'     => __( 'Todas as solicitações', 'pna' ),
				'edit_item'     => __( 'Analisar solicitação', 'pna' ),
				'search_items'  => __( 'Buscar solicitações', 'pna' ),
				'not_found'     => __( 'Nenhuma solicitação encontrada.', 'pna' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-clipboard',
			'menu_position'   => 6,
			'supports'        => array( 'title' ),
			'capability_type' => array( 'pna_solicitacao', 'pna_solicitacoes' ),
			'capabilities'    => array(
				// Solicitações só são criadas pelos formulários do site.
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'pna_core_registrar_solicitacoes' );

/**
 * Cria uma solicitação (usado pelos formulários do site e pelo conteúdo de exemplo).
 *
 * @param array $args {
 *     @type string $tipo    'adocao' ou 'apadrinhamento'.
 *     @type int    $pet_id  ID do pet.
 *     @type int    $user_id ID de quem solicita.
 *     @type array  $dados   Respostas do formulário (chave => valor).
 * }
 * @return int|WP_Error ID da solicitação ou erro.
 */
function pna_core_criar_solicitacao( $args ) {
	$tipo    = isset( $args['tipo'] ) ? $args['tipo'] : '';
	$pet_id  = isset( $args['pet_id'] ) ? (int) $args['pet_id'] : 0;
	$user_id = isset( $args['user_id'] ) ? (int) $args['user_id'] : 0;
	$dados   = isset( $args['dados'] ) ? (array) $args['dados'] : array();

	if ( ! array_key_exists( $tipo, pna_core_tipos_solicitacao() ) ) {
		return new WP_Error( 'pna_tipo_invalido', __( 'Tipo de solicitação inválido.', 'pna' ) );
	}
	if ( 'pet' !== get_post_type( $pet_id ) ) {
		return new WP_Error( 'pna_pet_invalido', __( 'Pet não encontrado.', 'pna' ) );
	}
	if ( ! get_userdata( $user_id ) ) {
		return new WP_Error( 'pna_usuario_invalido', __( 'Usuário não encontrado.', 'pna' ) );
	}
	if ( 'adocao' === $tipo && 'adotado' === pna_core_obter_status_pet( $pet_id ) ) {
		return new WP_Error( 'pna_pet_adotado', __( 'Este pet já foi adotado.', 'pna' ) );
	}

	$usuario = get_userdata( $user_id );
	$titulo  = sprintf(
		/* translators: 1: tipo, 2: nome do pet, 3: nome da pessoa. */
		__( '%1$s – %2$s – %3$s', 'pna' ),
		pna_core_tipos_solicitacao()[ $tipo ],
		get_the_title( $pet_id ),
		$usuario->display_name
	);

	$id = wp_insert_post(
		array(
			'post_type'   => 'pna_solicitacao',
			'post_status' => 'private',
			'post_title'  => $titulo,
			'post_author' => $user_id,
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		return $id;
	}

	update_post_meta( $id, '_pna_tipo', $tipo );
	update_post_meta( $id, '_pna_pet_id', $pet_id );
	update_post_meta( $id, '_pna_dados', array_map( 'sanitize_textarea_field', $dados ) );
	update_post_meta( $id, '_pna_status', 'enviada' );
	update_post_meta(
		$id,
		'_pna_historico',
		array(
			array(
				'status'  => 'enviada',
				'user_id' => $user_id,
				'data'    => current_time( 'mysql' ),
			),
		)
	);

	/**
	 * Disparado quando uma solicitação é criada.
	 *
	 * @param int    $id     ID da solicitação.
	 * @param string $tipo   Tipo.
	 * @param int    $pet_id ID do pet.
	 */
	do_action( 'pna_core_solicitacao_criada', $id, $tipo, $pet_id );

	return $id;
}

/**
 * Dados básicos de uma solicitação.
 *
 * @param int $id ID da solicitação.
 * @return array{tipo:string,pet_id:int,status:string,dados:array,historico:array,nota:string}
 */
function pna_core_obter_solicitacao( $id ) {
	return array(
		'tipo'      => (string) get_post_meta( $id, '_pna_tipo', true ),
		'pet_id'    => (int) get_post_meta( $id, '_pna_pet_id', true ),
		'status'    => (string) get_post_meta( $id, '_pna_status', true ),
		'dados'     => (array) get_post_meta( $id, '_pna_dados', true ),
		'historico' => (array) get_post_meta( $id, '_pna_historico', true ),
		'nota'      => (string) get_post_meta( $id, '_pna_nota_interna', true ),
	);
}

/**
 * Altera o status de uma solicitação. ÚNICO caminho para mudar status.
 *
 * @param int    $id      ID da solicitação.
 * @param string $novo    Novo status.
 * @param int    $user_id Quem fez a mudança (0 = sistema).
 * @return true|WP_Error
 */
function pna_core_alterar_status_solicitacao( $id, $novo, $user_id = 0 ) {
	$solicitacao = pna_core_obter_solicitacao( $id );
	$permitidos  = pna_core_status_solicitacao( $solicitacao['tipo'] );

	if ( ! array_key_exists( $novo, $permitidos ) ) {
		return new WP_Error( 'pna_status_invalido', __( 'Status inválido para este tipo de solicitação.', 'pna' ) );
	}

	$anterior = $solicitacao['status'];
	if ( $anterior === $novo ) {
		return true;
	}

	// Decisões exigem permissão específica (exceto mudanças feitas pelo sistema).
	if ( $user_id && in_array( $novo, pna_core_status_de_decisao(), true ) && ! user_can( $user_id, 'pna_decidir_solicitacoes' ) ) {
		return new WP_Error( 'pna_sem_permissao', __( 'Apenas a coordenação pode aprovar, recusar, concluir ou encerrar solicitações.', 'pna' ) );
	}

	update_post_meta( $id, '_pna_status', $novo );

	$historico   = $solicitacao['historico'];
	$historico[] = array(
		'status'  => $novo,
		'user_id' => (int) $user_id,
		'data'    => current_time( 'mysql' ),
	);
	update_post_meta( $id, '_pna_historico', $historico );

	if ( 'adocao' === $solicitacao['tipo'] ) {
		pna_core_sincronizar_pet_com_adocao( $solicitacao['pet_id'], $novo, $id );
	}

	/**
	 * Disparado quando o status de uma solicitação muda.
	 * Usado pelas notificações e e-mails (fase de notificações).
	 *
	 * @param int    $id       ID da solicitação.
	 * @param string $novo     Novo status.
	 * @param string $anterior Status anterior.
	 * @param string $tipo     Tipo da solicitação.
	 */
	do_action( 'pna_core_solicitacao_status_alterado', $id, $novo, $anterior, $solicitacao['tipo'] );

	return true;
}

/**
 * Mantém a situação do pet coerente com as adoções.
 *
 * - Adoção aprovada  → pet "Em processo".
 * - Adoção concluída → pet "Adotado" e apadrinhamentos ativos encerrados.
 * - Adoção recusada/cancelada → pet volta a "Disponível", se não houver outra adoção aprovada.
 *
 * @param int    $pet_id         ID do pet.
 * @param string $status_adocao  Novo status da adoção.
 * @param int    $solicitacao_id ID da adoção que mudou.
 */
function pna_core_sincronizar_pet_com_adocao( $pet_id, $status_adocao, $solicitacao_id ) {
	if ( ! $pet_id ) {
		return;
	}

	if ( 'aprovada' === $status_adocao && 'adotado' !== pna_core_obter_status_pet( $pet_id ) ) {
		pna_core_definir_status_pet( $pet_id, 'em_processo' );
		return;
	}

	if ( 'concluida' === $status_adocao ) {
		pna_core_definir_status_pet( $pet_id, 'adotado' );

		foreach ( pna_core_buscar_solicitacoes( $pet_id, 'apadrinhamento', array( 'aprovada' ) ) as $apadrinhamento ) {
			pna_core_alterar_status_solicitacao( $apadrinhamento, 'encerrada', 0 );
		}
		return;
	}

	if ( in_array( $status_adocao, array( 'recusada', 'cancelada' ), true ) && 'em_processo' === pna_core_obter_status_pet( $pet_id ) ) {
		$outras = array_diff( pna_core_buscar_solicitacoes( $pet_id, 'adocao', array( 'aprovada' ) ), array( $solicitacao_id ) );
		if ( empty( $outras ) ) {
			pna_core_definir_status_pet( $pet_id, 'disponivel' );
		}
	}
}

/**
 * IDs de solicitações de um pet, filtradas por tipo e status.
 *
 * @param int      $pet_id ID do pet (0 = todos).
 * @param string   $tipo   Tipo (vazio = todos).
 * @param string[] $status Status aceitos (vazio = todos).
 * @return int[]
 */
function pna_core_buscar_solicitacoes( $pet_id = 0, $tipo = '', $status = array() ) {
	$meta_query = array();
	if ( $pet_id ) {
		$meta_query[] = array(
			'key'   => '_pna_pet_id',
			'value' => (int) $pet_id,
		);
	}
	if ( $tipo ) {
		$meta_query[] = array(
			'key'   => '_pna_tipo',
			'value' => $tipo,
		);
	}
	if ( $status ) {
		$meta_query[] = array(
			'key'     => '_pna_status',
			'value'   => $status,
			'compare' => 'IN',
		);
	}

	return get_posts(
		array(
			'post_type'      => 'pna_solicitacao',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		)
	);
}

/**
 * Número de padrinhos ativos de um pet (Figma: "Padrinhos: 01").
 *
 * @param int $pet_id ID do pet.
 * @return int
 */
function pna_core_contar_padrinhos( $pet_id ) {
	return count( pna_core_buscar_solicitacoes( $pet_id, 'apadrinhamento', array( 'aprovada' ) ) );
}

/**
 * Número de adoções concluídas (Figma: "Contagem de adoções").
 *
 * @param string $periodo 'total' ou 'hoje'.
 * @return int
 */
function pna_core_contar_adocoes( $periodo = 'total' ) {
	$concluidas = pna_core_buscar_solicitacoes( 0, 'adocao', array( 'concluida' ) );

	if ( 'hoje' !== $periodo ) {
		return count( $concluidas );
	}

	$hoje  = current_time( 'Y-m-d' );
	$total = 0;
	foreach ( $concluidas as $id ) {
		foreach ( array_reverse( (array) get_post_meta( $id, '_pna_historico', true ) ) as $item ) {
			if ( isset( $item['status'] ) && 'concluida' === $item['status'] ) {
				if ( 0 === strpos( (string) $item['data'], $hoje ) ) {
					++$total;
				}
				break;
			}
		}
	}
	return $total;
}
