# Integracao PSP

Esta integracao consome a API do Porto sem Papel (PSP) conforme o manual [`manual-servicos-psp-v202409.pdf`](/home/jlazzarotto/Downloads/manual-servicos-psp-v202409.pdf).

## Configuracao

Defina no ambiente do backend:

```dotenv
PSP_API_BASE_URL=https://seu-endpoint-psp
PSP_API_CHAVE=sua-chave
PSP_API_SENHA=sua-senha
```

O token JWT e obtido em:

```text
POST /psp-autenticacao-rest-integracao/api/autenticacao/chave-senha
```

O token e cacheado localmente em `backend/var/cache/psp-jwt.json` por 29 minutos.

## Endpoints expostos no backend

- `GET /api/v1/integracoes/psp/status`
- `GET /api/v1/integracoes/psp/duvs`
- `GET /api/v1/integracoes/psp/duvs/{numeroDuv}/resumo`
- `GET /api/v1/integracoes/psp/duvs/{numeroDuv}/embarcacao`
- `GET /api/v1/integracoes/psp/duvs/{numeroDuv}/anuencias`
- `GET /api/v1/integracoes/psp/duvs/{numeroDuv}/chegadas-saidas?v2=true`
- `GET /api/v1/integracoes/psp/duvs/{numeroDuv}/anexos`
- `GET /api/v1/integracoes/psp/cadastro/portos/{bitrigramaPorto}/locais-atracacao`
- `GET /api/v1/integracoes/psp/historico`

Esses endpoints usam a permissao `admin.importacao_dados.view`.

## Filtros suportados em `/duvs`

- `imo`
- `inscricao`
- `situacaoDuv`
- `nomeEmbarcacao`
- `natureza`
- `finalizado`
- `pagina`
- `porto`
- `retornarPendencia`

## Comando de validacao

```bash
php bin/console app:integracao:psp:status
```

## Observacoes

- A API do PSP exige credenciais previamente cadastradas no Porto sem Papel.
- O token informado pelo manual tem validade de 30 minutos.
- O manual anexado nao publica uma URL de sandbox/homologacao. Ele usa apenas `<url>`, entao a URL de homologacao e a de producao precisam ser fornecidas pelo proprio PSP no credenciamento.
- Os identificadores de dominio do PSP, como `numeroDuv`, `idPSPChegada`, `idPSPSaida` e `idPSPLocal`, devem ser obtidos pelas consultas do proprio PSP.
- As consultas agora geram histórico persistido em `psp_consultas_historico`, com endpoint, request, sucesso/erro, duracao e timestamp.
