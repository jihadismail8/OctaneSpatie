<?php

namespace App\Livewire;
use CMServiceV1\inComingRequestsClient;
use CMServiceV1\Objects;
use CMServiceV1\search_request;
use Livewire\Component;
use Grpc;

class Search extends Component
{
    public $ObjectClass = "ManagementElements";
    public $field = "userLabel";
    public $Value = "MCrUrb";

    public function render()
    { 

        $request =new Objects();
        // $request->setObjects("[]");
        $client= new inComingRequestsClient('192.168.1.103:39201', [
            'credentials' => Grpc\ChannelCredentials::createInsecure()
        ]);
        // Prepare the request message
        $request = new search_request();
        $request->setObjectClass($this->ObjectClass); // Set your search criteria here
        $request->setField($this->field);
        $request->setValue($this->Value);
        list($response, $status) = $client->searchENB($request)->wait();
        if ($status->code === Grpc\STATUS_OK) {
            // Handle the response from the server
            $results = $response->getResult();
            // foreach ($results as $result) {
            //     dd($result);
            // }
        } else {
            dd("RPC failed with error code: " . $status->code . ", message: " . $status->details . PHP_EOL);
        }
        // list($data, $status) = $client->getMetaValidation($request)->wait();
        // $data = $reply->getMessage();
        // dd($results);
        $data = [
            'users' => $results,
        ];
        return view('livewire.search',$data);
    }
}
