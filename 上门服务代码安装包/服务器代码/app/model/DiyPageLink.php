<?php

namespace app\model;

class DiyPageLink
{
    public static function setlink($link)
    {
        if ($link['ptype'] == 'pageslist') {
            $bm = BottomMenuOriginal::find($link['id']);

            if (!empty($bm)) {
                $link['path'] = $bm->url;
            }
            // var_dump($link);
        } elseif ($link['ptype'] == 'diypageslist') {
            $dp = DiyPage::find($link['id']);
            if (!empty($dp)) {
                $link['path'] = "/pages/index/index?id=" . $dp->id;
            }
        } elseif ($link['ptype'] == 'custom') {
            $link['path'] = $link['path'];
        } else {
            $link['path'] = self::linklist($link['ptype'])['path'] . $link['id'];
        }

        $appdata['lan'] = Author()::getlan();
        return $link;
    }
    public static function linklist($type = '')
    {

        $data['pageslist'] = [
            'txt' => '栏目面页',
            'path' => '',
            'icon' => 'el-icon-shopping-bag-1'
        ];

        $data['diypageslist'] = [
            'txt' => 'DIY面页',
            'path' => '',
            'icon' => 'el-icon-shopping-bag-1'
        ];

        $data['servicelist'] = [
            'txt' => '服务列表',
            'v3' => 1,
            'path' => '/pages/goodsList/goodsList?cid=',
            'icon' => 'el-icon-shopping-bag-1'
        ];

        $data['serviceDetail'] = [
            'txt' => '服务详情',
            'v3' => 1,
            'path' => '/pages/goodsDetail/goodsDetail?id=',
            'icon' => 'el-icon-shopping-bag-1'
        ];
        $appdata['lan'] = Author()::getlan();
        $data['goodslist'] = [
            'path' => '/pages/goodsList/goodsList?cid=',
            'txt' => '商品列表',
            'icon' => 'el-icon-shopping-bag-1'
        ];

        $data['goodsDetail'] = [
            'txt' => '商品详情',
            'path' => '/pages/goodsDetail/goodsDetail?id=',
            'icon' => 'el-icon-shopping-bag-1'
        ];

        $data['technicalDetail'] = [
            'txt' => '师傅详情',
            'v3' => 1,
            'path' => '/pages/technical/details?id=',
            'icon' => 'el-icon-shopping-bag-1'
        ];

        $data['articlelist'] = [
            'txt' => '文章列表',
            'path' => '/pages/article/list?cid=',
            'icon' => 'el-icon-message'
        ];

        $data['articleDetail'] = [
            'txt' => '文章详情',
            'path' => '/pages/article/detail?id=',
            'icon' => 'el-icon-message'
        ];

        $data['custom'] = [
            'txt' => '自定义链接',
            'path' => '',
            'icon' => 'el-icon-set-up'
        ];

        if (empty($type)) {
            return $data;
        } else {
            return $data[$type];
        }
    }

    public static function getFields($ptype)
    {

        $data['pageslist'] = [
            [
                'viewmingcheng' => '名称',
                'fieldsmingcheng' => 'title',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '页面',
                'fieldsmingcheng' => 'url',
                'inputtype' => '',
            ]

        ];
        $data['diypageslist'] = [
            [
                'viewmingcheng' => '名称',
                'fieldsmingcheng' => 'title',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '页面',
                'fieldsmingcheng' => 'url',
                'inputtype' => '',
            ]

        ];

        $data['servicelist'] = [
            [
                'viewmingcheng' => '编号',
                'fieldsmingcheng' => 'id',
                'width' => '60',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '图片',
                'fieldsmingcheng' => 'image',
                'width' => '150',
                'inputtype' => 'pics',
            ],
            [
                'viewmingcheng' => '名称',
                'fieldsmingcheng' => 'title',
                'inputtype' => '',
            ]
        ];
        $data['serviceDetail'] = [
            [
                'viewmingcheng' => '编号',
                'fieldsmingcheng' => 'id',
                'width' => '60',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '类目',
                'fieldsmingcheng' => 'cat_id',
                'width' => '150',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '图片',
                'fieldsmingcheng' => 'image',
                'width' => '100',
                'inputtype' => 'pics',
            ],

            [
                'viewmingcheng' => '名称',
                'fieldsmingcheng' => 'name',
                'inputtype' => '',
            ],
        ];
        $appdata['lan'] = Author()::getlan();
        $data['goodslist'] = [
            [
                'viewmingcheng' => '编号',
                'fieldsmingcheng' => 'id',
                'width' => '60',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '图片',
                'fieldsmingcheng' => 'image',
                'width' => '150',
                'inputtype' => 'pics',
            ],
            [
                'viewmingcheng' => '名称',
                'fieldsmingcheng' => 'title',
                'inputtype' => '',
            ]
        ];
        $data['goodsDetail'] = [
            [
                'viewmingcheng' => '编号',
                'fieldsmingcheng' => 'id',
                'width' => '60',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '类目',
                'fieldsmingcheng' => 'cat_id',
                'width' => '150',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '图片',
                'fieldsmingcheng' => 'image',
                'width' => '100',
                'inputtype' => 'pics',
            ],

            [
                'viewmingcheng' => '名称',
                'fieldsmingcheng' => 'name',
                'inputtype' => '',
            ],
        ];
        $data['technicalDetail'] = [
            [
                'viewmingcheng' => '编号',
                'fieldsmingcheng' => 'id',
                'width' => '60',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '头像',
                'fieldsmingcheng' => 'touxiang',
                'width' => '100',
                'inputtype' => 'pics',
            ],

            [
                'viewmingcheng' => '姓名',
                'fieldsmingcheng' => 'title',
                'inputtype' => '',
            ],
        ];
        $data['articlelist'] = [
            [
                'viewmingcheng' => '编号',
                'fieldsmingcheng' => 'id',
                'width' => '60',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '名称',
                'fieldsmingcheng' => 'title',
                'inputtype' => '',
            ]
        ];
        $data['articleDetail'] = [
            [
                'viewmingcheng' => '编号',
                'fieldsmingcheng' => 'id',
                'width' => '60',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '分类',
                'fieldsmingcheng' => 'cid',
                'width' => '150',
                'inputtype' => '',
            ],
            [
                'viewmingcheng' => '标题',
                'fieldsmingcheng' => 'title',
                'inputtype' => '',
            ],
        ];

        return $data[$ptype];
    }
}
