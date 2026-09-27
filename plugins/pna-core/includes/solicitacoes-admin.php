<?php
/**
 * Solicitações no painel: lista, filtros e tela de análise.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Quadros da tela de análise.
 */
function pna_core_quadros_solicitacao() {
	add_meta_box( 'pna_solicitacao_dados', __( 'Respostas do formulário', 'pna' ), 'pna_core_render_dados_solicitacao', 'pna_solicitacao', 'normal', 'high' );
	add_meta_box( 'pna_solicitacao_decisao', __( 'Status e decisão', 'pna' ), 'pna_core_render_decisao_solicitacao', 'pna_solicitacao', 'side', 'high' );
	add_meta_box( 'pna_solicitacao_historico', __( 'Histórico', 'pna' ), 'pna_core_render_historico_solicitacao', 'pna_solicitacao', 'side', 'default' );
}
add_action( 'add_meta_boxes_pna_solicitacao', 'pna_core_quadros_solicitacao' );


/**
 * Quadro "Respostas do formulário" (somente leitura).
 *
 * @param WP_Post $post Solicitação.
 */
function pna_core_render_dados_solicitacao( $post ) {
	$s       = pna_core_obter_solicitacao( $post->ID );
	$autor   = get_userdata( (int) $post->post_author );
	$rotulos = pna_core_rotulos_campos_formulario();
	?>
	<table class="pna-quadro-dados">
		<tr>
			<th><?php esc_html_e( 'Tipo', 'pna' ); ?></th>
			<td><?php echo esc_html( pna_core_tipos_solicitacao()[ $s['tipo'] ] ?? '—' ); ?></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Pet', 'pna' ); ?></th>
			<td>
				<?php if ( $s['pet_id'] ) : ?>
					<a href="<?php echo esc_url( get_edit_post_link( $s['pet_id'] ) ?: get_permalink( $s['pet_id'] ) ); ?>"><?php echo esc_html( get_the_title( $s['pet_id'] ) ); ?></a>
					(<?php echo esc_html( pna_core_status_pet()[ pna_core_obter_status_pet( $s['pet_id'] ) ] ); ?>)
				<?php else : ?>
					—
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Conta de quem solicitou', 'pna' ); ?></th>
			<td><?php echo $autor ? esc_html( $autor->display_name . ' <' . $autor->user_email . '>' ) : '—'; ?></td>
		</tr>
		<?php foreach ( $s['dados'] as $chave => $valor ) : ?>
			<tr>
				<th><?php echo esc_html( $rotulos[ $chave ] ?? $chave ); ?></th>
				<td><?php echo esc_html( (string) $valor ); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

/**
 * Quadro "Status e decisão".
 *
 * @param WP_Post $post Solicitação.
 */
function pna_core_render_decisao_solicitacao( $post ) {
	$s       = pna_core_obter_solicitacao( $post->ID );
	$decide  = current_user_can( 'pna_decidir_solicitacoes' );
	$opcoes  = pna_core_status_solicitacao( $s['tipo'] );
	wp_nonce_field( 'pna_salvar_solicitacao', 'pna_solicitacao_nonce' );
	?>
	<p>
		<label for="pna_status_solicitacao"><strong><?php esc_html_e( 'Status', 'pna' ); ?></strong></label><br>
		<select name="pna_status_solicitacao" id="pna_status_solicitacao" style="width:100%">
			<?php foreach ( $opcoes as $valor => $texto ) : ?>
				<?php $bloqueado = ! $decide && in_array( $valor, pna_core_status_de_decisao(), true ) && $valor !== $s['status']; ?>
				<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $s['status'], $valor ); ?> <?php disabled( $bloqueado ); ?>><?php echo esc_html( $texto ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php if ( ! $decide ) : ?>
		<p class="description"><?php esc_html_e( 'Aprovar, recusar, concluir e encerrar são decisões da coordenação (Gestor PNA).', 'pna' ); ?></p>
	<?php endif; ?>
	<p>
		<label for="pna_nota_interna"><strong><?php esc_html_e( 'Nota interna', 'pna' ); ?></strong></label><br>
		<textarea name="pna_nota_interna" id="pna_nota_interna" rows="5" style="width:100%"><?php echo esc_textarea( $s['nota'] ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Visível só para a equipe. Nunca é mostrada a quem solicitou.', 'pna' ); ?></span>
	</p>
	<?php
}

/**
 * Quadro "Histórico".
 *
 * @param WP_Post $post Solicitação.
 */
function pna_core_render_historico_solicitacao( $post ) {
	$s      = pna_core_obter_solicitacao( $post->ID );
	$status = pna_core_status_solicitacao( $s['tipo'] );
	if ( empty( $s['historico'] ) ) {
		echo '<p>—</p>';
		return;
	}
	echo '<ol class="pna-historico">';
	foreach ( array_reverse( $s['historico'] ) as $item ) {
		$quem = $item['user_id'] ? get_userdata( (int) $item['user_id'] ) : null;
		printf(
			'<li><strong>%1$s</strong><br>%2$s · %3$s</li>',
			esc_html( $status[ $item['status'] ] ?? $item['status'] ),
			esc_html( mysql2date( 'd/m/Y H:i', $item['data'] ) ),
			esc_html( $quem ? $quem->display_name : __( 'Sistema', 'pna' ) )
		);
	}
	echo '</ol>';
}

/**
 * Salva status e nota interna.
 *
 * @param int $post_id ID da solicitação.
 */
function pna_core_salvar_solicitacao( $post_id ) {
	if ( ! isset( $_POST['pna_solicitacao_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pna_solicitacao_nonce'] ), 'pna_salvar_solicitacao' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['pna_nota_interna'] ) ) {
		update_post_meta( $post_id, '_pna_nota_interna', sanitize_textarea_field( wp_unslash( $_POST['pna_nota_interna'] ) ) );
	}

	if ( isset( $_POST['pna_status_solicitacao'] ) ) {
		$resultado = pna_core_alterar_status_solicitacao( $post_id, sanitize_key( $_POST['pna_status_solicitacao'] ), get_current_user_id() );
		if ( is_wp_error( $resultado ) ) {
			set_transient( 'pna_aviso_solicitacao_' . get_current_user_id(), $resultado->get_error_message(), 60 );
		}
	}
}
add_action( 'save_post_pna_solicitacao', 'pna_core_salvar_solicitacao' );

/**
 * Mostra o erro de uma mudança de status recusada.
 */
function pna_core_aviso_solicitacao() {
	$chave = 'pna_aviso_solicitacao_' . get_current_user_id();
	$aviso = get_transient( $chave );
	if ( $aviso ) {
		delete_transient( $chave );
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $aviso ) );
	}
}
add_action( 'admin_notices', 'pna_core_aviso_solicitacao' );

/**
 * Solicitações continuam privadas mesmo se alguém mudar a visibilidade.
 *
 * @param array $dados Dados do post a salvar.
 * @return array
 */
function pna_core_solicitacao_sempre_privada( $dados ) {
	if ( 'pna_solicitacao' === $dados['post_type'] && ! in_array( $dados['post_status'], array( 'trash', 'auto-draft' ), true ) ) {
		$dados['post_status'] = 'private';
	}
	return $dados;
}
add_filter( 'wp_insert_post_data', 'pna_core_solicitacao_sempre_privada' );

/**
 * Remove a "Edição rápida" (ela permitiria mudar dados sem as regras de status).
 *
 * @param array   $acoes Ações da linha.
 * @param WP_Post $post  Post.
 * @return array
 */
function pna_core_acoes_linha_solicitacao( $acoes, $post ) {
	if ( 'pna_solicitacao' === $post->post_type ) {
		unset( $acoes['inline hide-if-no-js'] );
	}
	return $acoes;
}
add_filter( 'post_row_actions', 'pna_core_acoes_linha_solicitacao', 10, 2 );

/**
 * Colunas da lista de solicitações.
 *
 * @param array $colunas Colunas atuais.
 * @return array
 */
function pna_core_colunas_solicitacoes( $colunas ) {
	return array(
		'cb'              => $colunas['cb'],
		'title'           => __( 'Solicitação', 'pna' ),
		'pna_tipo'        => __( 'Tipo', 'pna' ),
		'pna_pet'         => __( 'Pet', 'pna' ),
		'pna_solicitante' => __( 'Quem solicitou', 'pna' ),
		'pna_status'      => __( 'Status', 'pna' ),
		'date'            => __( 'Data', 'pna' ),
	);
}
add_filter( 'manage_pna_solicitacao_posts_columns', 'pna_core_colunas_solicitacoes' );

/**
 * Conteúdo das colunas.
 *
 * @param string $coluna  Coluna.
 * @param int    $post_id ID da solicitação.
 */
function pna_core_conteudo_colunas_solicitacoes( $coluna, $post_id ) {
	$s = pna_core_obter_solicitacao( $post_id );
	switch ( $coluna ) {
		case 'pna_tipo':
			echo esc_html( pna_core_tipos_solicitacao()[ $s['tipo'] ] ?? '—' );
			break;
		case 'pna_pet':
			echo esc_html( $s['pet_id'] ? get_the_title( $s['pet_id'] ) : '—' );
			break;
		case 'pna_solicitante':
			$autor = get_userdata( (int) get_post_field( 'post_author', $post_id ) );
			echo esc_html( $autor ? $autor->display_name : '—' );
			break;
		case 'pna_status':
			$rotulos = pna_core_status_solicitacao( $s['tipo'] );
			echo pna_core_etiqueta_status( $s['status'], $rotulos[ $s['status'] ] ?? $s['status'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
	}
}
add_action( 'manage_pna_solicitacao_posts_custom_column', 'pna_core_conteudo_colunas_solicitacoes', 10, 2 );

/**
 * Filtros por tipo e status na lista.
 *
 * @param string $post_type Tipo de conteúdo da lista.
 */
function pna_core_filtros_lista_solicitacoes( $post_type ) {
	if ( 'pna_solicitacao' !== $post_type ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$tipo   = isset( $_GET['pna_tipo'] ) ? sanitize_key( $_GET['pna_tipo'] ) : '';
	$status = isset( $_GET['pna_status'] ) ? sanitize_key( $_GET['pna_status'] ) : '';
	// phpcs:enable
	?>
	<select name="pna_tipo" aria-label="<?php esc_attr_e( 'Filtrar por tipo', 'pna' ); ?>">
		<option value=""><?php esc_html_e( 'Todos os tipos', 'pna' ); ?></option>
		<?php foreach ( pna_core_tipos_solicitacao() as $valor => $texto ) : ?>
			<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $tipo, $valor ); ?>><?php echo esc_html( $texto ); ?></option>
		<?php endforeach; ?>
	</select>
	<select name="pna_status" aria-label="<?php esc_attr_e( 'Filtrar por status', 'pna' ); ?>">
		<option value=""><?php esc_html_e( 'Todos os status', 'pna' ); ?></option>
		<?php foreach ( pna_core_status_solicitacao() as $valor => $texto ) : ?>
			<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $status, $valor ); ?>><?php echo esc_html( $texto ); ?></option>
		<?php endforeach; ?>
	</select>
	<?php
}
add_action( 'restrict_manage_posts', 'pna_core_filtros_lista_solicitacoes' );

/**
 * Aplica os filtros da lista.
 *
 * @param WP_Query $query Consulta.
 */
function pna_core_aplicar_filtros_solicitacoes( $query ) {
	global $pagenow;
	if ( 'edit.php' !== $pagenow || ! $query->is_main_query() || 'pna_solicitacao' !== $query->get( 'post_type' ) ) {
		return;
	}
	$meta_query = array();
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( ! empty( $_GET['pna_tipo'] ) ) {
		$meta_query[] = array(
			'key'   => '_pna_tipo',
			'value' => sanitize_key( $_GET['pna_tipo'] ),
		);
	}
	if ( ! empty( $_GET['pna_status'] ) ) {
		$meta_query[] = array(
			'key'   => '_pna_status',
			'value' => sanitize_key( $_GET['pna_status'] ),
		);
	}
	// phpcs:enable
	if ( $meta_query ) {
		$query->set( 'meta_query', $meta_query );
	}
}
add_action( 'pre_get_posts', 'pna_core_aplicar_filtros_solicitacoes' );

/**
 * Contador de solicitações novas no menu lateral.
 */
function pna_core_contador_menu_solicitacoes() {
	global $menu;
	if ( ! current_user_can( 'edit_pna_solicitacoes' ) || ! is_array( $menu ) ) {
		return;
	}
	$novas = count( pna_core_buscar_solicitacoes( 0, '', array( 'enviada' ) ) );
	if ( ! $novas ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=pna_solicitacao' === $item[2] ) {
			$menu[ $i ][0] .= sprintf( ' <span class="awaiting-mod"><span class="pending-count">%d</span></span>', $novas ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			break;
		}
	}
}
add_action( 'admin_menu', 'pna_core_contador_menu_solicitacoes', 99 );
