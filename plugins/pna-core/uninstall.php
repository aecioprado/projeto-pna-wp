<?php
/**
 * Executado quando o plugin é EXCLUÍDO pelo painel (não na desativação).
 *
 * Remove apenas os papéis criados pelo plugin. Pets e solicitações
 * NÃO são apagados: são dados do projeto e de pessoas (LGPD), e sua
 * exclusão deve ser uma decisão consciente, feita pelo painel.
 *
 * @package PNA_Core
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

foreach ( array( 'pna_membro', 'pna_avaliador', 'pna_gestor' ) as $pna_papel ) {
	remove_role( $pna_papel );
}

if ( 'pna_membro' === get_option( 'default_role' ) ) {
	update_option( 'default_role', 'subscriber' );
}

delete_option( 'pna_core_versao_papeis' );
