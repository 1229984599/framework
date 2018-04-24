<?php
namespace app\controller;
use lib\Controller;
use QL\QueryList;

class Crawl extends Controller
{
    public $domain; //爬取网站域名
    private $ql;

    public function __construct()
    {
        $this->ql = new QueryList();
        $this->domain = 'https://www.360kan.com';
    }

    //获取页面详情内容

    /**
     * @param $url  待采集页面链接
     * @param $rule 采集规则
     * @param string $range 采集区域,默认为空
     * @return array
     */
    public function get_content($url,$rule,$range=''){
        return $this->ql->get($url)->rules($rule)->range($range)->encoding('UTF-8')->query()->getData()->all();
    }

    /**
     * 获取电影详情页面的内容
     * @param $detial_url   页面详情地址
     * @return array
     */
    public function get_movie_content($detail_url){
        $html = $this->ql->get($detail_url);
        $data = [
            'desc' => $html->find('.item-desc:eq(0)')->text(),
            'title' => $html->find('.title-left > h1')->text(),
        ];
        $range = '.top-list-zd  a[href!="javascript:;"]';
        $rule = [
            'href' => ['','href'],
            'num' => ['','text']
        ];
        $data['site']['站点名称:'] =  $html->rules($rule)->range($range)->query()->getData()->all();
        //释放内存占用
        $this->ql->destruct();
        return $data;
    }

    /**
     * 获取综艺详情页面内容
     * @param $detail_url   详情页面链接
     * @return array
     */
    public function get_zy_content($detail_url){
        $html = $this->ql->get($detail_url);
        $data = [
            'desc' => $html->find('.item-desc:eq(0)')->text(),
            'title' => $html->find('.title-left > h1')->text(),
        ];
        $site = $html->find('.now-site > span')->text();
        $range = '.juji-main > #js-year-all > .list > li';
        $rule = [
            'num' => ['span.w-newfigure-hint', 'text'],
            'href' => ['a','href']
        ];
        $data['site'][$site] =  $html->rules($rule)->range($range)->query()->getData()->all();
        //释放内存占用
        $this->ql->destruct();
        return $data;
    }

    /**
     * 获取电视剧或动漫的详情页面内容
     * @param $detail_url 页面详情链接
     * @param $type  页面类型 2为电视剧 4为动漫
     * @return array   返回集数及其链接
     */
    public function get_detail_content($detail_url,$type=2){
        if($type=='dongman'){
            $type = 4;
        }
        $html = $this->ql->get($detail_url);
        $tmp = $html->find('.num-tab > .num-tab-main')->htmls()->all();
        $data = [
            'desc' => $html->find('.item-desc:eq(0)')->text(),
            'title' => $html->find('.title-left > h1')->text(),
        ];
        preg_match_all('#/(\w+)\.html#i',$detail_url,$arr);
        $item = $arr[1][0];     #当前页面的id
        preg_match_all('#ensite\":\"(\w+)\"#i',$html->getHtml(),$arr1);
        $en_site = $arr1[1];    #站点英文名称

        //获取页面集数
        $n = 0;
        foreach ($en_site as $site) {
            if($n++>=3){break;}
            $url = "https://www.360kan.com/cover/switchsite?site={$site}&id={$item}&category={$type}";
//            $content = json_decode(file_get_contents($url),true);
            $content = $this->ql->gethtml($url);
//            dump($content);die;
            $count = $this->ql->html($content)->find('.num-tab-main')->count();
            if($count<=1){
                $range = '.num-tab-main:eq(0) > a[href!="#"]';
                $rule = [
                    'href' =>['','href'],
                    'num' => ['','data-num','',function($num){
                        if($num < 10){
                            $num = '0'.$num;
                        }
                        return $num;
                    }]
                ];
                $data['site'][$site] = $this->ql->html($content)->rules($rule)->range($range)->query()->getData()->all();
                continue;
            }
            $range = '.num-tab-main:gt(0) > a[href!="#"]';
            $rule = [
                'href' =>['','href'],
                'num' => ['','text','',function($num){
                    if($num < 10){
                        $num = '0'.$num;
                    }
                    return $num;
                }]
            ];
            $data['site'][$site] = $this->ql->html($content)->rules($rule)->range($range)->query()->getData()->all();
        }
        //释放内存占用
        $this->ql->destruct();
        return $data;
    }

    //获取页面详情内容结束

    /**
     * 获取页面列表
     * @param $type 视频分类(电视剧,电影等)
     * @param int $page 页码
     * @param string $cate  栏目分类(视频类型)
     * @param string $year  视频年代
     */
    public function get_page_list($type,$page=1,$cate='all',$year='all'){
        $page_url = "https://www.360kan.com/{$type}/list?cat={$cate}&year={$year}&pageno={$page}";
        $range = '.list > li.item';
        $rule = [
            'title' => ['.title > .s1','text'],
            'js' => ['.hint','text'],
            'star' => ['.star','text'],
            'img' => ['.cover > img','src'],
            'url' => ['a','href','',function($url){
                return base64_encode($this->domain . $url);
            }],
        ];
        $data = $this->get_content($page_url, $rule, $range);
        //释放内存占用
        $this->ql->destruct();
        return $data;
    }

    /**
     * 获取首页数据
     * @param $type 获取首页视频类型
     * @param int $num  获取数据数量
     */
    public function get_index_content($type,$num=10,$rank='rankhot'){
        $num -= 1;
        $url = "https://www.360kan.com/{$type}/list?rank={$rank}";
        $range = ".list > li.item:lt({$num})";
        $rule = [
            'title' => ['.title > .s1','text'],
            'js' => ['.hint','text'],
            'star' => ['.star','text'],
            'img' => ['.cover > img','src'],
            'url' => ['a','href','',function($url){
                return base64_encode($this->domain . $url);
            }],
        ];
        $data = $this->get_content($url, $rule, $range);
        //释放内存占用
        $this->ql->destruct();
        return $data;
    }

    #获取首页数据标题
    public function get_index_hot($type,$num=10,$rank='rankhot'){
        $num -= 1;
        $url = "https://www.360kan.com/{$type}/list?rank={$rank}";
        $range = ".list > li.item:lt({$num})";
        $rule = [
            'title' => ['.title > .s1','text'],
            'url' => ['a','href','',function($url) {
                return base64_encode($this->domain . $url);
            }]
        ];
        $data = $this->get_content($url, $rule, $range);
        //释放内存占用
        $this->ql->destruct();
        return $data;
    }

    /**
     * @param $kw   关键字(要获取的影片)
     * @return array
     */
    public function get_search_content($kw){
        $url = "https://so.360kan.com/index.php?kw={$kw}";
        $rule = [
            'title' => ['.cont > .title','text','-.playtype'],
            'img' => ['.b-mainpic img','src'],
            'url' => ['.cont > .title > a','href','',function($url){
                return base64_encode($url);
            }],
            'from' => ['.cont > .title > .playtype','text','',function($from){
                switch ($from){
                    case '[动漫]': $from = 'dongman';break;
                    case '[综艺]': $from = 'zongyi';break;
                    case '[电视剧]': $from = 'tv';break;
                    case '[电影]': $from = 'movie';break;
                    default: $from = '';break;
                }
                return $from;
            }],
            'js' => ['.cont > ul> li:contains("剧集") > span','text']
        ];
        $data = $this->get_content($url,$rule);
        return $data;
    }

    /**
     * 获取总页数
     * @param $type
     * @return mixed
     */
    public function get_page_num($type){
        $page_url = "https://www.360kan.com/{$type}/list";
        $num = $this->ql->get($page_url)->find('.ew-page > a:eq(6)')->text();
        return $num;
    }
}