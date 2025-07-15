<?php

namespace app\index\controller;

class LocalpicController extends Base
{

    public function index()
    {

        $pic = input('post.pic', '', 'serach_in');

        if (!strpos($pic, '//' . getdomainname())) {
            $picfile =  explode('/', $pic);
            $filename = end($picfile);
            $content = file_get_contents($pic);
            $save_to = config('filesystem.disks.public.root') . '/' . $filename;
            file_put_contents($save_to, $content);

            $pic = toimg('public/uploads/' . $filename);
        }
        return $this->json(['data' => $pic]);
    }
}
