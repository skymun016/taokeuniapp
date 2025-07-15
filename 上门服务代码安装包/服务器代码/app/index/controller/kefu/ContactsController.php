<?php

namespace app\index\controller\kefu;

use think\exception\ValidateException;

class ContactsController extends Base
{
    function info()
    {
        return $this->json($this->userInfo);
    }
}
