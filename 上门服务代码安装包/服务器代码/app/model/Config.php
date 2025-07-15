<?php

namespace app\model;

use think\Model;

class Config extends Model
{

    protected $connection = 'mysql';

    protected $pk = 'id';

    protected $name = 'config';


    public static function getconfig($module = '')
    {

        if (empty($module)) {
            $module = 'common';
        }

        $config = self::where(['weid' => weid(), 'module' => $module])
            ->find();
        if (!empty($config)) {
            $config = $config->toArray();
            $settings = self::setconfigdata($config);
        }

        return $settings;
    }
    public static function getsitesetupconfig($module = '')
    {

        $config = self::where(['weid' => 0, 'module' => $module])
            ->find();
        if (!empty($config)) {
            $config = $config->toArray();
            $settings = self::setconfigdata($config);
        }

        return $settings;
    }
    static function setconfigdata($config)
    {
        if (!empty($config['settings'])) {
            $settings = iunserializer($config['settings']);

            if ($config['module'] == 'common') {
                if (empty($settings['filesize'])) {
                    $settings['filesize'] = 10;
                }

                if (empty($settings['filesize'])) {
                    $settings['filesize'] = 10;
                }

                if (empty($settings['filetype'])) {
                    $settings['filetype'] = 'jpg,jpeg,png,gif';
                }

                if (empty($settings['storedistance'])) {
                    $settings['storedistance'] = 200;
                }
                if (empty($settings['technicaldistance'])) {
                    $settings['technicaldistance'] = 200;
                }
                if (empty($settings['automaticsettlement'])) {
                    $settings['automaticsettlement'] = 7;
                }
            }
        }
        $settings['id'] = $config['id'];
        $settings['weid'] = $config['weid'];

        if ($settings['applypic']) {
            $settings['applypic'] = toimg($settings['applypic']);
        }
        if ($settings['poster']) {
            $settings['poster'] = toimg($settings['poster']);
        }

        return $settings;
    }
}
