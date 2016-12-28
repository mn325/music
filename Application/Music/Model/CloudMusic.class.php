<?php
namespace Music\Model;

class CloudMusic {

    public function curl_get($url)
    {
        $refer = "http://music.163.com/";
        $header[] = "Cookie: " . "appver=1.5.0.75771;";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $refer);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function music_search($word, $type)
    {
        $url = "http://music.163.com/api/search/pc";
        $post_data = array(
            's' => $word,
            'offset' => '0',
            'limit' => '20',
            'type' => $type,
        );
        $referrer = "http://music.163.com/";
        $URL_Info = parse_url($url);
        $values = array();
        $result = '';
        $request = '';
        foreach ($post_data as $key => $value) {
            $values[] = "$key=" . urlencode($value);
        }
        $data_string = implode("&", $values);
        if (!isset($URL_Info["port"])) {
            $URL_Info["port"] = 80;
        }
        $request .= "POST " . $URL_Info["path"] . " HTTP/1.1\n";
        $request .= "Host: " . $URL_Info["host"] . "\n";
        $request .= "Referer: $referrer\n";
        $request .= "Content-type: application/x-www-form-urlencoded\n";
        $request .= "Content-length: " . strlen($data_string) . "\n";
        $request .= "Connection: close\n";
        $request .= "Cookie: " . "appver=1.5.0.75771;\n";
        $request .= "\n";
        $request .= $data_string . "\n";
        $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);
        fputs($fp, $request);
        $i = 1;
        while (!feof($fp)) {
            if ($i >= 13) {
                $result .= fgets($fp);
            } else {
                fgets($fp);
                $i++;
            }
        }
        fclose($fp);
        return $result;
    }

    public function get_music_info($music_id)
    {
        $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
        return $this->curl_get($url);
    }

    public function get_artist_album($artist_id, $limit)
    {
        $url = "http://music.163.com/api/artist/albums/" . $artist_id . "?limit=" . $limit;
        return $this->curl_get($url);
    }

    public function get_album_info($album_id)
    {
        $url = "http://music.163.com/api/album/" . $album_id;
        return $this->curl_get($url);
    }

    public function get_playlist_info($playlist_id)
    {
        $url = "http://music.163.com/api/playlist/detail?id=" . $playlist_id;
        return $this->curl_get($url);
    }

    public function get_music_lyric($music_id)
    {
        $url = "http://music.163.com/api/song/lyric?os=pc&id=" . $music_id . "&lv=-1&kv=-1&tv=-1";
        return $this->curl_get($url);
    }

    public function get_mv_info($music_id)
    {
        $url = "http://music.163.com/api/mv/detail?id=" . $music_id . "&type=mp4";
        return $this->curl_get($url);
    }

    //歌曲搜索  $word = 文本  $limit = 返回条数
    public function get_search($word,$limit){
        $url = "http://music.163.com/api/search/get/web?csrf_token=";
        $curl = curl_init();
        $post_data = 'hlpretag=&hlposttag=&s='. $word . '&type=1&offset=0&total=true&limit=' . $limit;
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

        $header =array(
            'Host: music.163.com',
            'Origin: http://music.163.com',
            'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36',
            'Content-Type: application/x-www-form-urlencoded',
            'Referer: http://music.163.com/search/',
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $src = curl_exec($curl);
        curl_close($curl);
        return $src;
    }



}

?>