<?php
/**
 * Ações do cabeçalho.
 *
 * Visitante: botões "Cadastro" e "Entrar".
 * Logado:    sino de notificações (com contador) e acesso ao perfil.
 *
 * O número de notificações vem do filtro 'pna_notificacoes_nao_lidas',
 * que será preenchido pelo plugin pna-core. Sem o plugin, o contador
 * simplesmente não aparece.
 *
 * @package PNA
 */

defined( 'ABSPATH' ) || exit;

$pna_wrapper = get_block_wrapper_attributes( array( 'class' => 'pna-header-acoes' ) );

if ( is_user_logged_in() ) :
	$pna_nao_lidas = (int) apply_filters( 'pna_notificacoes_nao_lidas', 0, get_current_user_id() );
	$pna_rotulo    = 0 < $pna_nao_lidas
		/* translators: %d: número de notificações não lidas. */
		? sprintf( _n( 'Notificações: %d não lida', 'Notificações: %d não lidas', $pna_nao_lidas, 'pna' ), $pna_nao_lidas )
		: __( 'Notificações', 'pna' );
	?>
	<div <?php echo $pna_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<a class="pna-header-acoes__sino" href="<?php echo esc_url( home_url( '/minhas-notificacoes/' ) ); ?>" aria-label="<?php echo esc_attr( $pna_rotulo ); ?>">
			<?php echo pna_icone( 'sino' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( 0 < $pna_nao_lidas ) : ?>
				<span class="pna-header-acoes__contador" aria-hidden="true"><?php echo esc_html( 99 < $pna_nao_lidas ? '99+' : (string) $pna_nao_lidas ); ?></span>
			<?php endif; ?>
		</a>
		<a class="pna-header-acoes__perfil" href="<?php echo esc_url( home_url( '/meu-perfil/' ) ); ?>" aria-label="<?php esc_attr_e( 'Meu perfil', 'pna' ); ?>">
			<?php echo pna_icone( 'usuario' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</div>
<?php else : ?>
	<div <?php echo $pna_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<a class="pna-header-acoes__botao pna-header-acoes__botao--contorno" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Cadastro', 'pna' ); ?></a>
		<a class="pna-header-acoes__botao pna-header-acoes__botao--cheio" href="<?php echo esc_url( wp_login_url( home_url( pna_caminho_atual() ? '/' . pna_caminho_atual() . '/' : '/' ) ) ); ?>"><?php esc_html_e( 'Entrar', 'pna' ); ?></a>
	</div>
<?php
endif;
