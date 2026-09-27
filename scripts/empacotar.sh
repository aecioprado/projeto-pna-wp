#!/usr/bin/env bash
#
# Gera os pacotes .zip do tema e do plugin, prontos para instalar pelo
# painel do WordPress (Aparência → Temas → Adicionar novo → Enviar tema;
# Plugins → Adicionar novo → Enviar plugin).
#
# Uso: npm run pacote
# Resultado: dist/pna-theme-<versão>.zip e dist/pna-core-<versão>.zip
#
set -euo pipefail
cd "$(dirname "$0")/.."

if ! command -v zip >/dev/null 2>&1; then
	echo "O comando 'zip' não está instalado. No Ubuntu: sudo apt install zip"
	exit 1
fi

versao_tema=$(grep -m1 '^Version:' themes/pna-theme/style.css | awk '{print $2}')
versao_plugin=$(grep -m1 'Version:' plugins/pna-core/pna-core.php | awk '{print $NF}')

mkdir -p dist
rm -f "dist/pna-theme-${versao_tema}.zip" "dist/pna-core-${versao_plugin}.zip"

excluir=( '*/.DS_Store' '*/node_modules/*' '*/.git*' )

( cd themes && zip -qr "../dist/pna-theme-${versao_tema}.zip" pna-theme -x "${excluir[@]}" )
( cd plugins && zip -qr "../dist/pna-core-${versao_plugin}.zip" pna-core -x "${excluir[@]}" )

echo "Pacotes gerados:"
ls -lh dist/pna-theme-"${versao_tema}".zip dist/pna-core-"${versao_plugin}".zip
