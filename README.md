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
├── plugins/pna-core/     # regras de negócio: pets, solicitações, papéis
├── scripts/              # scripts de apoio (ex.: conteúdo inicial)
└── themes/pna-theme/     # tema de blocos (visual)
    ├── theme.json        # design system: cores, fontes, espaçamentos
    ├── style.css         # identificação do tema
    ├── functions.php
    ├── assets/           # css (base + componentes), fontes (Poppins) e ícones SVG
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
npm run conteudo   # páginas, postagens, pets e solicitações de exemplo, guia de estilo
```

O `npm run conteudo` pode ser executado de novo a qualquer momento: o que já existe não é duplicado.

O guia de estilo (página privada, visível só para administradores) fica em http://localhost:8888/guia-de-estilo/ e mostra todos os componentes do tema.

Para ver o logo no cabeçalho, envie a imagem em **Aparência → Editor → Padrões → Cabeçalho**, clicando no bloco de logo.

Acesse:

- Site: http://localhost:8888
- Painel: http://localhost:8888/wp-admin — usuário `admin`, senha `password` (apenas local)
- Membro de exemplo (criado pelo `npm run conteudo`): e-mail `membro.teste@exemplo.local` ou usuário `membro.teste`, senha `senha-local-123` (apenas local)
- Área logada: http://localhost:8888/entrar/, /cadastro/ e /meu-perfil/. Se algo der errado com essas páginas, o login padrão continua em http://localhost:8888/wp-login.php

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

**Quando reiniciar o ambiente:** mudanças em arquivos do tema aparecem na hora (basta recarregar o navegador). Rode `npm stop` e `npm start` depois de:

- alterar o `.wp-env.json`;
- `git switch` ou `git pull` que tragam ou removam pastas;
- apagar ou recriar pastas do projeto (por exemplo, ao extrair um zip).

Na dúvida, reinicie: leva poucos segundos e não afeta o banco de dados. Veja "Problemas comuns".

O ambiente local roda com `WP_DEVELOPMENT_MODE` = `theme` (no `.wp-env.json`), que desliga os caches do tema: padrões e templates novos aparecem na hora.

Erros de PHP ficam no log de debug (`WP_DEBUG_LOG`) e no plugin **Query Monitor**, instalado só no ambiente local.

## Problemas comuns

### "Folha de estilos em falta" (ou o site mostra erros sem motivo aparente)

**Causa:** uma pasta do projeto foi apagada e recriada com o ambiente ligado. Acontece ao extrair um zip escolhendo "Substituir" a pasta, e também com `git switch`/`git pull`: se um branch não tem uma pasta que o outro tem, o Git apaga e depois recria essa pasta. No Linux, os containers continuam presos à pasta antiga, agora vazia.

**Como confirmar:** compare a pasta do tema vista pelo servidor com a vista no seu computador:

```bash
npx wp-env run wordpress ls -la wp-content/themes/pna-theme
ls -la themes/pna-theme
```

Se a primeira vier vazia e a segunda completa, é esse o caso. O mesmo pode acontecer com o container `cli` (usado pelos comandos `npm run wp` e `npm run conteudo`): troque `wordpress` por `cli` no primeiro comando para testar.

**Correção:** `npm stop` e depois `npm start`. O banco de dados não é afetado.

**Prevenção:** extraia zips pelo terminal, que mescla em vez de substituir: `unzip -o arquivo.zip -d .` (instale com `sudo apt install unzip`, se preciso).

### `npm run conteudo` diz que o arquivo "does not exist"

É o mesmo caso acima, com a pasta `scripts/`. Correção: `npm stop` e `npm start`.

### Padrão novo não aparece (página em branco ou categoria "PNA" vazia no editor)

**Causa:** o WordPress guarda num cache a lista de padrões do tema e só a atualiza quando a versão do tema (em `style.css`) muda. No ambiente local isso não deve acontecer, porque o `WP_DEVELOPMENT_MODE` desliga o cache; se acontecer, confira se essa linha está no `.wp-env.json` e se o ambiente foi reiniciado depois.

**Como confirmar** (troque pelo nome do padrão):

```bash
npm run wp -- eval 'var_dump( WP_Block_Patterns_Registry::get_instance()->is_registered( "pna/guia-de-estilo" ) );'
```

`bool(false)` confirma o problema.

**Correção:**

```bash
npm run wp -- eval 'wp_get_theme()->delete_pattern_cache();'
```

Atenção: `wp transient delete --all` **não** resolve, porque esse cache é de outro tipo ("site transient").

**Na produção:** aumente a versão do tema a cada publicação (veja "Publicação").

### Página de formulário mostra "Página não encontrada"

Se um endereço com parâmetro (ex.: `/adotar/?pet_id=17`) cai na página 404, confira se o nome do parâmetro não coincide com uma variável do WordPress (como `pet`, `especie`, `name`, `page`). Veja a regra no `CLAUDE.md`.

### `permission denied` ao rodar `npm start` no Linux

Seu usuário não está no grupo do Docker. Rode `sudo usermod -aG docker $USER`, faça logout e login, e tente de novo.

## Fluxo de trabalho no Git

1. Nunca trabalhe direto na `main`. Crie um branch por tarefa: `git switch -c feat/tokens-theme-json`
2. Faça commits pequenos e descritivos.
3. Abra um Pull Request para a `main` e revise antes de mesclar.

Prefixos de branch: `feat/` (funcionalidade), `fix/` (correção), `docs/` (documentação), `chore/` (configuração).

## Publicação

A cada publicação na produção, **aumente a versão do tema** no cabeçalho de `themes/pna-theme/style.css` (ex.: `0.3.0` → `0.4.0`). Isso faz o WordPress atualizar o cache de padrões e os navegadores baixarem o CSS novo. Sem isso, padrões novos podem não aparecer no site publicado.

## Regras de segurança (repositório público)

- **Nunca** versione `wp-config.php`, senhas, chaves de API ou arquivos `.env`.
- **Nunca** versione banco de dados (`.sql`) ou uploads: contêm dados pessoais de adotantes (LGPD).
- Configurações pessoais do ambiente vão em `.wp-env.override.json` (já ignorado pelo Git).
