<?php

namespace Validator;

class ValidationLanguage
{
    private static $lang = 'en';

    public static $messages = [];

    /**
     * @return mixed
     */
    public static function getLang()
    {
        return self::$lang;
    }

    /**
     * @return array
     */
    public static function getMessages()
    {
        $messages = [
            'fr' => include('langs/fr.php'),
            'en' => include('langs/en.php'),
            'es' => include('langs/es.php')
        ];

        return $messages[self::getLang()];
    }

    /**
     * @param mixed $lang
     */
    public static function setLang($lang)
    {
        self::$lang = $lang;
    }

}
