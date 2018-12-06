<?php
namespace app\common\util;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2712 2012-02-06 10:12:49Z liu21st $
// 
// @see 修改config 在pagepre和pagenxt添加两个&nbsp;
// @author liufei

class Page {
    public $rollPage = 5;       // 分页栏每组显示的页数
    public $parameter  ;        // 页数跳转时要带的参数
    public $listRows = 20;      // 默认列表每页显示行数
    public $firstRow	;       // 起始行数
    protected $totalPages  ;    // 分页总页数
    protected $totalRows  ;     // 总行数
    public $nowPage    ;        // 当前页数
    protected $coolPages   ;    // 分页的栏的总页数

    // 分页显示定制 
    public $config  =	array(
        'header' => '条',
        'first'  => '<span>首页</span>',
        'prev'   => '<span>上一页</span>',
        'next'   => '<span>下一页</span>',
        'last'   => '<span>尾页</span>',
        'theme1' =>
                '<div class="mall_pageNum flexed4">
                    <div class="flexed2">
                        %first%
                        %upPage%
                        %prePage%
                        %linkPage%
                        %nextPage%
                        %downPage%
                        %end%
                        <i>%nowPageRows%/%totalRow%</i>                        
                        %jump%
                    </div>             
                </div>'
    );
    // 默认分页变量名
    protected $varPage;
// %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%
    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows='',$parameter='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->varPage   = 'page' ;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }
    
    public function sshow($url,$page){
    	if(stripos($url, '{p}')!==false || stripos($url, urlencode('{p}'))!==false){
    		if($page >= 1){
    			$url = str_replace('{p}',$page,$url);
    			$url = str_replace(urlencode('{p}'),$page,$url);
    		}
    	}else {
    		$url = $url.$page;
    	}
    	return $url;
    }

    /**
    +----------------------------------------------------------
     * 分页显示输出
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     */
    public function show() {
        if(0 == $this->totalPages) return '';

        $url     = $this->parameter;
        //上下翻页字符串
        $upRow   = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;

        if ($upRow > 0) {
            $upPage    = "<a href='" . $this->sshow($url,$upRow) . "'>" . $this->config['prev'] . "</a>";
        } else {
            $upPage    = "";
        }

        if ($downRow <= $this->totalPages) {
            $downPage  = "<a href='" . $this->sshow($url,$downRow) . "'>" . $this->config['next'] . "</a>";
        } else {
            $downPage  = "";
        }

        $linkPage = "";
        $prePage  = "";
        $theFirst = "";
        $nextPage = "";
        $theEnd   = "";
        if ($this->totalPages <= 5) {
            for ($i = 1; $i <= $this->totalPages; $i++) {
                if ($i != $this->nowPage) {
                    if ($i <= $this->totalPages) {
                        $linkPage .= "<a href='" . $this->sshow($url, $i) . "'>" . $i . "</a>";
                    } else {
                        break;
                    }
                } else {
                    $linkPage .= "<a href='javascript:void()' class='action'>" . $i . "</a>";
                }
            }
        } else {
            if ($this->nowPage <= 3) {
                $upRow2    = 1;
                $downRow2  = 5;

                $theEndRow = $this->totalPages;
                $nextPage  = "<a href='" . $this->sshow($url,$this->totalPages - 1) . "' >" . ($this->totalPages - 1) . "</a>" .
                    "<a href='" . $this->sshow($url, $this->totalPages) . "' >" . $this->totalPages . "</a>";

                if ($downRow2 < $this->totalPages - 2) {
                    $nextPage  = "<em href='javascript:void(0);' >...</em>" . $nextPage;
                }

                $theEnd    = "<a href='" . $this->sshow($url,$theEndRow) . "' >" . $this->config['last'] . "</a>";

                for ($i = $upRow2; $i <= $downRow2; $i++) {
                    if ($i < 1) {
                        continue;
                    }

                    if ($i != $this->nowPage) {
                        $linkPage .= "<a href='" . $this->sshow($url, $i) . "'>" . $i . "</a>";
                    } else {
                        $linkPage .= "<a href='javascript:void()' class='action'>" . $i . "</a>";
                    }
                }
            } else if ($this->nowPage >= $this->totalPages - 2) {
                $upRow2   = $this->totalPages - 4;
                $downRow2 = $this->totalPages;

                $prePage  =
                    "<a href='" . $this->sshow($url, 1) . "' >1</a>" .
                    "<a href='" . $this->sshow($url, 2) . "' >2</a>";
                if ($upRow2 > 3) {
                    $prePage .= "<em href='javascript:void(0);' >...</em>";
                }

                $theFirst  = "<a href='" . $this->sshow($url,1) . "'>" . $this->config['first'] . "</a>";

                for ($i = $upRow2; $i <= $downRow2; $i++) {
                    if ($i > $this->totalPages) {
                        break;
                    }

                    if ($i != $this->nowPage) {
                        $linkPage .= "<a href='" . $this->sshow($url, $i) . "'>" . $i . "</a>";
                    } else {
                        $linkPage .= "<a href='javascript:void()' class='action'>" . $i . "</a>";
                    }
                }
            } else {
                $upRow2   = $this->nowPage - 2;
                $downRow2 = $this->nowPage + 2;

                $prePage  =
                    "<a href='" . $this->sshow($url, 1) . "' >1</a>" .
                    "<a href='" . $this->sshow($url, 2) . "' >2</a>";
                if ($upRow2 > 3) {
                    $prePage .= "<em href='javascript:void(0);' >...</em>";
                }

                $theFirst  = "<a href='" . $this->sshow($url,1) . "'>" . $this->config['first'] . "</a>";

                $theEndRow = $this->totalPages;
                $nextPage  = "<a href='" . $this->sshow($url,$this->totalPages - 1) . "' >" . ($this->totalPages - 1) . "</a>" .
                    "<a href='" . $this->sshow($url, $this->totalPages) . "' >" . $this->totalPages . "</a>";

                if ($downRow2 < $this->totalPages - 2) {
                    $nextPage  = "<em href='javascript:void(0);' >...</em>" . $nextPage;
                }

                $theEnd    = "<a href='" . $this->sshow($url,$theEndRow) . "' >" . $this->config['last'] . "</a>";

                for ($i = $upRow2; $i <= $downRow2; $i++) {
                    if ($i <= 2) {
                        continue;
                    }

                    if ($i > $this->totalPages - 2) {
                        break;
                    }

                    if ($i != $this->nowPage) {
                        $linkPage .= "<a href='" . $this->sshow($url, $i) . "'>" . $i . "</a>";
                    } else {
                        $linkPage .= "<a href='javascript:void()' class='action'>" . $i . "</a>";
                    }
                }
            }
        }

        if ($this->totalPages > 1) {
            $jump = '<input type="text" id="gotopagediv" maxlength="4" class="mall_text2" placeholder="" onkeyup="this.value=this.value.replace(/[^\d]/g,\'\');if(this.value<1)this.value=1;">
                          <a href="javascript:void(0)" onclick="document.location.href=_for_this_page_href + $(\'#gotopagediv\').val(); ">跳转</a>';
        } else {
            $jump = "";
        }

        $theme = $this->config['theme1'];

        $pageStr = str_replace(
            [
                '%header%',
                '%nowPageRows%',
                '%totalRow%',
                '%totalPage%',
                '%upPage%',
                '%downPage%',
                '%first%',
                '%prePage%',
                '%linkPage%',
                '%nextPage%',
                '%end%',
                '%jump%'
            ],
            [
                $this->config['header'],
                $this->nowPage * $this->listRows > $this->totalRows ? $this->totalRows : $this->nowPage * $this->listRows,
                $this->totalRows,
                $this->totalPages,
                $upPage,
                $downPage,
                $theFirst,
                $prePage,
                $linkPage,
                $nextPage,
                $theEnd,
                $jump
            ],
            $theme
        );

        return $pageStr;
    }
}