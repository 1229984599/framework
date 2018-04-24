<?php
namespace lib;
class Controller
{
    /**
     * @param string $file  view文件夹下的文件(包含后缀)
     * @param array $data   所需要赋值的变量,以数组形式传入
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function view(string $file,Array $data)
    {
        $arr = CACHE?['cache' => BASE_PATH.'/cache',]:[];
        $loader = new \Twig_Loader_Filesystem(BASE_PATH.'/app/view');
        $twig = new \Twig_Environment($loader, $arr);
        echo $twig->render($file.'.html', $data);
    }
}