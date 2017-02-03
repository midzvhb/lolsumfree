<?php
/*
credit : midzvhb
*/


error_reporting(0);
class LOLSummon
{
    public function __constructor()
    {
    }
    private function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    private function getCurrentMondayInThisWeek()
    {
        return date('m-d-Y', time() - ((date('w')-1) * 86400));
    }

    public function updateFreeSummon()
    {
        $arrContextOptions=array(
        "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
    );
        $curMon = $this->getCurrentMondayInThisWeek();                                 // LẤY NGÀY/THÁNG/NĂM THỨ 2 HIỆN TẠI CỦA TUẦN
        $array = array();
        try {
            $curData = file_get_contents("./updateDate.json");                        // LẤY DATA JSON
            $parserData = json_decode($curData, true);                                // DECODE JSON
        if ($curMon != $parserData['date_update']) {                                  // NẾU NGÀY THỨ 2 HIỆN TẠI KHÔNG TRÙNG VỚI DỮ LIỆU TRƯỚC ĐÓ THÌ

            /* UPDATE DỮ LIỆU MỚI */
            $data = file_get_contents("https://lienminh.garena.vn/news", false, stream_context_create($arrContextOptions));
            $data = $this->get_string_between($data, "<ul>", "</ul>");
            $array['date_update'] = $curMon;
            $array['data'] = $this->arrFilter(str_replace("\n", "", $data));
            file_put_contents("./updateDate.json", json_encode($array));
            echo json_encode($array);
        } else {
            echo $curData;
        }
        } catch (Exception $e) {
            $array['date_update'] = $curMon;
            $array['data'] = "Có lỗi xảy ra....";
            echo json_encode($array);
        }
    }
    private function arrFilter($data)
    {
        $temp = "";
        preg_match_all("/<img[^>]*>/", $data, $out, PREG_PATTERN_ORDER);
        foreach ($out[0] as $item) {
            $temp = $temp.$item."\n";
        }
        preg_match_all('/<img[^>]+?title="([^"]+)".*>/', $temp, $out, PREG_PATTERN_ORDER);
        $temp = "";
        $result = array();
        $index = 0;
        foreach ($out[1] as $item) {
            $index++;
            $result[$index] = $item;
        }
        return $result;
    }
}

$objAPI = new LOLSummon();
$objAPI->updateFreeSummon();
?>
