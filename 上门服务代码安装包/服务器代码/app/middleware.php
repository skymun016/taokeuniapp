<?php
// 全局中间件定义文件
return [
    //跨域中间件
    app\samos\http\middleware\SamosAllowMiddleware::class,
    //think\middleware\AllowCrossDomain::class,
	think\middleware\SessionInit::class
];
