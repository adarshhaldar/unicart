<?php

namespace Unicart\Classes;

use Unicart\Exceptions\LocaleException;

final class Locale
{
    private static $defaultLocale = 'en';

    private static $locale = 'en';

    private static $locales = [
        'ar',
        'cs',
        'de',
        'en',
        'fr',
        'hi',
        'ja',
        'nl',
        'ru',
        'zh'
    ];

    /**
     * Set locale
     * 
     * @param string $locale The locale that has to be set. Default set to en.
     * 
     * @return void
     */
    public static function setLocale(string $locale = 'en'): void
    {
        self::$locale = in_array($locale, self::$locales) ? $locale : self::$defaultLocale;
    }

    /**
     * Get current locale
     * 
     * @return string
     */
    public static function getLocale(): string
    {
        return self::$locale;
    }

    /**
     * Get list of locales
     * 
     * @return array
     */
    public static function getLocales(): array
    {
        return self::$locales;
    }

    /**
     * Get message by dot notation 
     * 
     * @param array $messages The list of messages.
     * @param string $key Key for the array.
     * 
     * @return null|string|array
     */
    private static function getMessageByDotKey(array $messages, string $key): null|string|array
    {
        foreach (explode('.', $key) as $segment) {
            if (!is_array($messages) || !array_key_exists($segment, $messages)) {
                return null;
            }
            $messages = $messages[$segment];
        }
        return $messages;
    }

    /**
     * Translate the message
     * 
     * @param string $key Key for the message array.
     * @param array $replacement List of replacements.
     * @param null|string $locale Locale to force translate the message into mentioned locale.
     * 
     * @return string
     */
    public static function translate(string $key, array $replacement = [], null|string $locale = null): string
    {
        $locale = $locale ? (in_array($locale, self::$locales) ? $locale : self::getLocale()) : self::getLocale();

        $file = file_exists(__DIR__ . "/../Lang/{$locale}.php") ? __DIR__ . "/../Lang/{$locale}.php" : __DIR__ . "/../Lang/en.php";

        if (!file_exists($file)) {
            throw new LocaleException("Locale file not found: {$file}");
        }

        $messages = require $file;
        $message = self::getMessageByDotKey($messages, $key);

        if (!isset($message))  return $key;

        if (count($replacement)) {
            foreach ($replacement as $replace => $with) {
                $message = str_replace(':' . $replace, $with, $message);
            }
        }

        return $message;
    }
}
