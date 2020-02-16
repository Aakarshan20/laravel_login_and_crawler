<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Model\Astros;



class Crawler extends Command
{
    
    protected $signature = 'Crawler:astro';
    protected $description = '取得星座網站的資料';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //12星座編號拼接url
        for($i = 1; $i<=12; $i++){
            $this->getAstro($i);
            
            sleep(3);
        }

    }

    //取出各星座
    protected function getAstro($astroNum){
        $client = new Client();
        $url = sprintf('http://astro.click108.com.tw/daily_%s.php?iAstro=%s', $astroNum, $astroNum);
        
        $res = $client->request('GET', $url);
        
        $html = $res->getBody(); //原始文本
       
        $patternDate = '/<option value="(.*?)"/ies'; //正則比對日期
        preg_match($patternDate, $html, $matchesDate);
        $date = $matchesDate[1];// 取出日期

        $patternAstro = '/日運勢－(.*?)<\/a>/ies';
        preg_match($patternAstro, $html, $matchesAstro);
        $name = $matchesAstro[1];// 取出星座名'
       
        $patternTodayContent = '/<div class="TODAY_CONTENT">(.*?)<\/div>/ies';
        preg_match($patternTodayContent, $html, $matchesTodayContent);
        $todayContent = $matchesTodayContent[1];// 取出運勢'

        // 取出整體運勢
        $full =  $this->getFortune($todayContent, 'full');
        $career = $this->getFortune($todayContent, 'career');
        $love =  $this->getFortune($todayContent, 'love');
        $fortune =  $this->getFortune($todayContent, 'fortune');

        $insertData = [
            'name' =>$name,
            'date' =>$date,
            'full' => $full, 
            'career' => $career,
            'love' => $love,
            'fortune' => $fortune,
        ];
        $this->saveDb($insertData);
    }

    protected function saveDb($insertData){

        if($this->isInsertedInOneHour($insertData['name'], 3600)){
            return;
        } else {
            $astros = new Astros();
            $astros->create($insertData);
        }

    }

    //確保一個小時內只新增一筆
    protected function isInsertedInOneHour($name, $timeInterval){
        $subOneHour = time() - $timeInterval;
        $count = Astros::where('name', '=', $name)->where('updated_at', '<=', $subOneHour)->count();
        if($count){
            return true;
        } else {
            return false;
        }

    }


    //取出星數方便拼裝
    protected function getFortuneStars($string,  $type){
        $patternSuffix = '運勢(.*?)：<\/span>/ies';
        switch($type){
            case 'full':
                $pattern = '/整體' . $patternSuffix;
            break;
            case 'fortune':
                $pattern = '/財運' . $patternSuffix;
            break;
            case 'love':
                $pattern = '/愛情' . $patternSuffix;
            break;
            case 'career':
                $pattern = '/事業' . $patternSuffix;
            break;
        }
        preg_match($pattern, $string, $matches);

        return $matches[1];
    }

    //取出運勢
    protected function getFortune($string, $type){

        $typeStar = $this->getFortuneStars($string, $type);

        $patternSuffix = '運勢' . $typeStar . '：<\/span><\/p><p>(.*?)<\/p>/ies';
        switch($type){
            case 'full':
                $pattern = '/整體' . $patternSuffix;
            break;
            case 'fortune':
                $pattern = '/財運' . $patternSuffix;
            break;
            case 'love':
                $pattern = '/愛情' . $patternSuffix;
            break;
            case 'career':
                $pattern = '/事業' . $patternSuffix;
            break;
        }

        preg_match($pattern, $string, $matches);

        return $matches[1];
    }
}
