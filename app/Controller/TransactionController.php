<?php declare(strict_types=1);


namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

use Hyperf\DbConnection\Db;

use Carbon\Carbon;
use function Hyperf\Coroutine\co;

class TransactionController extends AbstractController
{

    protected ValidatorFactoryInterface $validationFactory;
    #[Inject]
    protected StdoutLogger $logger;


    public function __construct(ValidatorFactoryInterface $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }


    public function createTransaction()
    {

        $transactionData = $this->request->all();

        $validator = $this->validationFactory->make(
            $this->request->all(),
            [
                'valor' => 'required|integer',
                'tipo'  => 'required|in:c,d',
                'descricao' => 'required|string|min:1|max:10',
            ]);

        if ($validator->fails()) return $this->response->withStatus(422);

        $customerId = $this->request->route('id');

        list($row) = Db::select('select * from update_balance(?, ?, ?, ?)', [
            $customerId,
            $transactionData['tipo'],
            $transactionData['valor'],
            $transactionData['descricao']
        ]);

        if ($row->is_error) {
            if ($row->message == 'Limite permitido foi atingido') return $this->response->withStatus(422)->json([
                'message' => $row->message
            ]);
            if ($row->message == 'Cliente não encontrado') return $this->response->withStatus(404)->json([
                'message' => $row->message
            ]);
            return $this->response->withStatus(500)->json(['message' => 'Erro não previsto']);
        }

//        Db::table('transactions')
//            ->insert([
//                'value' => $transactionData['valor'],
//                'type' => $transactionData['tipo'],
//                'description' => $transactionData['descricao'],
//                'customer_id' => $customerId
//            ]);

        return [
            'message' => $row->message,
            'limite' => intval($row->customer_limit),
            'saldo'  => intval($row->updated_balance)
        ];
    }


    public function listStatement()
    {
        $customerId = intval($this->request->route('id'));

        $client = Db::table('customers')
            ->where(['id' => $customerId])->first();

        if (empty($client)) {
            return $this->response->withStatus(404)->json(['message' => 'Cliente não encontrado']);
        }

        $data = [
            'saldo' => [
                'total' => $client->balance,
                'data_extrato' => Carbon::now()->toISOString(),
                'limite' => $client->limit,
            ],
            'ultimas_transacoes' => Db::table('transactions')
                ->select([
                    'value as valor',
                    'description as descricao',
                    'type as tipo',
                    'created_at as realizada_em'
                ])
                ->where([
                    'customer_id' => $customerId
                 ])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
        ];

        return $data;

    }
}