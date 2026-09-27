# Publicação na HostGator (passo a passo)

Guia para colocar o site do PNA no ar na hospedagem **HostGator, Plano P**, com **WordPress 7.1.2** já instalado. Foi escrito para quem tem pouca experiência com WordPress: siga na ordem, sem pular etapas.

Não há ambiente de testes separado ([decisão 0007](decisoes/0007-sem-ambiente-de-testes.md)). Em vez disso, o site fica **fechado ao público** (página "Em breve") até você decidir lançá-lo, e **cada atualização começa com um backup**.

> **Tempo estimado:** de 1h30 a 2h para a primeira publicação (partes 1 a 5). O cadastro do conteúdo real (parte 6) depende da quantidade de pets e textos.

---

## Antes de começar

Tenha em mãos:

- O login do **Portal do Cliente da HostGator** e acesso ao **cPanel**.
- Um usuário **administrador** do WordPress do site.
- O seu computador com o projeto (o mesmo onde você roda `npm start`).

Alguns termos usados neste guia:

| Termo | O que é |
|---|---|
| **cPanel** | O painel da hospedagem, onde se configuram arquivos, e-mail, segurança e backups. |
| **Painel do WordPress** | A área administrativa do site, em `https://seudominio/wp-admin`. |
| **Tema** | O visual do site (`pna-theme`). |
| **Plugin** | As funções do PNA: pets, pedidos, cadastro (`pna-core`). |
| **Pacote (.zip)** | O arquivo do tema ou do plugin, pronto para enviar ao WordPress. |

---

## Parte 1 – Gerar os pacotes no seu computador (5 min)

1. Abra o terminal na pasta do projeto e garanta que está com a versão mais recente:

   ```bash
   git switch main
   git pull
   ```

2. Gere os pacotes:

   ```bash
   npm run pacote
   ```

3. **O que você deve ver:** a mensagem "Pacotes gerados:" com dois arquivos na pasta `dist/` do projeto, algo como `pna-theme-0.8.0.zip` e `pna-core-0.5.0.zip`.

4. **Guarde uma cópia** desses dois arquivos numa pasta fora do projeto (ex.: `Documentos/PNA/versoes/`). Se uma atualização futura der errado, é com eles que você volta atrás.

> Se aparecer "O comando 'zip' não está instalado", rode `sudo apt install zip` e repita o passo 2.

---

## Parte 2 – Preparar a hospedagem no cPanel (20 min)

Entre no cPanel pelo Portal do Cliente da HostGator (Hospedagens → seu plano → **cPanel**). Em todos os passos abaixo, use a **barra de busca do cPanel** para encontrar as ferramentas pelo nome.

### 2.1 Fazer um backup completo

Mesmo com o site vazio, crie o hábito: **backup antes de qualquer mudança**.

1. Busque por **Backup** e abra a ferramenta.
2. Em "Backup completo", clique em **Gerar backup completo** e depois em **Gerar backup**.
3. Aguarde o e-mail ou o aviso de conclusão. O Plano P guarda **apenas um** backup manual por vez; um novo substitui o anterior.

A HostGator também faz **backups automáticos diários** do Plano P. Para restaurar um deles, use o Portal do Cliente (Hospedagens → **Backup**) ou peça ao suporte.

### 2.2 Conferir o cadeado de segurança (HTTPS)

O site tem login e dados pessoais: o HTTPS é obrigatório.

1. Abra o site no navegador digitando `https://` antes do domínio.
2. **O que você deve ver:** o cadeado ao lado do endereço, sem alertas de segurança.
3. **Se aparecer alerta:** no cPanel, busque por **SSL/TLS Status**, selecione o domínio e clique em **Run AutoSSL**. Aguarde alguns minutos e teste de novo. O domínio precisa estar apontado para a HostGator.

### 2.3 Conferir a versão do PHP

O projeto exige PHP 8.1 ou mais recente e foi testado com o **PHP 8.2**.

1. Busque por **PHP** (a ferramenta costuma se chamar **MultiPHP Manager** ou "Selecionar versão do PHP").
2. Marque o domínio do site e escolha **PHP 8.2** (ou 8.3). Clique em **Aplicar**.

### 2.4 Proteger os arquivos do site

Esta configuração impede que alguém que invada o painel do WordPress altere o código do site.

1. Busque por **Gerenciador de Arquivos** e abra a pasta **public_html**.
2. Encontre o arquivo **wp-config.php**. Clique com o botão direito → **Download**, para guardar uma cópia de segurança dele.
3. Clique de novo com o botão direito → **Edit** (Editar).
4. Procure a linha que diz `/* That's all, stop editing! Happy publishing. */` (ou "Isto é tudo, pare de editar!").
5. **Logo acima** dela, acrescente esta linha:

   ```php
   define( 'DISALLOW_FILE_EDIT', true );
   ```

6. Clique em **Save Changes** (Salvar alterações).
7. **O que você deve ver:** o site continua abrindo normalmente. No painel do WordPress, o menu Aparência não mostra mais o "Editor de arquivos de tema".

> **Se o site mostrar uma tela branca ou de erro:** volte ao Gerenciador de Arquivos e envie (Upload) a cópia do `wp-config.php` que você baixou no passo 2, substituindo o arquivo. Depois confira se a linha foi colada sem alterações.

---

## Parte 3 – Preparar o WordPress (20 min)

Entre no painel do WordPress: `https://seudominio/wp-admin`.

### 3.1 Idioma

**Configurações → Geral → Idioma do site** → escolha **Português do Brasil** → **Salvar alterações**.

### 3.2 Esconder o site dos buscadores até o lançamento

**Configurações → Leitura** → marque **"Evitar que mecanismos de busca indexem este site"** → **Salvar alterações**.

### 3.3 Organizar os administradores

Siga a matriz de acessos definida no início do projeto:

1. Em **Usuários**, confira quem é Administrador. Cada pessoa deve ter **o seu próprio usuário**, com e-mail institucional. Nenhum usuário deve se chamar `admin`.
2. Para ativar a **verificação em duas etapas**: Plugins → Adicionar novo → busque **"Two Factor"** (autor: WordPress.org Contributors) → Instalar → Ativar. Depois, cada administrador ativa em **Usuários → Perfil**, na seção "Two-Factor Options".

### 3.4 Remover o que veio instalado e não será usado

A instalação do WordPress da HostGator costuma vir com plugins e temas extras (por exemplo, WooCommerce e plugins de marketing). Eles deixam o site mais lento e aumentam o que precisa ser atualizado.

1. Em **Plugins**, **anote** a lista do que está instalado.
2. Desative e exclua os que não fazem parte do projeto. Na dúvida sobre algum (especialmente os de segurança ou cache da HostGator), **mantenha** e pergunte ao suporte ou ao desenvolvedor.
3. Em **Aparência → Temas**, os temas extras podem ser excluídos **depois** de ativar o tema do PNA (Parte 4).

---

## Parte 4 – Instalar o tema e o plugin (15 min)

A ordem importa: **primeiro o tema, depois o plugin**.

### 4.1 Tema

1. **Aparência → Temas → Adicionar novo tema → Enviar tema**.
2. Clique em **Escolher arquivo**, selecione `pna-theme-<versão>.zip` (o arquivo `.zip` em si, **sem descompactar**) e clique em **Instalar agora**.
3. Clique em **Ativar**.
4. **O que você deve ver:** ao abrir o site, a faixa verde no topo com o menu, e o rodapé verde. Por enquanto, o conteúdo fica vazio.

### 4.2 Plugin

1. **Plugins → Adicionar novo plugin → Enviar plugin**.
2. Selecione `pna-core-<versão>.zip`, clique em **Instalar agora** e depois em **Ativar plugin**.
3. **O que você deve ver:** os menus **Pets** e **Solicitações** no painel, e o aviso **"🔒 Site fechado ao público"** na barra preta do topo.

### 4.3 Conferir que o site está fechado

Abra o site numa **janela anônima** (Ctrl+Shift+N no Chrome).

**O que você deve ver:** apenas a página **"Em breve"**, com o link "Entrar (equipe do PNA)". É isso que o público verá até o lançamento. Logado, você vê o site completo.

### 4.4 Configuração inicial

1. **Ferramentas → PNA: configuração inicial** → **Executar configuração inicial**.
2. **O que você deve ver:** uma lista do que foi feito (páginas criadas, página inicial definida, cadastro aberto, rascunho da Política de Privacidade) e a tabela "Situação atual".
3. Na tabela, é esperado ver ⚠️ em "Política de Privacidade publicada", "Site aberto ao público" e "Mecanismos de busca liberados". Esses itens são resolvidos nas partes 6 e 7.

> **Se as páginas abrirem com "Página não encontrada":** vá em **Configurações → Links permanentes** e clique em **Salvar alterações**, sem mudar nada. Isso refaz os endereços do site.

---

## Parte 5 – Testar com o site fechado (30 min)

Siga o roteiro de validação das fases 6 e 7, agora na hospedagem. Com o site fechado, só quem está logado consegue testar.

1. **Crie um membro de teste:** Usuários → Adicionar novo usuário → preencha usuário e e-mail → em **Função**, escolha **Membro** → defina uma senha → Adicionar.
2. Numa janela anônima, entre em `https://seudominio/entrar/` com esse membro e teste:
   - a galeria de pets e os filtros (cadastre antes 2 ou 3 pets de teste, ver Parte 6);
   - um pedido de adoção e um de apadrinhamento;
   - o acompanhamento em Meu perfil.
3. No painel, como administrador, analise os pedidos em **Solicitações** e confira se o status muda no perfil do membro.
4. **Ao terminar:** exclua as solicitações de teste (Solicitações → Lixeira), os pets de teste e o membro de teste (Usuários).

> Os e-mails automáticos ainda não existem: eles são a Fase 9, validada neste mesmo site antes do lançamento ([decisão 0005](decisoes/0005-notificacoes-por-ultimo.md)).

---

## Parte 6 – Conteúdo real

Pode ser feito aos poucos, pela equipe, com o site ainda fechado.

| O quê | Onde | Observação |
|---|---|---|
| **Pets** | Pets → Adicionar pet | Nome, descrição, "Dados do pet" e **Foto principal** (quadro à direita). Fotos na horizontal ou quadradas, com pelo menos 800 px de largura. |
| **Imagem da página inicial** | Páginas → Página inicial → Imagem destacada | Aparece ao lado do contador de adoções. |
| **Logo** | Aparência → Editor → Padrões → Partes de modelo → Cabeçalho → bloco do logo | Quando o logo chegar, peça ao desenvolvedor para remover o link provisório "Página inicial" do menu. |
| **Postagens** | Posts → Adicionar novo | Sempre com **imagem destacada**, que aparece no card. |
| **Dúvidas e Doações** | Páginas | Revise os textos; em Doações, informe a **chave Pix**. |
| **Política de Privacidade** | Páginas → Política de Privacidade | Complete os trechos entre [colchetes], apague o aviso vermelho e publique. Depois confira em **Configurações → Privacidade** se é a página selecionada. |
| **Equipe** | Usuários → Adicionar novo | Função **Gestor PNA** (coordenação, aprova pedidos) ou **Avaliador PNA** (voluntários, analisa pedidos). |

---

## Parte 7 – Lançamento (10 min)

Quando o conteúdo estiver pronto e as notificações (Fase 9) validadas:

1. Faça um **backup completo** (Parte 2.1).
2. **Ferramentas → PNA: configuração inicial** → **Desligar e abrir o site ao público**.
3. **Configurações → Leitura** → **desmarque** "Evitar que mecanismos de busca indexem este site" → Salvar.
4. Numa janela anônima, confira se o site abre normalmente para visitantes.
5. Confira se a tabela "Situação atual" está toda com ✅.

---

## Atualizações futuras

Sempre que houver uma nova versão do tema ou do plugin no repositório:

1. **No computador:** valide tudo no ambiente local (é o único ambiente de testes), faça o merge na `main` e rode `git switch main`, `git pull` e `npm run pacote`. Guarde os `.zip` novos junto com os anteriores.
2. **Backup completo** no cPanel (Parte 2.1).
3. **Envie o pacote novo** pelo mesmo caminho da instalação (Parte 4). O WordPress vai mostrar uma comparação entre a versão atual e a nova: clique em **Substituir a atual pela enviada**.
4. Rode **Ferramentas → PNA: configuração inicial** de novo. É seguro repetir, e isso garante que páginas novas sejam criadas.
5. Abra o site e confira as páginas principais: início, pets, um pet, postagens, entrar e meu perfil.

Prefira fazer atualizações em horário de pouco movimento.

### Se uma atualização der errado

1. **Primeiro recurso:** reenvie o `.zip` da **versão anterior** (guardado no passo 1) do mesmo jeito, escolhendo "Substituir a atual pela enviada".
2. **Se não resolver:** restaure o backup feito antes da atualização, pelo cPanel (Backup) ou pelo Portal do Cliente. Na dúvida, o suporte da HostGator atende 24h.

---

## Problemas comuns

| Sintoma | Causa provável | Solução |
|---|---|---|
| Página "Em breve" aparece mesmo logado | Sessão expirada | Entre de novo em `/entrar/`. |
| Endereços dão "Página não encontrada" | Links permanentes desatualizados | Configurações → Links permanentes → Salvar alterações. |
| O visual antigo continua aparecendo depois de uma atualização | Cache do navegador ou da CDN da HostGator | Recarregue com Ctrl+Shift+R. Se persistir, limpe o cache da CDN pelo Portal do Cliente ou peça ao suporte. |
| "O link que você seguiu expirou" ao enviar o `.zip` | Limite de tamanho de envio | Confira se enviou o arquivo certo (os pacotes têm menos de 2 MB). Se o erro continuar, peça ao suporte para aumentar o `upload_max_filesize`. |
| "O pacote não pôde ser instalado. O tema não tem a folha de estilo style.css" | Foi enviado um `.zip` errado ou descompactado e recompactado | Envie o arquivo gerado pelo `npm run pacote`, exatamente como está na pasta `dist/`. |
| Não consigo entrar pelo `/entrar/` | Problema na página de login | Use o login padrão do WordPress: `https://seudominio/wp-login.php`. |
