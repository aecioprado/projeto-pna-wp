<?php
/**
 * Formulário de adoção ou de apadrinhamento (Figma, montado na Fase 3).
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

$pna_tipo = isset( $attributes['tipo'] ) && 'apadrinhamento' === $attributes['tipo'] ? 'apadrinhamento' : 'adocao';

if ( ! is_user_logged_in() ) {
	printf( '<p %s>%s</p>', get_block_wrapper_attributes(), esc_html__( 'Entre para preencher o formulário.', 'pna' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$pna_usuario = wp_get_current_user();
$pna_uid     = $pna_usuario->ID;
$pna_pet     = pna_core_pet_da_url();
$pna_estado  = pna_core_estado_form( $pna_tipo );
// Se o envio falhou por impedimento, ele vem no estado; senão, verifica agora.
$pna_impedimento = $pna_pet ? pna_core_impedimento_formulario( $pna_tipo, $pna_pet, $pna_uid ) : pna_core_impedimento_formulario( $pna_tipo, 0, $pna_uid );
$pna_meta        = static function ( $chave ) use ( $pna_uid ) {
	return (string) get_user_meta( $pna_uid, $chave, true );
};
$pna_privacidade = get_privacy_policy_url();
$pna_texto_priv  = $pna_privacidade
	/* translators: %s: link da política de privacidade. */
	? sprintf( __( 'Li e concordo com a <a href="%s" target="_blank">Política de Privacidade</a> e autorizo o uso dos meus dados para a análise do pedido.', 'pna' ), esc_url( $pna_privacidade ) )
	: __( 'Li e concordo com a Política de Privacidade e autorizo o uso dos meus dados para a análise do pedido.', 'pna' );

$pna_titulo = 'adocao' === $pna_tipo ? __( 'Formulário de Adoção', 'pna' ) : __( 'Formulário de Apadrinhamento', 'pna' );
$pna_passos = 'adocao' === $pna_tipo
	? array( __( 'Você envia este formulário', 'pna' ), __( 'Nossa equipe analisa o pedido', 'pna' ), __( 'Agendamos a retirada por e-mail', 'pna' ), __( 'Você busca o pet no CAA – UFPE', 'pna' ) )
	: array( __( 'Você envia este formulário', 'pna' ), __( 'Nossa equipe aprova o pedido', 'pna' ), __( 'Enviamos as instruções de pagamento', 'pna' ), __( 'Todo mês você recebe notícias do pet', 'pna' ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-solicitar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="pna-solicitar__topo">
		<div class="pna-solicitar__pet">
			<?php if ( $pna_pet && has_post_thumbnail( $pna_pet ) ) : ?>
				<?php echo get_the_post_thumbnail( $pna_pet, 'thumbnail', array( 'class' => 'pna-solicitar__foto', 'alt' => '' ) ); ?>
			<?php else : ?>
				<span class="pna-solicitar__foto" aria-hidden="true"></span>
			<?php endif; ?>
			<div>
				<h1 class="pna-solicitar__titulo"><?php echo esc_html( $pna_titulo ); ?></h1>
				<?php if ( $pna_pet ) : ?>
					<p class="pna-solicitar__lead"><?php echo esc_html( 'adocao' === $pna_tipo ? __( 'Você está pedindo para adotar', 'pna' ) : __( 'Você vai apadrinhar', 'pna' ) ); ?></p>
					<p class="pna-solicitar__nome"><a href="<?php echo esc_url( get_permalink( $pna_pet ) ); ?>"><?php echo esc_html( get_the_title( $pna_pet ) ); ?></a></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="is-style-pna-destaque pna-solicitar__passos">
			<h2 class="wp-block-heading"><?php esc_html_e( 'Como funciona', 'pna' ); ?></h2>
			<ol>
				<?php foreach ( $pna_passos as $pna_passo ) : ?>
					<li><?php echo esc_html( $pna_passo ); ?></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>

	<?php if ( $pna_impedimento ) : ?>
		<div class="pna-aviso pna-aviso--erro" role="alert">
			<p><?php echo esc_html( $pna_impedimento ); ?></p>
			<p><a href="<?php echo esc_url( $pna_pet ? get_permalink( $pna_pet ) : get_post_type_archive_link( 'pet' ) ); ?>"><?php esc_html_e( 'Voltar', 'pna' ); ?></a> · <a href="<?php echo esc_url( pna_core_url_pagina( 'meu-perfil' ) ); ?>"><?php esc_html_e( 'Meu perfil', 'pna' ); ?></a></p>
		</div>
	<?php else : ?>
		<?php pna_core_resumo_erros( $pna_tipo ); ?>

		<form class="pna-form" method="post" action="" novalidate>
			<?php wp_nonce_field( 'pna_' . $pna_tipo, 'pna_nonce' ); ?>
			<input type="hidden" name="pna_acao" value="<?php echo esc_attr( $pna_tipo ); ?>">
			<input type="hidden" name="pna_pet" value="<?php echo esc_attr( (string) $pna_pet ); ?>">
			<?php pna_core_campo_armadilha(); ?>

			<h2 class="pna-form__secao-titulo"><?php esc_html_e( 'Seus dados', 'pna' ); ?></h2>
			<p class="pna-campo__ajuda"><?php esc_html_e( 'Trouxemos as informações do seu cadastro. Confira e atualize o que for preciso. Todos os campos são obrigatórios.', 'pna' ); ?></p>
			<div class="pna-form__grade">
				<?php
				pna_core_campo( $pna_tipo, 'nome', __( 'Nome completo', 'pna' ), array( 'valor' => $pna_usuario->display_name, 'autocomplete' => 'name' ) );
				pna_core_campo( $pna_tipo, 'email_conta', __( 'E-mail', 'pna' ), array( 'tipo' => 'email', 'valor' => $pna_usuario->user_email, 'somente_leitura' => true, 'ajuda' => __( 'Usaremos este e-mail para falar com você.', 'pna' ) ) );
				if ( 'adocao' === $pna_tipo ) {
					pna_core_campo( $pna_tipo, 'nascimento', __( 'Data de nascimento', 'pna' ), array( 'tipo' => 'date', 'valor' => $pna_meta( 'pna_nascimento' ), 'autocomplete' => 'bday', 'ajuda' => __( 'A adoção é permitida apenas para maiores de 18 anos.', 'pna' ) ) );
				}
				pna_core_campo( $pna_tipo, 'telefone', __( 'Telefone / WhatsApp', 'pna' ), array( 'tipo' => 'tel', 'valor' => $pna_meta( 'pna_telefone' ), 'placeholder' => '(81) 00000-0000', 'autocomplete' => 'tel' ) );
				pna_core_campo( $pna_tipo, 'cep', __( 'CEP', 'pna' ), array( 'valor' => $pna_meta( 'pna_cep' ), 'autocomplete' => 'postal-code', 'ajuda' => __( 'Atendemos Caruaru e região.', 'pna' ) ) );
				if ( 'adocao' === $pna_tipo ) {
					pna_core_campo( $pna_tipo, 'ocupacao', __( 'Ocupação', 'pna' ), array( 'valor' => $pna_meta( 'pna_ocupacao' ), 'placeholder' => __( 'Ex.: estudante', 'pna' ) ) );
				}
				?>
			</div>

			<?php if ( 'adocao' === $pna_tipo ) : ?>
				<h2 class="pna-form__secao-titulo"><?php esc_html_e( 'Sobre você e o pet', 'pna' ); ?></h2>
				<div class="pna-form__grade">
					<?php
					pna_core_campo( $pna_tipo, 'animais', __( 'Quantos animais você já tem?', 'pna' ), array( 'placeholder' => __( 'Ex.: 2 (1 gato e 1 cachorro)', 'pna' ), 'ajuda' => __( 'Assim indicamos conteúdos para ajudar na adaptação.', 'pna' ) ) );
					pna_core_campo( $pna_tipo, 'pet_nome', __( 'Pet escolhido', 'pna' ), array( 'valor' => get_the_title( $pna_pet ), 'somente_leitura' => true, 'ajuda' => __( 'Vem da página do pet e não pode ser alterado aqui.', 'pna' ) ) );
					pna_core_campo( $pna_tipo, 'motivo', __( 'Por que você decidiu adotar?', 'pna' ), array( 'tipo' => 'textarea', 'largo' => true, 'max' => 1000, 'placeholder' => __( 'Conte um pouco sobre você, sua rotina e como o pet vai fazer parte da sua casa.', 'pna' ) ) );
					?>
				</div>
				<?php
				pna_core_aceite( $pna_tipo, 'compromisso', __( '<strong>Estou ciente e me comprometo com a responsabilidade</strong>: cuidar da saúde, do bem-estar e da segurança do pet, sem praticar qualquer tipo de maus-tratos.', 'pna' ) );
				?>
			<?php else : ?>
				<?php
				$pna_valor_atual = pna_core_valor( $pna_tipo, 'valor', '50' );
				$pna_erros       = $pna_estado['erros'];
				?>
				<fieldset class="pna-campo<?php echo isset( $pna_erros['valor'] ) || isset( $pna_erros['outro_valor'] ) ? ' pna-campo--erro' : ''; ?>">
					<legend class="pna-form__secao-titulo"><?php esc_html_e( 'Valor mensal', 'pna' ); ?></legend>
					<p class="pna-campo__ajuda"><?php esc_html_e( 'O valor é usado exclusivamente nos cuidados de saúde e alimentação do pet escolhido.', 'pna' ); ?></p>
					<div class="pna-escolhas">
						<?php foreach ( pna_core_valores_apadrinhamento() as $pna_v => $pna_rotulo ) : ?>
							<label class="pna-escolha"><input type="radio" name="valor" value="<?php echo esc_attr( $pna_v ); ?>" <?php checked( $pna_valor_atual, (string) $pna_v ); ?>><span><?php echo esc_html( $pna_rotulo ); ?></span></label>
						<?php endforeach; ?>
						<label class="pna-escolha"><input type="radio" name="valor" value="outro" <?php checked( $pna_valor_atual, 'outro' ); ?>><span><?php esc_html_e( 'Outro valor', 'pna' ); ?></span></label>
					</div>
					<?php if ( isset( $pna_erros['valor'] ) ) : ?>
						<p class="pna-campo__erro"><?php echo esc_html( $pna_erros['valor'] ); ?></p>
					<?php endif; ?>
				</fieldset>
				<div class="pna-form__grade">
					<?php pna_core_campo( $pna_tipo, 'outro_valor', __( 'Outro valor (se escolheu "Outro valor")', 'pna' ), array( 'placeholder' => __( 'Ex.: 30', 'pna' ), 'ajuda' => __( 'Mínimo de R$ 10.', 'pna' ), 'obrigatorio' => false ) ); ?>
				</div>

				<fieldset class="pna-campo<?php echo isset( $pna_erros['pagamento'] ) ? ' pna-campo--erro' : ''; ?>">
					<legend class="pna-form__secao-titulo"><?php esc_html_e( 'Forma de pagamento', 'pna' ); ?></legend>
					<p class="pna-campo__ajuda"><?php esc_html_e( 'Após a aprovação, enviaremos por e-mail as instruções para o pagamento, renovadas todo mês.', 'pna' ); ?></p>
					<?php $pna_pag = pna_core_valor( $pna_tipo, 'pagamento', 'pix' ); ?>
					<?php foreach ( pna_core_formas_pagamento() as $pna_v => $pna_rotulo ) : ?>
						<label class="pna-opcao"><input class="pna-opcao__marcador" type="radio" name="pagamento" value="<?php echo esc_attr( $pna_v ); ?>" <?php checked( $pna_pag, $pna_v ); ?>> <?php echo esc_html( $pna_rotulo ); ?></label>
					<?php endforeach; ?>
					<?php if ( isset( $pna_erros['pagamento'] ) ) : ?>
						<p class="pna-campo__erro"><?php echo esc_html( $pna_erros['pagamento'] ); ?></p>
					<?php endif; ?>
				</fieldset>

				<?php
				pna_core_aceite( $pna_tipo, 'compromisso', __( '<strong>Estou ciente e me comprometo com a responsabilidade</strong> de realizar as doações mensais. Sei que posso cancelar o apadrinhamento a qualquer momento.', 'pna' ) );
				?>
			<?php endif; ?>

			<?php pna_core_aceite( $pna_tipo, 'privacidade', $pna_texto_priv ); ?>

			<div class="pna-form__acoes">
				<button type="submit" class="wp-element-button"><?php esc_html_e( 'Enviar', 'pna' ); ?></button>
				<a href="<?php echo esc_url( get_permalink( $pna_pet ) ); ?>"><?php esc_html_e( 'Cancelar e voltar', 'pna' ); ?></a>
			</div>
		</form>
	<?php endif; ?>
</div>
