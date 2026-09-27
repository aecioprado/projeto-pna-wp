# PNA – Pets no Agreste

Site do projeto de extensão **Pets no Agreste** (CAA – UFPE, Caruaru): galeria de pets para adoção, área logada com processo de adoção e apadrinhamento.

Este repositório guarda **somente o código próprio** (tema e plugin). O WordPress é criado automaticamente no seu computador pelo [wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) e nunca entra no Git.

## Estrutura

```
projeto-pna-wp/
├── .wp-env.json          # define o WordPress local (versão, PHP, tema, plugins)
├── package.json          # atalhos: npm start, npm run setup...
├── CLAUDE.md             # convenções do projeto para IA e desenvolvedores
├── docs/                 # decisões, design tokens, manual do admin
├── plugins/              # pna-core (regras de negócio) – a criar
└── themes/pna-theme/     # tema de blocos (visual)
    ├── theme.json        # design system: cores, fontes, espaçamentos
    ├── style.css         # identificação do tema
    ├── functions.php
    ├── assets/           # css, fontes (Poppins) e ícones SVG
    ├── parts/            # cabeçalho e rodapé
    ├── patterns/         # componentes reutilizáveis
    └── templates/        # modelos de página
```

## Requisitos

- [Git](https://git-scm.com/)
- [Node.js](https://nodejs.org/) versão LTS
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) aberto e rodando (no Windows, com WSL 2)

## Primeira execução

```bash
git clone https://github.com/aecioprado/projeto-pna-wp.git
cd projeto-pna-wp
npm install
npm start          # sobe o WordPress (a 1ª vez demora alguns minutos)
npm run setup      # português, fuso de Recife, tema PNA e links amigáveis
```

Acesse:

- Site: http://localhost:8888
- Painel: http://localhost:8888/wp-admin — usuário `admin`, senha `password` (apenas local)

## Comandos do dia a dia

| Comando | O que faz |
|---|---|
| `npm start` | Liga o ambiente |
| `npm stop` | Desliga o ambiente |
| `npm run wp -- <comando>` | Roda WP-CLI. Ex.: `npm run wp -- plugin list` |
| `npm run logs` | Mostra os logs do WordPress |
| `npm run reset` | Apaga o banco local e recomeça do zero |
| `npm run destroy` | Remove o ambiente inteiro (containers e dados) |

Erros de PHP ficam no log de debug (`WP_DEBUG_LOG`) e no plugin **Query Monitor**, instalado só no ambiente local.

## Fluxo de trabalho no Git

1. Nunca trabalhe direto na `main`. Crie um branch por tarefa: `git switch -c feat/tokens-theme-json`
2. Faça commits pequenos e descritivos.
3. Abra um Pull Request para a `main` e revise antes de mesclar.

Prefixos de branch: `feat/` (funcionalidade), `fix/` (correção), `docs/` (documentação), `chore/` (configuração).

## Regras de segurança (repositório público)

- **Nunca** versione `wp-config.php`, senhas, chaves de API ou arquivos `.env`.
- **Nunca** versione banco de dados (`.sql`) ou uploads: contêm dados pessoais de adotantes (LGPD).
- Configurações pessoais do ambiente vão em `.wp-env.override.json` (já ignorado pelo Git).
