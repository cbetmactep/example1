<?php
/** @noinspection CurlSslServerSpoofingInspection */
$stakes = (new UnibetParser())->parse();

class Storage {

    const APP_NAME = "Unibet Parser"; // название парсера
    const APP_VERSION = "1.09"; // версия парсера
    const APP_ID = "0a275fef"; // id приложения
    const APP_KEY = "68c7567f2390127b4f6a2db9614fb607"; // секретный ключ для приложения
    const APP_PID = "5244427"; // партнерский id для ссылок на ставки
    const APP_BID = "{AFFID}"; // заменяется на сайте odds.am под конктретную страну
    const API_URL_GROUPS = "http://api.unicdn.net/v1/feeds/sportsbookv2/groups.json?app_id=" . Storage::APP_ID . "&app_key=" . Storage::APP_KEY . "&local=en_US&site=www.unibet.com&excludeLive=true&excludePrematch=false"; // API для получения групп
    const API_URL_GROUP_EVENTS = "http://api.unicdn.net/v1/feeds/sportsbookv2/event/group/{{group_id}}.json?app_id=" . Storage::APP_ID . "&app_key=" . Storage::APP_KEY . "&includeparticipants=false&outComeSortDir=desc&local=en_US&site=www.unibet.com"; // апи для получения событий
    const API_URL_EVENT = "http://api.unicdn.net/v1/feeds/sportsbookv2/betoffer/event/{{event_id}}.json?app_id=" . Storage::APP_ID . "&app_key=" . Storage::APP_KEY . "&includeparticipants=false&outComeSortBy=lexical&outComeSortDir=desc";

    const BETTING_URL = "http://dspk.kindredplc.com/redirect.aspx?pid=". Storage::APP_PID . "&bid=". Storage::APP_BID . "&unibetTarget=/betting%2523event/{eventid}?coupon=single|{betid}|0|replace";
           // переменные для curl
    const CURL_OPTS_ARRAY = [
        CURLOPT_SSL_VERIFYPEER => 1,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_BINARYTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 40,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_ENCODING => 'gzip',
        CURLOPT_HTTPHEADER => array('Content-Type:application/json') // в данном случае json
    ];

    const PARSE_FUNCTION = [
        'parse2wor3w' => [
            '2nd Half',
            'Full Time',
            'Including Overtime',
            'Result at end of Quarter 4',
            '1st Half','Quarter 1',
            '2nd Half',
            'Result at end of Quarter 4',
            'Map 1',
            'Set 2',
            'Set 1',
            'Set Betting',
            'Set 3',
            'Quarter 1',
            'Quarter 2',
            'Quarter 3',
            'Quarter 4',
            'Regular Time (3-way)',
            'Regular Time',
            'Including overtime and penalty shootout',
            'Period 1',
            'Period 2',
            'Period 3'],
        'parseTotal' => [
            'Total Points - Including Overtime',
            'Total Points - Quarter 1',
            'Total Points - Quarter 2',
            'Total Points - Quarter 3',
            'Total Points - Quarter 4',
            'Total Maps',
            'Total Goals',
            'Total Goals - 1st Half',
            'Total Goals - 2nd Half',
            'Asian Total',
            'Asian Total - 1st Half',
            'Total Goals - Period 1',
            'Total Goals - Period 2',
            'Total Goals - Period 3',
            'Total Goals - Including Overtime',
            'Total Goals - Regular Time',
            'Total Goals - Period 1',
            'Total Goals - Period 2',
            'Total Goals - Period 3',
            'Total Points - 1st Half',
            'Total Points - 2nd Half',
            'Total Goals - Including Overtime and Penalty Shootout',
            'Total Games'],
        'parseScore' => [
            'Correct Score',
            'Correct Map Score'],
        'parseHandicap' => [
            'Handicap - Quarter 4',
            'Handicap - 2nd Half',
            'Map Handicap',
            'Handicap - Quarter 3',
            'Handicap - 1st Half',
            'Handicap - Including Overtime',
            'Handicap - Quarter 1',
            '3-Way Handicap - 1st Half',
            'Handicap - Including Overtime',
            'Handicap','Map Handicap',
            '3-Way Handicap',
            '3-Way Handicap - 1st Half',
            '3-Way Handicap - 2nd Half', 
            'Asian Handicap - 1st Half',
            'Asian Handicap - 2nd Half',
            'Asian Handicap','Set Handicap',
            'Handicap - Quarter 1',
            'Handicap - Quarter 2',
            'Handicap - Quarter 3',
            'Handicap - Quarter 4',
            'Game Handicap',
            '3-Way Handicap - Including Overtime',
            '3-Way Handicap - Regular Time',
            'Handicap - Regular Time',
            'Handicap - Including Overtime and Penalty Shootout',
            '3-Way Handicap - Including Overtime and Penalty Shootout'],
        'parseDnb' => [
            'Draw No Bet',
            'Draw No Bet - 1st Half',
            'Draw No Bet - 2nd Half', 
            'Draw No Bet - Quarter 1',
            'Draw No Bet - Quarter 2',
            'Draw No Bet - Quarter 3',
            'Draw No Bet - Quarter 4', 
            'Draw No Bet - Period 1',
            'Draw No Bet - Period 2',
            'Draw No Bet - Period 3',
            'Draw No Bet - Regular Time'
    ],
        'parseDc' => [
            'Double Chance',
            'Double Chance - 1st Half',
            'Double Chance - 2nd Half',
            'Double Chance - Regular Time',
            'Double Chance - Period 1',
            'Double Chance - Period 2',
            'Double Chance - Period 3'
        ],
        'parseHalffull' => ['Half Time/Full Time'],
        'parseQual' => ['To qualify to the next round'],
        'parseFgoal' => ['First Team to Score'],
        'parseTimegoals' => [
            'Highest scoring Half',
            'Highest scoring Quarter'
        ],
        'parseGoals' => [],
        'parseSentoff' => ['Red Card given'],
        'parsePenalty' => [],
        'parseBtts' => [
            'Both Teams To Score', 
            'Both Teams To Score - 1st Half',
            'Both Teams To Score - 2nd Half',
            'Both Teams To Score - Regular Time',
            'Both Teams To Score - Period 1', 
            'Both Teams To Score - Period 2',
            'Both Teams To Score - Period 3'],
        'parseOvertime' => [],
        'parseTotalsSets' => [
            'Total Sets'],
        'parseOddeven' => [
            'Total Goals Odd/Even',
            'Total Points Odd/Even - Including Overtime',
            'Total Goals Odd/Even - 1st Half',
            'Total Goals Odd/Even - 2nd Half',
            'Total Points Odd/Even - 1st Half',
            'Total Points Odd/Even - 2nd Half'],
    ];

    const STAKES_CHECKER = ['2w'=>2,'3w'=>3,'dc'=>3,'dnb'=>2,'totals'=>2,'pers_totals'=>2,'ah'=>2,'eh'=>3,'halffull'=>9,'qual'=>2,'fgoal'=>3,'oddeven'=>2,'sentoff'=>2,'penalty'=>2,'btts'=>2,'overtime'=>2,'totals_sets'=>2];

    const PARSE_PERIOD = [
        0 => ['Regular Time'],
        1 => ['1st Half','Period 1','Quarter 1','Set 1'],
        2 => ['2nd Half','Period 2','Quarter 2','Set 2'],
        3 => ['Period 3','Quarter 3','Set 3'],
        4 => ['Quarter 4','Set 4'],
        5 => ['Set 5'],
        20 => ['Including Overtime'],
        30 => ['Including Overtime and Penalty Shootout']
    ];

    const CHECK_STAKES=['2w','3w','dc','dnb','totals','pers_totals','ah','eh','halffull','qual','fgoal','timegoals','score','goals','oddeven','sentoff','penalty','btts','overtime','totals_most','totals_less','timebreak','totals_sets'];
    const TIMEGOALS_REPLACE = [
        "Same Amount" => 0, "Same amount" => 0,"First Half" => 1,"First Quarter" => 1,"Second Half" => 2,"Second Quarter" => 2,"Third Quarter" => 3,"Fourth Quarter" => 4,"1st Quarter" => 1,"2nd Quarter" => 2, "3rd Quarter" => 3, "4th Quarter" => 4,"1st Half" => 1,"2nd Half" => 2
    ];

    const ESPORT_NAMES = ["Dota","Valorant","Counter_strike","Overwatch", "League of legends", "Counter strike"];

}

class Logger {

    /**
     * Summary of writeLog
     * @param string $section - обозначает к какому типу относится строка, которая записывается в лог
     * @param string $message - сообщение, записываемое в лог
     * @return void
     */
    public static function writeLog(string $section, string $message): void
    {
        $day = date("Y-m-d");
        $time = date("H:i");
        $log_line = "[{$day}][{$time}][" . Storage::APP_VERSION . "][{$section}] - {$message}\n";
        @file_put_contents($_ENV['BM_LOGS'], $log_line, FILE_APPEND | LOCK_EX);
    }


}

class GetDataWithCurl {

    /**
     * Summary of add_multi_curl_handle
     * @param array $urls - массив url для запроса
     * @param resource $mh - ресурс multi_curl 
     * @return array 
     */
    public function add_multi_curl_handle($urls,$mh) {
        $chs=[];
        $opts = Storage::CURL_OPTS_ARRAY + ( $_ENV[ 'BM_PROXY' ] ?? [] );
        foreach($urls as $k => $url) {
            $chs[$k]=curl_init($url);
            curl_setopt_array($chs[$k],$opts);
            curl_multi_add_handle($mh,$chs[$k]);
        }
        return $chs;
    }

    /**
     * Summary of add_multi_curl_handle
     * @param array $urls - массив url
     * @return array - возвращает массив с результами щапроса
     */
    public function getMultiData(array $urls) {
        $result=[];
        $mh = curl_multi_init();
        $chs = $this->add_multi_curl_handle($urls,$mh);
        $active = false;
        do{
            curl_multi_exec( $mh, $active );
            $m_curl_read = curl_multi_info_read($mh);
            if(is_array($m_curl_read) && $ch_r=$m_curl_read['handle']) {
                if($m_curl_read['result']==0) {
                    $result[]=curl_multi_getcontent($ch_r);
                    
                } else {
                    $curl_error=curl_error($ch_r);
                    $curl_info=curl_getinfo($ch_r);
                    Logger::writeLog('Warning','Error loading data from '.$curl_info['url'].' '.$curl_error);
                }
            }
        } while($active);
        foreach($chs as $k => $ch) {
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);
        return $result;
    }

    /**
     * Summary of getData
     * @param string $url - 
     * @return string - возвращает данные от беттера
     */
    public function getData(string $url)
    {
        $ch = curl_init($url);
        $options = Storage::CURL_OPTS_ARRAY + ($_ENV['BM_PROXY'] ?? []);
        curl_setopt_array($ch, $options);
        if (($cUrlData = curl_exec($ch)) === false) {
            Logger::writeLog("Error", "Error loading data " . curl_errno($ch) . " - " . curl_error($ch));
        }
        curl_close($ch);
        return $cUrlData;
    }
}



class ParserHelpers {


    /**
     * Summary of checkMarkets
     * @param array $line - получаем обработаное спортивное событие
     * @return bool если в обработанной линии присутсвует хоть одно спортивное событие то возвращаем true, в противном false
     */
    protected function checkMarkets($line) {
        return ( count( array_intersect_key($line, array_flip(Storage::CHECK_STAKES) )) > 0 );
    }

    /**
     * Summary of returnJsonString
     * @param string $json_string - строка json
     * @return object - возвращает преобразованную в объект строку json
     */
    protected function returnJsonString(string $json_string) {
        $json_decode_string = @json_decode($json_string, false);
        if(json_last_error() !== JSON_ERROR_NONE) {
             Logger::writeLog("error", "Error decode groups data" . Storage::APP_NAME . " json error " . json_last_error());
        }
        return $json_decode_string;
    }

    /**
     * Summary of get2wor3w 
     * @param array $odds - массив с очками
     * @return string - тип ставки 2w или 3w
     */
    protected function get2wor3w($odds) {
        $dic = [ 2 => '2w', 3 => '3w' ];
        return $dic[ count($odds) ] ?? '';
    }


    /**
     * Summary of getTeamForBet 
     * @param string $find_me - называние рынка 
     * @param string $team1 - название спорта 
     * @param string $team2  - массив url отдельных событий
     * @return string - массив url отдельных событий
     */
    protected function getTeamForBet($find_me, $team1, $team2) {
        $dic = [
            $team1 => 1,
            $team2 => 2,
            'X' => 'x',
            'x' => 'x',
            1 => 1,
            2 => 2,
            '2-1' => '21',
        ];
        return ($dic[$find_me]) ?? '';
    }

    private function validateLine($line, $bet_type) : bool {
        return (count($line) === Storage::STAKES_CHECKER[$bet_type]);
    }

    /**
     * Summary of returnTimeperiod 
     * @param string $market_name - называние рынка 
     * @param string $sport_name - название спорта 
     * @return string - массив url отдельных событий
     */
    protected function getTimeperiod($market_name, $sport_name) {
        foreach(Storage::PARSE_PERIOD as $period => $period_values) {
            foreach($period_values as $period_value) {
                if (strpos($market_name, $period_value)!==FALSE) {
                    if(strpos( strtolower($market_name), "half")!==FALSE && ($sport_name === "Basketball" || $sport_name === "Australian rules")) {
                           return (string)( 10 + (int)$period);
                    }
                    return $period;
                }
            }
        }
        return 0;
    }

    protected function getBettingUrl($event_id, $betoffer_id=0) : string {
        return str_replace(["{eventid}","{betid}"], [$event_id,$betoffer_id], Storage::BETTING_URL);
    }

    protected function cleanNullStakes($parseLine, $betType) : array {
        if( count($parseLine[$betType]) === 0) {
            unset($parseLine[$betType]);
        }
        return $parseLine;
    }

    /**
     * Summary of parse2wor3w 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parse2wor3w(array $parseLine, int $period, array $odds) : array {
        if ( ($betType = $this->get2wor3w($odds)) !== '') {
            foreach ($odds as $odd) {
                if(isset($odd['label'], $odd['odd'])) {
                    $team = $this->getTeamForBet($odd['label'], $parseLine['team1'], $parseLine['team2']);
                    $parseLine[$betType][$period][$team] = $odd['odd'];
                }
            }
            if($this->validateLine($parseLine[$betType][$period], $betType) === false) {
                unset($parseLine[$betType][$period]);   
            } 
            else {
                $i = 0;
                foreach ($parseLine[$betType][$period] as $team => $odd) {
                    if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') {
                        $parseLine[$betType][$period]["url_".$team] = $betting_url;
                    }
                    $i++;
                }
            }
            $parseLine = $this->cleanNullStakes($parseLine, $betType);
        }
        return $parseLine;
    }

    /**
     * Summary of parseTotal 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseTotal(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odds[0]['points'], $odd['label'], $odd['odd'])) {
                $parseLine['totals'][ $period ][ (string) $odds[0]['points'] ][ strtolower( $odd['label'] ) ] = $odd['odd'];
            }
        }
        if(isset($odds[0]['points']) && $this->validateLine($parseLine['totals'][ $period ][ (string) $odds[0]['points'] ], 'totals') === false) {
            unset($parseLine['totals'][ $period ][ (string) $odds[0]['points'] ]);   
        } 
        else 
        {
            $i = 0;
            if(isset($odds[0]['points'] )) {
                foreach ($parseLine['totals'][ $period ][ (string) $odds[0]['points'] ] as $team => $odd) {
                    if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') {
                        $parseLine['totals'][ $period ][ (string) $odds[0]['points'] ]["url_".$team] = $betting_url;
                    }
                }
                $i++;
            }
        }
        if(isset($parseLine['totals'][ $period ]) && count($parseLine['totals'][ $period ]) === 0) {
            unset($parseLine['totals'][ $period ]);
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'totals');
        return $parseLine;
    }   

    /**
     * Summary of parsePerstotal 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parsePerstotal(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['team'], $odd['points'], $odd['label'], $odd['odd'])) {
                $parseLine['pers_totals'][ $period ][$odd['team'] ][ (string) $odd['points'] ][ strtolower( $odd['label'] ) ] = $odd['odd'];
            }
        }
        if(isset($odd['points']) && $this->validateLine($parseLine['pers_totals'][ $period ][$odd['team'] ][ (string) $odd['points'] ], 'pers_totals') === false) {
            unset($parseLine['pers_totals'][ $period ][$odd['team'] ][ (string) $odd['points'] ]);   
        } 
        else 
        {
            $i = 0;
            foreach ($parseLine['pers_totals'][ $period ][$odds[0]['team'] ][ (string) $odds[0]['points'] ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') {
                    $parseLine['pers_totals'][ $period ][$odds[0]['team'] ][ (string) $odds[0]['points'] ]["url_".$team] = $betting_url;
                }
            }
            $i++;
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'pers_totals');
        return $parseLine;
    }     

    /**
     * Summary of parseTotalsSets 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseTotalsSets(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['points'], $odd['label'], $odd['odd'])) {
                $parseLine['totals_sets'][ (string) $odd['points'] ][ strtolower( $odd['label'] ) ] = $odd['odd'];
            }
        }
        if(isset($odds[0]['points']) && $this->validateLine($parseLine['totals_sets'][ (string) $odds[0]['points'] ], 'totals_sets') === false) {
            unset($parseLine['totals_sets'][ (string) $odd['points'] ]);   
        } 
        else 
        {
            $i = 0;
            foreach ($parseLine['totals_sets'][ (string) $odds[0]['points']  ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') {
                    $parseLine['totals_sets'][ (string) $odds[0]['points'] ]["url_".$team] = $betting_url;
                }
            }
            $i++;
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'totals_sets');
        return $parseLine;
    }

    /**
     * Summary of parseScore 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseScore(array $parseLine, int $period, array $odds) : array {
         foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['points'], $odd['label'], $odd['odd'])) {
                $points = str_replace("-", ":", $odd['label']);
                $parseLine['score'][ $points ] = $odd['odd'];
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odd['id'])) !== '') 
                {
                    $parseLine['score']["url_".$team] = $betting_url;
                }
            }
        }
        return $parseLine;       
    }

    /**
     * Summary of parseEhAh 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseHandicap(array $parseLine, int $period, array $odds) : array {
        $handicap_type = (count($odds) === 2) ? 'ah' : 'eh'; 
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odds[0]['points'], $odd['label'], $odd['odd'])) {
                $team = $this->getTeamForBet($odd['label'], $parseLine['team1'], $parseLine['team2']);
                $parseLine[$handicap_type][ $period ][ (string) $odds[0]['points'] ][ $team ] = $odd['odd'];
            }
        }
        if(isset($odds[0]['points']) && $this->validateLine($parseLine[$handicap_type][ $period ][ (string) $odds[0]['points'] ], $handicap_type) === false) {
            unset($parseLine[$handicap_type][ $period ][ (string) $odds[0]['points'] ]);   
        }
        else {
            $i = 0;
            foreach ($parseLine[$handicap_type][ $period ][ (string) $odds[0]['points'] ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine[$handicap_type][ $period ][ (string) $odds[0]['points'] ]["url_".$team] = $betting_url;
                }
                $i++;
            }
        }
        $parseLine = $this->cleanNullStakes($parseLine, $handicap_type);
        return $parseLine; 
    }

    /**
     * Summary of parseDnb 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseDnb(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                    $parseLine['dnb'][ $period ][ $odd['label'] ] = $odd['odd'];
            }
        }
        if($this->validateLine($parseLine['dnb'][ $period ], 'dnb') === false) {
            unset($parseLine['dnb'][ $period ]);   
        } else {
            $i = 0;
            foreach ($parseLine['dnb'][ $period ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine['dnb'][ $period ]["url_".$team] = $betting_url;
                }
                $i++;
            }
        }
        if( count ($parseLine['dnb']) === 0 ) {
            unset($parseLine['dnb']);
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'dnb');
        return $parseLine; 
    }

    /**
     * Summary of parseDc 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
     protected function parseDc(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                $parseLine['dc'][ $period ][ strtolower( $odd['label'] )] = $odd['odd'];
            }
        }
        if($this->validateLine($parseLine['dc'][ $period ], 'dc') === false) {
            unset($parseLine['dc'][ $period ]);   
        } else {
            $i = 0;
            foreach ($parseLine['dc'][ $period ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine['dc'][ $period ]["url_".$team] = $betting_url;
                }
                $i++;
            }
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'dc');
        return $parseLine; 
    }

    /**
     * Summary of parseBtts 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseBtts(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                $label = strtolower($odd['label']);
                $parseLine['btts'][ $period ][ $label ] = $odd['odd'];
            }
        }
        if($this->validateLine($parseLine['btts'][ $period ], 'btts') === false) {
            unset($parseLine['btts'][ $period ]);   
        } else {
            $i = 0;
            foreach ($parseLine['btts'][ $period ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine['btts'][ $period ]["url_".$team] = $betting_url;
                }
                $i++;
            }
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'btts');
        return $parseLine; 
    }

    /**
     * Summary of parseHalffull 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseHalffull(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                $label = str_replace(["/","X"], ["",0], $odd['label']);
                $parseLine['halffull'][ $label ] = $odd['odd'];
            }
        }
        if($this->validateLine($parseLine['halffull'], 'halffull') === false) {
            unset($parseLine['halffull']);   
        } else {
            $i = 0;
            foreach ($parseLine['halffull'] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine['halffull']["url_".$team] = $betting_url;
                }
                $i++;
            }
        }
        return $parseLine; 
    }

    /**
     * Summary of parseSentoff 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseSentoff(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                $parseLine['sentoff'][ $period ][ strtolower($odd['label']) ] = $odd['odd'];
            }
        }
        if($this->validateLine($parseLine['sentoff'][ $period ], 'sentoff') === false) {
            unset($parseLine['sentoff'][ $period ]);   
        } else {
            $i = 0;
            foreach ($parseLine['sentoff'][ $period ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine['sentoff'][ $period ]["url_".$team] = $betting_url;
                }
            }
            $i++;
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'sentoff');
        return $parseLine; 
    }

    /**
     * Summary of parseFgoal 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseFgoal(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                $label = strtolower($odd['label']);
                $parseLine['fgoal'][ $period ][ $label]  = $odd['odd'];
            }
        }
        if($this->validateLine($parseLine['fgoal'][ $period ], 'fgoal') === false) {
            unset($parseLine['fgoal'][ $period ]);   
        } else {
            $i = 0;
            foreach ($parseLine['fgoal'][ $period ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine['fgoal'][ $period ]["url_".$team] = $betting_url;
                }
            }
            $i++;
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'fgoal');
        return $parseLine; 
    }

    /**
     * Summary of returnTimegoalsLabel
     * @param array $label - метка события
     * @return string - возвращает временной интервал с больщим количеством голов
     */
    private function returnTimegoalsLabel($label) {

        return str_replace( array_keys(STORAGE::TIMEGOALS_REPLACE), array_values(STORAGE::TIMEGOALS_REPLACE), $label);
    }

    /**
     * Summary of parseTimegoals 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseTimegoals(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                $label = $this->returnTimegoalsLabel($odd['label']);
                if( $parseLine['sport'] === 'Australian rules' || $parseLine['sport'] === 'Basketball' ) {
                    if($odd['label'] === "First Half") {
                        $label = 11;
                    }
                    if($odd['label'] === "Second Half") {
                        $label = 12;
                    }
                }
                $parseLine['timegoals'][ $label ] = $odd['odd'];
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odd['id'])) !== '') 
                {
                    $parseLine['timegoals']["url_".$label] = $betting_url;
                }
            }
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'timegoals');
        return $parseLine; 
    }

    /**
     * Summary of parseOddeven 
     * @param array $parseLine - массив парсера
     * @param int $period - период игры
     * @param array $odds - массив со ставками
     * @return array - дополненный событием массив
     */
    protected function parseOddeven(array $parseLine, int $period, array $odds) : array {
        foreach ($odds as $key => $odd) {
            if(is_int($key) && isset($odd['label'], $odd['odd'])) {
                $label = strtolower($odd['label']);
                $parseLine['oddeven'][ $period ][ $label]  = $odd['odd'];
            }
        }
        if($this->validateLine($parseLine['oddeven'][ $period ], 'oddeven') === false) {
            unset($parseLine['oddeven'][ $period ]);   
        } else {
            $i = 0;
            foreach ($parseLine['oddeven'][ $period ] as $team => $odd) {
                if (($betting_url = $this->getBettingUrl($parseLine['id'], $odds[$i]['id'])) !== '') 
                {
                    $parseLine['oddeven'][ $period ]["url_".$team] = $betting_url;
                }
            }
            $i++;
        }
        $parseLine = $this->cleanNullStakes($parseLine, 'oddeven');
        return $parseLine; 
    }
}


class UnibetParser extends ParserHelpers {

    public function __construct() {
        Logger::writeLog("info", Storage::APP_NAME . " started");
    }

    /**
     * Summary of getGoupsJsonString
     * @return string - возвращаем группы спортивных событий
     */
    private function getGoupsJsonString() : string {
        return (new GetDataWithCurl())->getData(Storage::API_URL_GROUPS);
    }

    /**
     * Summary of parseSportGroupsLeagues
     * @param array $sports_groups - массив спортивных групп
     * @param array $group_ids - идентификаторы спортивных групп
     * @return array - возвращаем группы спортивных событий
     */
    private function parseSportGroupsLeagues($sports_groups, $group_ids) : array {
        if(property_exists($sports_groups, 'groups')) {
            foreach ($sports_groups->groups as $group) {
                if(property_exists($group, 'groups')) {
                    foreach($group->groups as $country_group) {
                        $group_ids[] = $country_group->id;
                    }
                } else {
                    $group_ids[] = $group->id;
                }
            }
        }
        return $group_ids;
    }

    /**
     * Summary of parseSportsGroups
     * @param array $group - собираем из групп спортивные группы
     * @return array - возвращаем группы спортивных событий
     */
    private function parseSportsGroups($group) : array {
        $groupsIds=[];
        if(property_exists($group, 'groups') && count($group->groups)>0) {
            foreach ($group->groups as $sports_groups) {
                $sports_name[$sports_groups->englishName]=$sports_groups->englishName;
                $groupsIds = $this->parseSportGroupsLeagues($sports_groups,$groupsIds);
            }
        }
        return $groupsIds;
    }

    /**
     * Summary of returnBetOffersUrls
     * @param array $groupsIds - массив идентификаторов спортивных групп
     * @return array - массив url спортивных событий по группам
     */
    private function returnBetOffersUrls($groupsIds) {
        $betOffersUrlsArray = [];
        foreach ($groupsIds as $groupId) {
            $betOffersUrlsArray[] = str_replace("{{group_id}}", $groupId, Storage::API_URL_GROUP_EVENTS);
        }
        return $betOffersUrlsArray;
    }

    /**
     * Summary of getUrlEventsArray
     * @param array $events - массив событий
     * @return array - массив url отдельных событий
     */
    private function getUrlEventsArray($events) {
        $eventsUrlsArray = [];
        foreach ($events as $event) {
            $eventsUrlsArray[] = str_replace("{{event_id}}", $event->id, Storage::API_URL_EVENT);
        }
        return $eventsUrlsArray;
    }

    /**
     * Summary of returnOdds
     * @param string $oddsFractional - очнь представленные в виде дроби 
     * @return float - возвращает ставку
     */
    private function returnOdds($oddsFractional) : float {

        $oddsFractional_array = explode("/", $oddsFractional);
        if (!isset($oddsFractional_array[1]) || $oddsFractional_array[1] === 0) {
            return 0.0;
        }
        return $oddsFractional_array[0] / $oddsFractional_array[1] + 1;
    }


    /* Собственно здесь можно собрать массив который нужен */
    private function getParseFunction(string $market_name) : string {
        foreach(Storage::PARSE_FUNCTION as $func_name => $events_keys) {
            if(in_array($market_name,$events_keys)!==FALSE && method_exists($this, $func_name)) {
                 return $func_name;
             }
        } 
        return "";        
    }

    /**
     * Summary of checkParseArray
     * @param array $json_object - разбираемая строка json
     * @return bool - 
     */
    private function checkParseArray($json_object) {
        return ( isset($json_object->id, $json_object->start, $json_object->homeName, $json_object->awayName, $json_object->sport, $json_object->state) );
    }

    /**
     * Summary of returnOddsRow
     * @param object $outcome - объект ставки
     * @param string $totals_team - команда для тотала, гости или хощяева
     * @return array - линия со ставкой
     */
    private function returnOddsRow($outcome, $totals_team = NULL) : array{
        $odds = ['label' => '', 'odd' => ''];
        //print_r($outcome); exit;
        if($totals_team !== '') {
            $odds['team'] = $totals_team;
        }
        if(property_exists($outcome, 'line')) {
            $odds['points'] = (float) $outcome->line / 1000;
        }
        if(property_exists($outcome, 'id')) {
            $odds['id'] = $outcome->id;
        }
        if(property_exists($outcome, 'betOfferId')) {
            $odds['betOfferId'] = $outcome->betOfferId;
        }
        if(property_exists($outcome, 'label')) {
            $odds['label'] = $outcome->label;
        }
        if(property_exists($outcome, 'oddsAmerican')) {
            $odds['odd'] = (float) ( $outcome->odds / 1000 );
        } else {
            return [];
        }
        return $odds;
    }

    /**
     * Summary of parseBetOffersl
     * @param array $event - оффер со ставками
     * @return array - обработанный массив с линиями
     */
    private function parseBetOffers($parseLine,$element_json) {
        foreach($element_json->betOffers as $betOffer) {
            $func_name = $this->getParseFunction($betOffer->criterion->englishLabel);
            $totals_team = NULL;
            if( $betOffer->betOfferType->englishName === "Over/Under" ) {
                if( strpos ($betOffer->criterion->englishLabel, $parseLine['team1']) !== false) {
                    $func_name = "parsePerstotal";
                    $totals_team = "home";
                }
                if( strpos ($betOffer->criterion->englishLabel, $parseLine['team2']) !== false) {
                    $func_name = "parsePerstotal";
                    $totals_team = "away";
                }
            }
            if(method_exists( get_parent_class(__CLASS__), $func_name)) {
                $odds = [];
                $period = (new ParserHelpers)->getTimeperiod($betOffer->criterion->englishLabel, ucfirst(strtolower($parseLine['sport'])));
                if(property_exists($betOffer, 'outcomes')) {
                    foreach($betOffer->outcomes as $outcome) {
                        $odds[] = $this->returnOddsRow($outcome,$totals_team);
                    }
                }
                $parseLine = (array)(new ParserHelpers)->$func_name($parseLine,$period,$odds);
            }
        }
        return $parseLine;
    }

    private function transformSportName(string $sportname, string $tourney) : string {
        $sportname = ucfirst( strtolower ( $sportname) );
        $sportname = str_replace( "_", " ", $sportname);
        $sportname = str_replace( STORAGE::ESPORT_NAMES, "e-Sports", $sportname);
        if( strpos($tourney, "Esporte")) {
            $sportname = "e-Sports";
        }
        return $sportname;
    }

    /**
     * Summary of parseEvents
     * @param array $event - оффер со ставками
     * @return array - массив с обработанными спортивными линиями
     */
    private function parseEvents($event) {
        $parse = [ 'pre' => [], 'live' => []];
        $eventsUrlsArray=$this->getUrlEventsArray($event);
        $data = (new GetDataWithCurl)->getMultiData($eventsUrlsArray);
        foreach ($data as $element) {
            $parseLine=[];
            $element_json = (new ParserHelpers)->returnJsonString($element);
            if ( is_object($element_json) ) {
                if( property_exists($element_json, 'events') && $this->checkParseArray($element_json->events[0]) && property_exists($element_json, 'betOffers')) {
                    $parseLine['id'] = $element_json->events[0]->id;
                    $parseLine['datetime'] = date( 'Y-m-d H:i', strtotime( $element_json->events[0]->start));
                    $parseLine['team1'] = $element_json->events[0]->homeName;
                    $parseLine['team2'] = $element_json->events[0]->awayName;
                    //print_r($element_json->events[0]); exit;
                    $tourney = "";
                    if(isset($element_json->events[0]->path[1]->englishName)) {
                        $tourney .= $element_json->events[0]->path[1]->englishName;
                    }
                    if(isset($element_json->events[0]->path[2]->englishName)) {
                        $tourney .= ". ".$element_json->events[0]->path[2]->englishName;
                    }
                    if(isset($element_json->events[0]->englishName)) {
                        $tourney .= ". ".$element_json->events[0]->englishName;
                    }
                    $parseLine['tourney'] = $tourney;
                    $parseLine['sport'] = $this->transformSportName( $element_json->events[0]->sport, $parseLine['tourney']);
                    $parseLine['isLive'] = $element_json->events[0]->state === 'STARTED' ? 1 : 0;
                    $parseLine = $this->parseBetOffers($parseLine,$element_json);
                    if( $this->checkMarkets($parseLine) === true) {
                        if ($parseLine['isLive'] === 1) {
                            $parse['live'][] = $parseLine;
                        } else {
                            $parse['pre'][] = $parseLine;
                        }
                    }
                }
            }
        }
        return $parse;
    }

    /**
     * Summary of parseGroup
     * @param array $groupsIds - массив с идентификаторами групп
     * @return array - массив с обработанными спортивными линиями
     */
    private function parseGroup($groupsIds) {
        $betOffersUrlsArray=$this->returnBetOffersUrls($groupsIds); 
        $cUrlDatadata = (new GetDataWithCurl)->getMultiData($betOffersUrlsArray);
        $eventsIds = [];
        foreach ($cUrlDatadata as $element) {
            $json_data = (new ParserHelpers)->returnJsonString($element);
            if(is_object($json_data)) {
                if(property_exists($json_data, "error") === FALSE) {
                    foreach($json_data->events as $event) {
                        $eventsIds[] = $event;
                    }
                }
            }
        }
        return $this->parseEvents($eventsIds);
    }

    /**
     * Summary of parseGroups
     * @param array $groups_object - массив с группами
     * @param array $parse_groups - обработанный массив с группами
     * @return array - обработанный массив с группами
     */
    private function parseGroups($groups_object,$parse_groups=[]) : array {
        foreach ($groups_object as $group) {
            $groupsIds = $this->parseSportsGroups($group);
            if(count($groupsIds) > 0 ) {
               $parse_groups = $this->parseGroup($groupsIds);
            }
        }
        return $parse_groups;
    }

    /**
     * Summary of parse
     * @return array - результат работы парсера
     */
    public function parse() : array {
        $parse = [
            "live"   => [],
            "pre"    => []
        ];
        $groups_json_string = $this->getGoupsJsonString();
        if (is_object( $groups_object = (new ParserHelpers)->returnJsonString($groups_json_string) ) ) {
            $parse = $this->parseGroups($groups_object);
        }
        return $parse["pre"];
    }

    public function __destruct() {
        Logger::writeLog("info", Storage::APP_NAME . " finished");
    }

}