# Relatório do Catálogo de Bancos

- Fonte analisada: `/home/jlazzarotto/Downloads/bancos.sql`
- Registros brutos: 263
- Bancos únicos por `codigo_compe`: 260
- Códigos duplicados encontrados: 3

## Regras Aplicadas

- `cod` do dump foi mapeado para `codigo_compe` com zero à esquerda até 3 dígitos.
- `status` `1/0` foi convertido para `active/inactive`.
- `id_banco`, `id_operador`, `deleted_at` e `abreviatura` não foram carregados no schema atual.
- Em conflitos de `codigo_compe`, foi mantido o registro com melhor completude: site preenchido, site não malformado e nome mais completo.

## Conflitos Resolvidos

### Código 280

- Mantido: `Avista S.A. Crédito` (source_id `140`, site `https://www.avista.com.br/`)
- Descartado: `Avista S.A. Crédito` (source_id `141`, site `https://www.avistafinanceira.com.br/`)

### Código 306

- Mantido: `Qi Sociedade de Crédito Direto S.A.` (source_id `156`, site `https://www.qipagamentos.com.br`)
- Descartado: `Portopar Distribuidora de Titulos e Valores M` (source_id `157`, site `NULL`)

### Código 370

- Mantido: `Terra Investimentos Distribuidora de Títulos` (source_id `193`, site `https://www.terrainvestimentos.com.br`)
- Descartado: `Banco Mizuho Do Brasil S.A.` (source_id `194`, site `NULL`)

