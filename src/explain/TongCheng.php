<?php
/**
 *
 */

namespace ExplainHtml;
use QL\QueryList;
class TongCheng{

    static protected $job_tag_role = '/<span><i><\/i>([^<]+)<\/span>/';

    // http://mm.58.com/yewu/28953062755900x.shtml
    static protected $job_item_url_role = '/http:\/\/(\w+).58.com\/(\w+)\/(\d+)x.shtml/';


    static public function m_job_role(){
        return [
            "title"=>['.d_title','text'],
            "position"=>['.job_con .attrValue>a:eq(0)','text'],
            "location"=>['.job_con .dizhiValue','text','',function($content){
                return trim($content);
            }],
            "description"=>['.dis_con p','text'],

            "company_name"=>['.company .c_tit a:eq(0)','html'],
            "company_url"=>['.company .c_tit a:eq(0)','href'],

//            "mobile"=>['.ffield #contact_phone','phoneno'],

            "pay"=>['.price .pay','text'],
            "pub_at"=>['.pub_date span:eq(1)','text'],
            "tag"=>['.fulivalue','html','',function($content){
                if($content && preg_match_all(self::$job_tag_role,$content,$tags)){
                    return $tags[1];
                }
                return $content;
            }],
        ];
    }

    static public function jobInfo($html)
    {
        $hj = QueryList::Query($html,self::m_job_role());
        $html = $hj->getHtml(false);
        $linkman =  self::match_find($html,1,'/{"I":"5333","V":"(.+?)"},/');
        $mobile =  self::match_find($html,1,'/id="contact_phone" phoneno="(\d+?)"/');
        $email =  self::match_find($html,1,'/{"I":"5360","V":"(.+?)"},/');
        //  $mobile = $data[0];
        return array_merge($hj->data[0],compact('linkman','email','mobile'));
    }


    // 从58同城职位列表中获取职位详细数据 (pc版)
    static public function jobList($html)
    {
        $links = QueryList::Query($html,[
            'link'=>['.infolist dt a','href','',function($link){
                if( preg_match(self::$job_item_url_role,$link,$val) ){
                    // 组装成手机版的详细地址 (pc的手机号加密了)
                    return "http://m.58.com/{$val[1]}/{$val[2]}/{$val[3]}x.shtml";
                }
                return false;
            }]
        ])->getData(function($item){
            return $item['link'];
        });
        return array_filter($links);
    }

    /**
     * 简单的字符匹配
     * @param $str
     * @param $i int 取位
     * @param $i string  正则式
     */
    function match_find($str, $i = 1, $rep = '/(\d+)\D+(\d+)\D+/'){
        preg_match_all($rep,$str,$arr);
        if(isset($arr[$i][0])){
            return $arr[$i][0];
        }
        return false;
    }
}