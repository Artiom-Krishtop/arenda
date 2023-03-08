<?
namespace ITG\Clients;

use Bitrix\Main\Web\HttpClient;

class TelegramClient
{
    private const TELEGRAM_API_KEY = '5912066191:AAFTm9UMATPtCfZxsYeBGc_jouwfh-nXiu0';
    private const TELEGRAM_CHAT_ID = '@arenda_test_channel';

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new HttpClient();
    }

    public function sendPhoto(array $arrPhotos, $caption, $mode = 'html')
    {
        $mediaData = array();

        foreach ($arrPhotos as $key => $photoData) {
            $mediaData['media'][$key] = array(
                'type' => 'photo',
                'media' => 'attach://' . $photoData['name'],
            );

            if($key == 0){
                $mediaData['media'][$key]['caption'] = $caption;
                $mediaData['media'][$key]['parse_mode'] = $mode;
            }

            $mediaData[$photoData['name']] = fopen($photoData['tmp_name'], 'r');
        }

        $data = array(
            'chat_id' => self::TELEGRAM_CHAT_ID,
        );

        $mediaData['media'] = json_encode($mediaData['media']);

        $data = array_merge($data, $mediaData);

        return $this->send('sendMediaGroup', $data, true);
    }

    public function sendMessage($text, $mode = 'HTML')
    {
        $data = array(
            'chat_id' => self::TELEGRAM_CHAT_ID,
            'text' => $text,
            'parse_mode' => $mode
        );

        return $this->send('sendMessage', $data);
    }

    protected function send($method, $data, $multipart = false)
    {
        $url = 'https://api.telegram.org/bot'. self::TELEGRAM_API_KEY . '/' . $method;

        return $this->httpClient->post($url, $data, $multipart);
    }
}