# PNA – Pets no Agreste

Site do projeto de extensão **Pets no Agreste** (CAA – UFPE, Caruaru): galeria de pets para adoção, área logada com processo de adoção e apadrinhamento.

Este repositório guarda **somente o código próprio** (tema e plugin). O WordPress é criado automaticamente no seu computador pelo [wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) e nunca entra no Git.

## Estrutura

```
projeto-pna-wp/
├── .wp-env.json          # define o WordPress local (versão, PHP, tema, plugins)
├── package.json          # atalhos: npm start, npm run setup...
├── CLAUDE.md             # convenções do projeto para IA e desenvolvedores
├── docs/                 # decisões (docs/decisoes), design tokens, manual do admin
├── plugins/              # pna-core (regras de negócio) – a criar
├── scripts/              # scripts de apoio (ex.: conteúdo inicial)
└── themes/pna-theme/     # tema de blocos (visual)
    ├── theme.json        # design system: cores, fontes, espaçamentos
    ├── style.css         # identificação do tema
    ├── functions.php
    ├── assets/           # css, fontes (Poppins) e ícones SVG
    ├── blocks/           # blocos próprios (ações do cabeçalho, breadcrumb)
    ├── inc/              # funções PHP de apoio
    ├── parts/            # cabeçalho e rodapé
    ├── patterns/         # componentes reutilizáveis
    └── templates/        # modelos de página
```

## Requisitos

- [Git](https://git-scm.com/)
- [Node.js](https://nodejs.org/) versão LTS
- Docker:
  - **Linux (Ubuntu):** [Docker Engine](https://docs.docker.com/engine/install/ubuntu/). Depois de instalar, adicione seu usuário ao grupo do Docker (uma vez só) e faça logout/login: `sudo usermod -aG docker $USER`. Confira com `docker info`.
  - **Mac ou Windows:** [Docker Desktop](https://www.docker.com/products/docker-desktop/), aberto e rodando (no Windows, com WSL 2).

> No Linux, **nunca** use `sudo` com os comandos do projeto (`sudo npm start`). Isso cria arquivos do usuário root que depois nem você nem o WordPress conseguem editar.

## Primeira execução

```bash
git clone https://github.com/aecioprado/projeto-pna-wp.git
cd projeto-pna-wp
npm install
npm start          # sobe o WordPress (a 1ª vez demora alguns minutos)
npm run setup      # português, fuso de Recife, tema PNA e links amigáveis
npm run conteudo   # páginas do menu, página inicial e cadastro aberto
```

O `npm run conteudo` pode ser executado de novo a qualquer momento: o que já existe não é duplicado.

Para ver o logo no cabeçalho, envie a imagem em **Aparência → Editor → Padrões → Cabeçalho**, clicando no bloco de logo.

Acesse:

- Site: http://localhost:8888
- Painel: http://localhost:8888/wp-admin — usuário `admin`, senha `password` (apenas local)

## Comandos do dia a dia

| Comando | O que faz |
|---|---|
| `npm start` | Liga o ambiente |
| `npm stop` | Desliga o ambiente |
| `npm run conteudo` | Cria as páginas básicas (sem duplicar) |
| `npm run wp -- <comando>` | Roda WP-CLI. Ex.: `npm run wp -- plugin list` |
| `npm run logs` | Mostra os logs do WordPress |
| `npm run reset` | Apaga o banco local e recomeça do zero |
| `npm run destroy` | Remove o ambiente inteiro (containers e dados) |

**Quando reiniciar o ambiente:** mudanças em arquivos do tema aparecem na hora (basta recarregar o navegador). Só é preciso `npm stop` e `npm start` depois de alterar o `.wp-env.json` ou de apagar/recriar pastas do projeto (veja "Problemas comuns").

Erros de PHP ficam no log de debug (`WP_DEBUG_LOG`) e no plugin **Query Monitor**, instalado só no ambiente local.

## Problemas comuns

### "Folha de estilos em falta" (ou o site mostra erros sem motivo aparente)

**Causa:** uma pasta do projeto foi apagada e recriada com o ambiente ligado (por exemplo, ao extrair um zip escolhendo "Substituir" a pasta). No Linux, o container do servidor continua preso à pasta antiga, agora vazia.

**Como confirmar:** compare a pasta do tema vista pelo servidor com a vista no seu computador:

```bash
npx wp-env run wordpress ls -la wp-content/themes/pna-theme
ls -la themes/pna-theme
```

Se a primeira vier vazia e a segunda completa, é esse o caso. (O container `cli` não serve para o teste: ele é recriado a cada comando e sempre enxerga a pasta atual.)

**Correção:** `npm stop` e depois `npm start`. O banco de dados não é afetado.

**Prevenção:** extraia zips pelo terminal, que mescla em vez de substituir: `unzip -o arquivo.zip -d .` (instale com `sudo apt install unzip`, se preciso).

### `permission denied` ao rodar `npm start` no Linux

Seu usuário não está no grupo do Docker. Rode `sudo usermod -aG docker $USER`, faça logout e login, e tente de novo.

## Fluxo de trabalho no Git

1. Nunca trabalhe direto na `main`. Crie um branch por tarefa: `git switch -c feat/tokens-theme-json`
2. Faça commits pequenos e descritivos.
3. Abra um Pull Request para a `main` e revise antes de mesclar.

Prefixos de branch: `feat/` (funcionalidade), `fix/` (correção), `docs/` (documentação), `chore/` (configuração).

## Regras de segurança (repositório público)

- **Nunca** versione `wp-config.php`, senhas, chaves de API ou arquivos `.env`.
- **Nunca** versione banco de dados (`.sql`) ou uploads: contêm dados pessoais de adotantes (LGPD).
- Configurações pessoais do ambiente vão em `.wp-env.override.json` (já ignorado pelo Git).
