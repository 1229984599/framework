<?php
namespace app\controller;
use app\controller\Crawl;
class Index extends Crawl
{
    public function index(){
        $data = $data = $this->get_detail_content('https://www.360kan.com/tv/QLVqan7lRz8mMX.html');
//        dump($data);
        return $this->view('index', $data);
    }
}