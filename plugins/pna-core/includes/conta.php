<?php
/**
 * Conta do Membro: cadastro, login, dados do perfil, senha e
 * cancelamento de solicitações.
 *
 * Os formulários enviam para a própria página (POST com o campo
 * "pna_acao"). Em caso de erro, a página é mostrada de novo com os
 * erros e os valores digitados; em caso de sucesso, redireciona.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Campos do perfil guardados como metadados do usuário.
 *
 * @return array chave => rótulo
 */
function pna_core_campos_perfil() {
	return array(
		'pna_pronome'    => __( 'Pronome', 'pna' ),
		'pna_telefone'   => __( 'Telefone / WhatsApp', 'pna' ),
		'pna_cep'        => __( 'CEP', 'pna' ),
		'pna_nascimento' => __( 'Data de nascimento', 'pna' ),
		'pna_ocupacao'   => __( 'Ocupação', 'pna' ),
	);
}

/**
 * Distribui os envios de formulário para o tratamento certo.
 */
function pna_core_tratar_envios() {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || empty( $_POST['pna_acao'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}
	$acao = sanitize_key( $_POST['pna_acao'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( ! isset( $_POST['pna_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pna_nonce'] ), 'pna_' . $acao ) ) {
		pna_core_estado_form(
			$acao,
			array(
				'erros'   => array( 'geral' => __( 'O formulário expirou. Recarregue a página e tente de novo.', 'pna' ) ),
				'valores' => array(),
			)
		);
		return;
	}

	switch ( $acao ) {
		case 'cadastro':
			pna_core_tratar_cadastro();
			break;
		case 'entrar':
			pna_core_tratar_login();
			break;
		case 'perfil':
			pna_core_tratar_perfil();
			break;
		case 'senha':
			pna_core_tratar_senha();
			break;
		case 'cancelar':
			pna_core_tratar_cancelamento();
			break;
		case 'adocao':
		case 'apadrinhamento':
			pna_core_tratar_formulario_solicitacao( $acao );
			break;
	}
}
add_action( 'template_redirect', 'pna_core_tratar_envios', 10 );

/**
 * Para onde ir depois do login ou do cadastro.
 *
 * @param WP_User $usuario Usuário.
 * @return string
 */
function pna_core_destino_apos_login( $usuario ) {
	$pedido = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $pedido ) {
		return wp_validate_redirect( $pedido, pna_core_url_pagina( 'meu-perfil' ) );
	}
	return user_can( $usuario, 'edit_posts' ) || user_can( $usuario, 'edit_pna_solicitacoes' ) ? admin_url() : pna_core_url_pagina( 'meu-perfil' );
}

/**
 * Cadastro (Figma: nome, e-mail, CEP, senha, confirmar senha, termo).
 */
function pna_core_tratar_cadastro() {
	if ( pna_core_envio_de_robo() || ! get_option( 'users_can_register' ) ) {
		return;
	}

	$v = array(
		'nome'       => pna_core_post( 'nome' ),
		'email'      => sanitize_email( pna_core_post( 'email' ) ),
		'cep'        => pna_core_post( 'cep' ),
		'termo'      => pna_core_post( 'termo' ),
		'privacidade' => pna_core_post( 'privacidade' ),
	);
	$senha    = isset( $_POST['senha'] ) ? (string) wp_unslash( $_POST['senha'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$confirma = isset( $_POST['confirmar_senha'] ) ? (string) wp_unslash( $_POST['confirmar_senha'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$erros    = array();

	if ( mb_strlen( $v['nome'] ) < 3 ) {
		$erros['nome'] = __( 'Informe seu nome completo.', 'pna' );
	}
	if ( ! is_email( $v['email'] ) ) {
		$erros['email'] = __( 'Informe um e-mail válido.', 'pna' );
	} elseif ( email_exists( $v['email'] ) ) {
		$erros['email'] = __( 'Já existe uma conta com este e-mail. Use a página Entrar.', 'pna' );
	}
	if ( 8 !== strlen( pna_core_digitos( $v['cep'] ) ) ) {
		$erros['cep'] = __( 'Informe um CEP com 8 números.', 'pna' );
	}
	if ( strlen( $senha ) < 8 ) {
		$erros['senha'] = __( 'A senha precisa ter pelo menos 8 caracteres.', 'pna' );
	} elseif ( $senha !== $confirma ) {
		$erros['confirmar_senha'] = __( 'As senhas não são iguais.', 'pna' );
	}
	if ( '1' !== $v['termo'] ) {
		$erros['termo'] = __( 'É preciso concordar com o Termo de Compromisso.', 'pna' );
	}
	if ( '1' !== $v['privacidade'] ) {
		$erros['privacidade'] = __( 'É preciso concordar com a Política de Privacidade.', 'pna' );
	}

	if ( $erros ) {
		pna_core_estado_form( 'cadastro', array( 'erros' => $erros, 'valores' => $v ) );
		return;
	}

	// Nome de usuário gerado a partir do e-mail (a pessoa entra com o e-mail).
	$base  = sanitize_user( strstr( $v['email'], '@', true ), true );
	$base  = $base ? $base : 'membro';
	$login = $base;
	for ( $i = 2; username_exists( $login ); $i++ ) {
		$login = $base . $i;
	}

	$user_id = wp_insert_user(
		array(
			'user_login'   => $login,
			'user_email'   => $v['email'],
			'user_pass'    => $senha,
			'display_name' => $v['nome'],
			'first_name'   => $v['nome'],
			'role'         => get_option( 'default_role', 'pna_membro' ),
		)
	);

	if ( is_wp_error( $user_id ) ) {
		pna_core_estado_form(
			'cadastro',
			array(
				'erros'   => array( 'geral' => $user_id->get_error_message() ),
				'valores' => $v,
			)
		);
		return;
	}

	update_user_meta( $user_id, 'pna_cep', pna_core_digitos( $v['cep'] ) );
	update_user_meta( $user_id, 'pna_aceite_termos', current_time( 'mysql' ) );

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	wp_safe_redirect( add_query_arg( 'pna_aviso', 'bem-vindo', pna_core_destino_apos_login( get_userdata( $user_id ) ) ) );
	exit;
}

/**
 * Login com e-mail (ou nome de usuário) e senha.
 */
function pna_core_tratar_login() {
	if ( pna_core_envio_de_robo() ) {
		return;
	}
	$identificacao = pna_core_post( 'usuario' );
	$senha         = isset( $_POST['senha'] ) ? (string) wp_unslash( $_POST['senha'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	$usuario = wp_signon(
		array(
			'user_login'    => $identificacao,
			'user_password' => $senha,
			'remember'      => true,
		),
		is_ssl()
	);

	if ( is_wp_error( $usuario ) ) {
		pna_core_estado_form(
			'entrar',
			array(
				// Mensagem única: não revela se o e-mail existe.
				'erros'   => array( 'geral' => __( 'E-mail ou senha incorretos.', 'pna' ) ),
				'valores' => array( 'usuario' => $identificacao ),
			)
		);
		return;
	}

	wp_safe_redirect( pna_core_destino_apos_login( $usuario ) );
	exit;
}

/**
 * Atualiza os dados do perfil.
 */
function pna_core_tratar_perfil() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$user_id = get_current_user_id();
	$v       = array(
		'nome'           => pna_core_post( 'nome' ),
		'pna_pronome'    => pna_core_post( 'pna_pronome' ),
		'pna_telefone'   => pna_core_post( 'pna_telefone' ),
		'pna_cep'        => pna_core_post( 'pna_cep' ),
		'pna_nascimento' => pna_core_post( 'pna_nascimento' ),
		'pna_ocupacao'   => pna_core_post( 'pna_ocupacao' ),
	);
	$erros = array();

	if ( mb_strlen( $v['nome'] ) < 3 ) {
		$erros['nome'] = __( 'Informe seu nome completo.', 'pna' );
	}
	if ( '' !== $v['pna_cep'] && 8 !== strlen( pna_core_digitos( $v['pna_cep'] ) ) ) {
		$erros['pna_cep'] = __( 'Informe um CEP com 8 números.', 'pna' );
	}
	if ( '' !== $v['pna_telefone'] && strlen( pna_core_digitos( $v['pna_telefone'] ) ) < 10 ) {
		$erros['pna_telefone'] = __( 'Informe o telefone com DDD.', 'pna' );
	}
	if ( '' !== $v['pna_nascimento'] && ! pna_core_data_valida( $v['pna_nascimento'] ) ) {
		$erros['pna_nascimento'] = __( 'Informe uma data de nascimento válida.', 'pna' );
	}

	if ( $erros ) {
		pna_core_estado_form( 'perfil', array( 'erros' => $erros, 'valores' => $v ) );
		return;
	}

	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $v['nome'],
			'first_name'   => $v['nome'],
		)
	);
	$v['pna_cep']      = pna_core_digitos( $v['pna_cep'] );
	foreach ( array_keys( pna_core_campos_perfil() ) as $chave ) {
		update_user_meta( $user_id, $chave, $v[ $chave ] );
	}

	wp_safe_redirect( add_query_arg( 'pna_aviso', 'dados', pna_core_url_pagina( 'meu-perfil' ) ) . '#meus-dados' );
	exit;
}

/**
 * Troca de senha (exige a senha atual).
 */
function pna_core_tratar_senha() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$usuario = wp_get_current_user();
	// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$atual    = isset( $_POST['senha_atual'] ) ? (string) wp_unslash( $_POST['senha_atual'] ) : '';
	$nova     = isset( $_POST['senha_nova'] ) ? (string) wp_unslash( $_POST['senha_nova'] ) : '';
	$confirma = isset( $_POST['senha_confirma'] ) ? (string) wp_unslash( $_POST['senha_confirma'] ) : '';
	// phpcs:enable
	$erros = array();

	if ( ! wp_check_password( $atual, $usuario->user_pass, $usuario->ID ) ) {
		$erros['senha_atual'] = __( 'A senha atual está incorreta.', 'pna' );
	}
	if ( strlen( $nova ) < 8 ) {
		$erros['senha_nova'] = __( 'A nova senha precisa ter pelo menos 8 caracteres.', 'pna' );
	} elseif ( $nova !== $confirma ) {
		$erros['senha_confirma'] = __( 'As senhas não são iguais.', 'pna' );
	}

	if ( $erros ) {
		pna_core_estado_form( 'senha', array( 'erros' => $erros, 'valores' => array() ) );
		return;
	}

	wp_set_password( $nova, $usuario->ID );
	// wp_set_password encerra a sessão: entra de novo automaticamente.
	wp_set_current_user( $usuario->ID );
	wp_set_auth_cookie( $usuario->ID, true );
	wp_safe_redirect( add_query_arg( 'pna_aviso', 'senha', pna_core_url_pagina( 'meu-perfil' ) ) . '#senha' );
	exit;
}

/**
 * O Membro cancela uma solicitação própria.
 *
 * Adoção: pode cancelar enquanto está Enviada ou Em análise.
 * Apadrinhamento: também pode cancelar quando está Ativo.
 */
function pna_core_tratar_cancelamento() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$id = isset( $_POST['solicitacao'] ) ? absint( $_POST['solicitacao'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( ! $id || 'pna_solicitacao' !== get_post_type( $id ) || (int) get_post_field( 'post_author', $id ) !== get_current_user_id() ) {
		return;
	}
	if ( ! pna_core_membro_pode_cancelar( $id ) ) {
		return;
	}
	pna_core_alterar_status_solicitacao( $id, 'cancelada', get_current_user_id() );
	wp_safe_redirect( add_query_arg( 'pna_aviso', 'cancelada', pna_core_url_pagina( 'meu-perfil' ) ) . '#minhas-solicitacoes' );
	exit;
}

/**
 * O Membro ainda pode cancelar esta solicitação?
 *
 * @param int $id ID da solicitação.
 * @return bool
 */
function pna_core_membro_pode_cancelar( $id ) {
	$s = pna_core_obter_solicitacao( $id );
	$permitidos = 'apadrinhamento' === $s['tipo'] ? array( 'enviada', 'em_analise', 'aprovada' ) : array( 'enviada', 'em_analise' );
	return in_array( $s['status'], $permitidos, true );
}

/**
 * Data no formato AAAA-MM-DD (campo type="date") válida e no passado.
 *
 * @param string $data Data.
 * @return bool
 */
function pna_core_data_valida( $data ) {
	$d = DateTime::createFromFormat( 'Y-m-d', $data );
	return $d && $d->format( 'Y-m-d' ) === $data && $d < new DateTime();
}

/**
 * Idade em anos completos a partir de uma data AAAA-MM-DD.
 *
 * @param string $data Data de nascimento.
 * @return int
 */
function pna_core_idade( $data ) {
	$d = DateTime::createFromFormat( 'Y-m-d', $data );
	return $d ? (int) $d->diff( new DateTime( current_time( 'Y-m-d' ) ) )->y : 0;
}
