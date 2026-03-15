# Task VNext: Auditoria Rica para Compliance

## Título
Ampliar a trilha de auditoria do backend para nível enterprise/compliance.

## Contexto
O sistema já registra auditoria com:
- `company_id`
- `empresa_id`
- `unidade_id`
- `user_id`
- `request_id`
- path, método e IP
- payload resumido da ação

Hoje isso cobre rastreabilidade operacional básica, mas ainda não atende plenamente cenários de compliance enterprise, governança forte e investigação detalhada.

## Objetivo
Evoluir a auditoria para registrar contexto, mudança e correlação suficientes para:
- rastrear ações críticas com precisão
- sustentar revisão operacional e auditoria externa
- investigar incidentes, divergências e automações
- preservar evidência de alteração em operações sensíveis

## Motivação
- fortalecer compliance e governança
- melhorar capacidade forense
- suportar revisões de BPO financeiro com mais evidência
- preparar o produto para clientes enterprise com exigência de trilha expandida

## Escopo
### Incluído
- enriquecer o modelo de auditoria
- capturar estado anterior/posterior quando aplicável
- registrar motivo/origem da operação quando houver
- correlacionar auditoria com evento de domínio, integração e automação
- definir padrão de auditoria para ações críticas
- revisar payloads para evitar excesso ou falta de informação

### Não incluído
- SIEM externo
- retenção legal avançada
- criptografia de evidência em storage externo
- trilha de aprovação em ferramenta third-party

## Estado Atual
Pontos já existentes:
- [`AuditoriaLogger.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Shared/Infrastructure/Auditoria/AuditoriaLogger.php)
- [`AuditoriaLog.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Company/Domain/Entity/AuditoriaLog.php)
- [`RequestIdListener.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Shared/Infrastructure/Http/RequestIdListener.php)

## Problema Atual
A trilha atual ainda não cobre de forma estruturada:
- `before` e `after` da alteração
- motivo da ação
- origem da ação: manual, integração, automação, webhook, job
- correlação com evento de domínio
- correlação com integração externa
- identificador funcional do agregado afetado
- classificação da criticidade da ação

## Requisitos Funcionais
1. Toda ação crítica deve registrar contexto suficiente para auditoria posterior.
2. Operações de criação, alteração, baixa, aprovação, conciliação e integração devem ter padrão consistente.
3. Quando houver mutação de estado, deve ser possível recuperar o antes e o depois.
4. Auditorias ligadas a integração/webhook devem registrar correlação funcional.
5. O modelo deve suportar filtros por `company`, `empresa`, `unidade`, usuário, recurso, ação, criticidade e período.

## Requisitos Técnicos
1. Não mover regra de negócio para controller.
2. Centralizar a estratégia em infraestrutura/shared.
3. Evitar duplicação manual de montagem de auditoria em cada service.
4. Permitir uso incremental, sem refatoração massiva imediata de todos os módulos.
5. Preservar performance e evitar payload excessivo ou sensível demais.

## Proposta de Evolução
### Modelo de dados
Adicionar, no mínimo, suporte estruturado para:
- `aggregate_type`
- `aggregate_id`
- `criticidade`
- `origem`
- `motivo`
- `before_json`
- `after_json`
- `event_name`
- `integration_name`
- `correlation_id`

### Logger/infra
- evoluir `AuditoriaLogger` para aceitar contexto expandido
- criar DTO ou value object de auditoria para evitar assinatura frágil
- padronizar helpers para operações de:
  - criação
  - atualização
  - exclusão lógica
  - aprovação
  - baixa
  - conciliação
  - webhook

### Padrão de uso
- ações simples continuam podendo registrar auditoria mínima
- ações críticas passam a exigir auditoria rica
- adotar convenção gradual por módulo

## Ações Críticas Prioritárias
- criação e alteração de usuário/perfil
- criação e baixa de título
- aprovação de título
- conciliação bancária
- geração/importação de boleto
- criação/recebimento PIX
- criação de regra automática
- criação de lançamento contábil

## Critérios de Aceite
1. O modelo de auditoria suporta `before`/`after`.
2. É possível diferenciar origem manual, webhook, integração e automação.
3. Ações críticas prioritárias usam o modelo enriquecido.
4. O sistema preserva `request_id` e adiciona `correlation_id` quando aplicável.
5. A consulta futura de auditoria consegue filtrar por contexto organizacional e criticidade.
6. `lint:container`, `schema:validate` e testes continuam passando.

## Riscos
- registrar payload demais e criar ruído operacional
- gravar dado sensível indevido em auditoria
- aumentar acoplamento se cada service montar auditoria de forma própria
- crescer custo de storage sem política mínima de retenção

## Estratégia Recomendada
Implementar em duas etapas:

### Etapa 1
- evolução do schema e `AuditoriaLogger`
- suporte a `before/after`, criticidade, origem e correlação
- adoção nas ações mais críticas

### Etapa 2
- padronização por mais módulos
- endpoint/consulta administrativa de auditoria
- diretrizes de retenção e mascaramento

## Dependências
- definição do conjunto exato de campos auditáveis
- política mínima para dados sensíveis em auditoria
- decisão sobre retenção e consulta futura

## Definição de Pronto
- migration criada
- logger enriquecido implementado
- uso aplicado nas ações críticas prioritárias
- testes e validações verdes
- documentação técnica mínima atualizada

## Resultado Esperado
O Prosperium passa a ter trilha de auditoria adequada para operação enterprise, revisão de compliance e investigação operacional, sem depender apenas de logs genéricos ou contexto parcial.
