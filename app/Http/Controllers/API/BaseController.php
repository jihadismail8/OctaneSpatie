<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use SergiX44\Nutgram\Nutgram;

/**
 * @OA\OpenApi(
 *  @OA\Info(
 *      title="NMS API",
 *      version="1.0.0",
 *      description="API documentation for NMS App",
 *      @OA\Contact(email="jihad.ismail.8@gmail.com")
 *  ),
 *  @OA\Server(
 *      description="NMS App API",
 *      url=""
 *  ),  
 * )
 */
class BaseController extends Controller

{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /** 

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendResponse($result, $message)

    {

    	$response = [

            'success' => true,

            'data'    => $result,

            'message' => $message,

        ];


        return response()->json($response, 200);

    }


    /**

     * return error response.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendError($error, $errorMessages = [], $code = 404)

    {

    	$response = [

            'success' => false,

            'message' => $error,

        ];


        if(!empty($errorMessages)){

            $response['data'] = $errorMessages;

        }


        return response()->json($response, $code);

    }


    public function logtochannel($channel,$group,$level,$msg){
        $bot = new Nutgram($_ENV['TELEGRAM_TOKEN']);    
        $level = isset($level) ? $level : 'info';
        $channel = isset($channel) ? $channel : 'telegram';
        $groupName = isset($group) ? $group : '';
        $messageBody = isset($msg) ? $msg : '';
        $telegramTopicsIds = json_decode(env('TELEGRAM_TOPICS_IDS'), true);
        $topicId = $telegramTopicsIds[$groupName];

        if($channel == "telegram"){
            switch ($level) {
                case 'emergency':
                    $html = "<pre style='border: 2px solid red; padding: 10px;'><b>Emergency  : </b><span class='tg-spoiler' style='color: red;'>$messageBody</span></pre>";
                    break;
                case 'alert':
                    $html = "<pre style='border: 2px solid orange; padding: 10px;'><b>Alert  : </b><span class='tg-spoiler' style='color: orange;'>$messageBody</span></pre>";
                    break;
                case 'critical':
                    $html = "<pre style='border: 2px solid purple; padding: 10px;'><b>Critical  : </b><span class='tg-spoiler' style='color: purple;'>$messageBody</span></pre>";
                    break;
                case 'error':
                    $html = "<pre style='border: 2px solid darkred; padding: 10px;'><b>Error  : </b><span class='tg-spoiler' style='color: darkred;'>$messageBody</span></pre>";
                    break;
                case 'warning':
                    $html = "<pre style='border: 2px solid gold; padding: 10px;'><b>Warning  : </b><span class='tg-spoiler' style='color: gold;'>$messageBody</span></pre>";
                    break;
                case 'notice':
                    $html = "<pre style='border: 2px solid blue; padding: 10px;'><b>Notice  : </b><span class='tg-spoiler' style='color: blue;'>$messageBody</span></pre>";
                    break;
                case 'info':
                    $html = "<pre style='border: 2px solid green; padding: 10px;'><b>Info : </b><span class='tg-spoiler' style='color: green;'>$messageBody</span></pre>";
                    break;
                case 'debug':
                    $html = "<pre style='border: 2px solid gray; padding: 10px;'><b>Debug  : </b><span class='tg-spoiler' style='color: gray;'>$messageBody</span></pre>";
                    break;
                default:
                    $html = "<p>$messageBody</p>";
                    break;
            }
            if (strpos($messageBody, "AutoTest") === false && strpos($messageBody, "jihad") === false) {
                $bot->sendMessage($html,['chat_id'=>$_ENV['TELEGRAM_CHANNEL_ID'],'message_thread_id'=>$topicId , 'parse_mode'=>'HTML']);
            }
        }
    }

}
