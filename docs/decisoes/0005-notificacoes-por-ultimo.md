# 0005 – Notificações implementadas por último e validadas na hospedagem

- **Status:** aceita
- **Data:** 27/09/2026
- **Decidido por:** responsável pelo projeto

## Contexto

As notificações do PNA incluem a página "Minhas notificações", o contador do sino no cabeçalho e os e-mails enviados a cada mudança de status (pedido recebido, aprovado, recusado, instruções de pagamento do apadrinhamento, pet adotado para os padrinhos).

O envio de e-mails depende da configuração da hospedagem (servidor SMTP, domínio, autenticação SPF/DKIM), que não existe no ambiente local.

## Decisão

As notificações são a **última fase de implementação**, feita e validada **já no ambiente da hospedagem**, com o envio de e-mail real configurado.

## Implicações

- Até lá, o Membro acompanha seus pedidos pela seção **"Minhas solicitações"** em Meu perfil, que mostra o status atualizado de cada um.
- O sino do cabeçalho já existe, mas sem contador, e a página "Minhas notificações" continua vazia.
- "Esqueci minha senha" depende de e-mail: só funciona de verdade na hospedagem.
- O plugin já dispara as ações `pna_core_solicitacao_criada`, `pna_core_solicitacao_status_alterado` e `pna_core_pet_status_alterado`: a fase de notificações só precisa "ouvir" essas ações, sem mexer no fluxo existente.

## Ordem das fases a partir daqui

1. Fase 7 – Área logada (cadastro, login, perfil, formulários).
2. Fase 8 – Revisão geral, acessibilidade, LGPD e publicação num ambiente de testes na hospedagem.
3. Fase 9 – Notificações (site e e-mail), validadas na hospedagem.
4. Fase 10 – Entrega: produção, manual do administrador e remoção dos acessos do desenvolvedor.
