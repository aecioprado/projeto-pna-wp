# 0003 – Cadastro de pets com formulário próprio, sem editor de blocos

- **Status:** aceita
- **Data:** 27/09/2026

## Contexto

Um pet é um registro com dados estruturados (espécie, sexo, porte, idade, saúde, personalidade), cadastrado por voluntários com rotatividade. No editor de blocos, esses dados ficariam em painéis laterais pouco visíveis, e a página do pet poderia ser montada de formas diferentes a cada cadastro.

## Decisão

A tela de pets usa o **editor clássico**, com o quadro **"Dados do pet"** logo abaixo da descrição, onde todos os campos são listas de escolha. A aparência da página do pet é definida pelo tema (template), não por quem cadastra.

## Implicações

- Cadastro mais simples e padronizado: impossível esquecer um campo escondido ou quebrar o layout.
- A descrição aceita só texto formatado simples (sem blocos).
- Galeria de fotos extras ainda não existe (ver `docs/pendencias.md`).

## Como mudar no futuro

Remover o filtro `pna_core_pets_sem_editor_de_blocos` em `plugins/pna-core/includes/pets.php` e levar os campos para um painel do editor de blocos (exige JavaScript com etapa de build).
