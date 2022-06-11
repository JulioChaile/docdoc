<?php
namespace frontend\modules\api\controllers;

use Yii;
use GuzzleHttp\Client;
use yii\web\HttpException;

class ReportController extends BaseController
{
    const ERROR = "Error report-api";

    /**
     * @api {post} /report Reportar Bug
     * @apiName ReportarBug
     * @apiGroup Report
     * @apiError {String} error Mensaje de error.
     */
    public function actionCreate()
    {
        $rq = Yii::$app->request->post();
        $card_id = $this->createTrelloCard($rq);
        $image_link = $this->sendImage($rq);
        $this->addImageTrelloCard($rq, $card_id, $image_link);
        return ['error' => null];
    }

    private function sendImage($rq)
    {
        $client = new Client([
            'base_uri' => 'https://api.imgur.com'
        ]);

        $path = '/3/image';

        try {
            $response = $client->request('POST', $path, [
                'headers' => [
                    'Authorization' => 'Client-ID d7dd4d381ad9437'
                ],
                'form_params' => [
                    'image' => $rq['image']
                ]
            ]);
            if ($response->getStatusCode()/100 != 2) {
                throw new HttpException(500, self::ERROR);
            }
            $jsonResp = json_decode($response->getBody(), true);
            return $jsonResp['data']['link'];
        } catch (\Exception $e) {
            throw new HttpException(500, self::ERROR);
        }
    }

    private function createTrelloCard($rq)
    {
        $client = new Client([
            'base_uri' => 'https://api.trello.com'
        ]);

        $rq['desc'] .= "\n```User-Agent: " . Yii::$app->request->userAgent . "```";

        $path = "/1/cards?key=9af914fe341ce0fd3bb92a43559a1bdd&token=c419007cceecdf40e2a51740b3671cc4904b832abb90bb6a5d1a33104ee80432&idList=5ef0043ec7ee1a529397f8ad&name={$rq['title']}&desc={$rq['desc']}";

        try {
            $response = $client->request('POST', $path);
            if ($response->getStatusCode()/100 != 2) {
                throw new HttpException(500, self::ERROR);
            }
            $jsonResp = json_decode($response->getBody(), true);
            return $jsonResp['id'];
        } catch (\Exception $e) {
            throw new HttpException(500, "Error gmail-api");
        }
    }

    private function addImageTrelloCard($rq, $card_id, $image_link)
    {
        $client = new Client([
            'base_uri' => 'https://api.trello.com'
        ]);

        $path = "https://api.trello.com/1/cards/{$card_id}/attachments?key=9af914fe341ce0fd3bb92a43559a1bdd&token=c419007cceecdf40e2a51740b3671cc4904b832abb90bb6a5d1a33104ee80432&idList=5ef0043ec7ee1a529397f8ad&url={$image_link}";

        try {
            $response = $client->request('POST', $path);
            if ($response->getStatusCode()/100 != 2) {
                throw new HttpException(500, self::ERROR);
            }
        } catch (\Exception $e) {
            throw new HttpException(500, self::ERROR);
        }
    }
}
