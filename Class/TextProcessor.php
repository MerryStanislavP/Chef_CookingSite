<?php
namespace App\Utils;

class TextProcessor {
    /**
     * Преобразует текст с разметкой markdown в HTML с использованием регулярных выражений
     * @param string $markdown Исходный текст в формате markdown
     * @return string HTML-разметка
     */
    public static function markdownToHTML($markdown) {

        $html = preg_replace('/^#\s+(.*?)$/m', '<h1>$1</h1>', $markdown);
        $html = preg_replace('/^##\s+(.*?)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^###\s+(.*?)$/m', '<h3>$1</h3>', $html);
        
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
        
        $html = preg_replace('/^\*\s+(.*?)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*?<\/li>)\s+(<li>)/', '$1$2', $html);
        $html = preg_replace('/(^<li>.*?<\/li>$)/m', '<ul>$1</ul>', $html);
        
        $html = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $html);
        
        $html = preg_replace('/\n\s*\n/', '</p><p>', $html);
        $html = '<p>' . $html . '</p>';
        
        return $html;
    }
    
    /**
     * Преобразует HTML в обычный текст с использованием регулярных выражений
     * @param string $html HTML-разметка
     * @return string Простой текст
     */
    public static function htmlToText($html) {
        $text = preg_replace('/\s+/', ' ', $html);
        
        $text = preg_replace('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', "\n\n$1\n\n", $text);
        $text = preg_replace('/<p[^>]*>(.*?)<\/p>/i', "\n$1\n", $text);
        $text = preg_replace('/<br[^>]*>/i', "\n", $text);
        $text = preg_replace('/<li[^>]*>(.*?)<\/li>/i', "• $1\n", $text);
        
        $text = preg_replace('/<[^>]*>/', '', $text);
        
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        return trim($text);
    }
    
    /**
     * Проверяет корректность email с использованием регулярных выражений
     * @param string $email Email для проверки
     * @return bool Результат проверки
     */
    public static function isValidEmail($email) {

        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return (bool) preg_match($pattern, $email);
    }
    
    /**
     * Проверяет сложность пароля:
     * - минимум 8 символов
     * - хотя бы одна цифра
     * - хотя бы одна буква в верхнем регистре
     * - хотя бы одна буква в нижнем регистре
     * @param string $password Пароль для проверки
     * @return bool Результат проверки
     */
    public static function isStrongPassword($password) {
        if (strlen($password) < 8) {
            return false;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Извлекает URL-адреса из текста
     * @param string $text Текст для анализа
     * @return array Массив найденных URL
     */
    public static function extractUrls($text) {
        $urlPattern = '/https?:\/\/[^\s]+/i';
        preg_match_all($urlPattern, $text, $matches);
        return $matches[0];
    }
}