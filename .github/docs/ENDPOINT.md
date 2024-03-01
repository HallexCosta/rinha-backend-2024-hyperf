## Extrato

**Requisição**

`GET /clientes/{id}/extrato`

Onde
- `{id}` (na URL) deve ser um número inteiro representando a identificação do cliente.
```json
{
 "saldo": {
   "total": -9098,
   "data_extrato": "2024-01-17T02:34:41.217753Z",
   "limite": 100000
 },
 "ultimas_transacoes": [
   {
     "valor": 10,
     "tipo": "c",
     "descricao": "descricao",
     "realizada_em": "2024-01-17T02:34:38.543030Z"
   },
   {
     "valor": 90000,
     "tipo": "d",
     "descricao": "descricao",
     "realizada_em": "2024-01-17T02:34:38.543030Z"
   }
 ]
}
```

## Transações
`POST /clientes/{id}/transacoes`
- `{id}` (path) - ID do cliente
- valor (body) - Valor da transação (em centavos)
- tipo (body) - Tipo da transação (c - crédito, d - débito)
- descricao (body) - Descrição da transação
- Realiza uma transação no extrato do cliente.  