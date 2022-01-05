<?php
namespace Deform\Util;

/**
 * string handling utility functions
 */
class Strings
{
    const ENCODING_ISO_8859_1 = "ISO-8859-1";
    const ENCODING_UTF_8 = "UTF-8";
    const ENCODING_ASCII = "ASCII";

    /**
     * get a class name for an object or class name *without* it's namespace
     *
     * @param string|object $object
     *
     * @return string
     * @throws \Exception
     */
    public static function getClassWithoutNamespace($object): string
    {
        if (is_object($object)) {
            $class_name = get_class($object);
        }
        elseif (is_string($object)) {
            $class_name = $object;
        }
        else {
            throw new \Exception("Parameter must be an object or class name");
        }

        $idx = strrpos($class_name, "\\");
        if ($idx === false) {
            return $class_name;
        }
        return substr($class_name, $idx + 1);
    }

    /**
     * check if one string starts with another. case sensitive.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    /**
     * check if one string ends with another. case sensitive.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Returns the given lower_case_and_underscored_word as a CamelCased word.
     *
     * @param string $lowerCaseAndUnderscoredWord Word to camelize
     *
     * @return string Camelized word. LikeThis.
     * @link http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html#Inflector::camelize
     */
    public static function camelise(string $lowerCaseAndUnderscoredWord): string
    {
        return str_replace(' ', '', self::humanise($lowerCaseAndUnderscoredWord));
    }

    /**
     * Returns the given camelCasedWord as a character separated word, default is underscored_word (which works well
     * with camelise above!)
     *
     * @param string $camelCasedWord Camel-cased word
     * @param string $separator
     *
     * @return string
     * @link http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html#Inflector::underscore
     */
    public static function separateCased(string $camelCasedWord, string $separator='_'): string
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', $separator.'\\1', $camelCasedWord));
    }

    /**
     * Returns the given underscored_word_group as a Human Readable Word Group.
     * (Underscores are replaced by spaces and capitalized following words.)
     *
     * @param string $lowerCaseAndUnderscoredWord String to be made more readable
     *
     * @return string Human-readable string
     * @link http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html#Inflector::humanize
     */
    public static function humanise(string $lowerCaseAndUnderscoredWord): string
    {
        return ucwords(str_replace(array('_', '-'), ' ', $lowerCaseAndUnderscoredWord));
    }

    /**
     * @param string $lowerCaseAndUnderscoredWord
     *
     * @return string|string[]
     */
    public static function humaniseTitle(string $lowerCaseAndUnderscoredWord)
    {
        $search = [
            'And ' => 'and ',
            'The ' => 'the ',
            'Of ' => 'of ',
            'In ' => 'in ',
            'As ' => 'as ',
            'A ' => 'a ',
            'For ' => 'for ',
        ];
        return str_replace(array_keys($search), array_values($search), self::humanise($lowerCaseAndUnderscoredWord));
    }

    /**
     * filter out all non alphanumeric characters from a string
     *
     * @param string $text
     * @param bool|string $allowChars
     *
     * @return string
     */
    public static function alphanumeric(string $text, $allowChars = false): string
    {
        return preg_replace("/[^a-zA-Z0-9" . ($allowChars ? preg_quote($allowChars) : "") . "]+/", "", $text);
    }

    /**
     * trim whitespace from start and end of a string and collapse any multiple internal whitespace to single spaces
     *
     * @param string $text
     *
     * @return string
     */
    public static function trimInternal(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * make a url from an arbitrary string
     *
     * @param string $text
     * @param string $spaceReplacementChar
     *
     * @return string
     */
    public static function makeUrl(string $text, string $spaceReplacementChar = "-"): string
    {
        $alpha_only = self::alphanumeric(strtolower($text), " ");
        $clean_whitespace = self::trimInternal($alpha_only);
        return str_replace(" ", $spaceReplacementChar, $clean_whitespace);
    }

    /**
     * summarize some text adding an ellipsis and a tooltip if text is longer than desired
     *
     * @param string $text
     * @param int $maxLength
     * @param bool $includeTooltip
     * @param string $hellip
     *
     * @return string
     */
    public static function summarize(string $text, int $maxLength, bool $includeTooltip = true, string $hellip = "&hellip;"): string
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        return "<span" . ($includeTooltip ? " title='" . $text . "'" : "") . ">" . substr($text, 0, $maxLength) . $hellip . "</span>";
    }

    /**
     * obtain summary string for a given keyword
     *
     * @param string $text
     * @param string $keyword
     * @param int $maxLength
     *
     * @return string
     */
    public static function summarizeForKeyword(string $text, string $keyword, int $maxLength = 100): string
    {
        $text = self::trimInternal(htmlspecialchars_decode(strip_tags($text)));
        if (strlen($text) < strlen($keyword)) {
            return $text;
        }
        $keyword_index = stripos($text, $keyword);
        if ($keyword_index === false) {
            if ($maxLength == false) {
                return $text;
            }
            return self::summarize($text, $maxLength);
        }
        if ($maxLength == false) {
            $start_index = 0;
            $end_index = strlen($text);
        } else {
            $start_index = $keyword_index - floor($maxLength / 2);
            $end_index = $keyword_index + floor($maxLength / 2);
            if ($start_index < 0) {
                $end_index -= $start_index;
                $start_index = 0;
            }
        }

        $text_len = strlen($text);
        $end_index = min($text_len, $end_index);

        $summary_length = $end_index - $start_index;

        $summary = substr($text, $start_index, $summary_length);

        $results = preg_split("/" . preg_quote($keyword) . "/i", $summary, null, PREG_SPLIT_OFFSET_CAPTURE);
        $rebuild = "";
        foreach ($results as $result) {
            $match_index = strlen($result[0]) + $result[1];
            $rebuild .= $result[0];
            $strong_part = substr($summary, $match_index, strlen($keyword));
            if ($strong_part !== false) {
                $rebuild .= "<strong>" . $strong_part . "</strong>";
            }
        }

        return (($start_index > 0) ? "&hellip;" : "")
        . $rebuild
        . (($end_index < strlen($text) ? "&hellip;" : ""));
    }

    /**
     * generate a link which can be easily disabled (useful for reusing views for emails)
     *
     * @param string $href
     * @param string $text
     * @param bool $disabled if true disable the text
     *
     * @return string
     */
    public static function link(string $href, string $text, bool $disabled = false): string
    {
        return $disabled ? $text : "<a href='" . $href . "'>" . $text . "</a>";
    }

    /**
     * explodes a string by specified delimiter and then implodes the specified number of parts
     *
     * @param string $delimiter
     * @param string $string
     * @param int $count
     *
     * @return string
     */
    public static function getDelimitedParts(string $string, string $delimiter, int $count): string
    {
        if (strpos($string, $delimiter) === false) {
            return $string;
        }
        $parts = explode($delimiter, $string);
        $parts = array_slice($parts, 0, min($count, count($parts)));
        return implode($delimiter, $parts);
    }

    /**
     * comma separates either an array, or an array generated from a string with the number of required elements
     *
     * @param string|string[] $items
     * @param int|bool $count
     * @param string $separator
     *
     * @return string
     */
    public static function commaSeparate($items, $count = false, string $separator = ","): string
    {
        if (is_string($items)) {
            if ($count == false) {
                return $items;
            }
            $items = array_pad([], $count, $items);
        }
        if ($count !== false) {
            if ($count < count($items)) {
                $items = array_slice($items, 0, $count);
            } else {
                if ($count > count($items)) {
                    $original_items = $items;
                    $index = 0;
                    while (count($items) < $count) {
                        $items[] = $original_items[$index++];
                        if ($index == count($original_items)) {
                            $index = 0;
                        }
                    }
                }
            }
        }
        return implode($separator, $items);
    }

    /**
     * similar to ucwords(strtolower(...)) except that specified items are ignored (useful for preserving acronyms etc.)
     *
     * @param string $text
     * @param string|string[] $exceptWords
     *
     * @return string
     *@throws \Exception
     *
     */
    public static function ucwordsExcept(string $text, $exceptWords): string
    {
        if (is_string($exceptWords)) {
            $exceptWords = [$exceptWords];
        }
        foreach ($exceptWords as $except_word) {
            if (strpos($except_word, " ") !== false) {
                throw new \Exception("Excepted words containing spaces don't work!");
            }
        }
        $text_parts = explode(" ", $text);
        $new_text_parts = [];
        foreach ($text_parts as $text_part) {
            $new_text_parts[] = in_array($text_part, $exceptWords) ? $text_part : ucfirst(strtolower($text_part));
        }
        return implode(" ", $new_text_parts);
    }


    /**
     * naive phrase pluralizer works off the last word in the phrase ... mainly of use for ignoring adjectives, e.g.
     * e.g.
     *      \helper\strings::pluralize_phrase("confused dunce",2,true) = '2 confused dunces'
     *
     * !! don't expect this to work with anything complex !!
     *
     * @param string $phrase
     * @param int $count
     * @param bool $includeNumber
     * @return bool|string
     */
    public static function pluralizePhrase(string $phrase, int $count = 0, bool $includeNumber = false)
    {
        $phrase = rtrim($phrase);
        $lastSpaceIndex = strpos($phrase, " ");
        if ($lastSpaceIndex === false) {
            return self::pluralize($phrase, $count, $includeNumber);
        }
        $last_word = substr($phrase, $lastSpaceIndex + 1);
        $pluralize_last_word = self::pluralize($last_word, $count, false);

        return ($includeNumber ? $count : "") . " " .
            substr($phrase, 0, $lastSpaceIndex) . " " .
            $pluralize_last_word;
    }

    /**
     * simplistic pluralizer, handles sufficient edge cases for most situations, extended from the following:
     * @see http://www.kavoir.com/2011/04/php-class-converting-plural-to-singular-or-vice-versa-in-english.html
     *
     * @param string $singularWord
     * @param int $count optionally provide a count
     * @param bool $includeNumber
     *
     * @return bool|string
     */
    public static function pluralize(string $singularWord, int $count = 0, bool $includeNumber = false)
    {
        if ($includeNumber) {
            $singularWord = $count . ' ' . $singularWord;
        }

        if ($count == 1) {
            return $singularWord;
        }

        if (strlen($singularWord) == 0) {
            return $singularWord;
        }

        $lowercasedWord = strtolower($singularWord);

        $uncountables = [
            //general
            'aircraft',
            'equipment',
            'feedback',
            'graffiti',
            'hovercraft',
            'information',
            'money',
            'rice',
            'series',
            'species',
            'spacecraft',
            'barracks',
            'buffalo',
            'deer',
            'fish',
            'moose',
            'pike',
            'plankton',
            'salmon',
            'sheep',
            'squid',
            'swine',
            'trout'
        ];

        if (in_array($lowercasedWord, $uncountables)) {
            return $singularWord;
        }

        $irregular = [
            'appendix' => 'appendices',
            'atlas' => 'atlases',
            'beef' => 'beefs',
            'brief' => 'briefs',
            'cafe' => 'cafes',
            'corpus' => 'corpuses',
            'child' => 'children',
            'data' => 'data',
            'datum' => 'data',
            'foot' => 'feet',
            'gas' => 'gases',
            'genus' => 'genera',
            'goose' => 'geese',
            'graffito' => 'graffiti',
            'hero' => 'heroes',
            'is' => 'are',
            'loaf' => 'loaves',
            'man' => 'men',
            'move' => 'moves',
            'mythos' => 'mythoi',
            'numen' => 'numina',
            'occiput' => 'occiputs',
            'octopus' => 'octopuses',
            'opus' => 'opuses',
            'ox' => 'oxen',
            'penis' => 'penises',
            'person' => 'people',
            'potato' => 'potatoes',
            'quiz' => 'quizzes',
            'sex' => 'sexes',
            'tomato' => 'tomatoes',
            'tooth' => 'teeth',
            'trilby' => 'trilbys',
            'turf' => 'turfs',
            'woman' => 'women',
            'hive' => 'hives',
        ];

        if (isset($irregular[$lowercasedWord])) {
            return $irregular[$lowercasedWord];
        }

        $plural = [
            '/([m|l])ouse$/i' => '$1ice',
            '/(matr|vert|ind)ix|ex$/i' => '$1ices',
            '/(x|ch|ss|sh)$/i' => '$1es',
            '/([^aeiouy]|qu)ies$/i' => '$1y',
            '/([^aeiouy]|qu)y$/i' => '$1ies',
            '/(?:([^f])fe|([lre])f)$/i' => '$1$2ves',
            '/sis$/i' => 'ses',
            '/([ti])um$/i' => '$1a',
            '/(bu|viru)s$/i' => '$1ses',
            '/(gas|alias|status)/i' => '$1es',
            '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin)us$/i' => '$1i',
            '/(ax|cris|occiput)is$/i' => '$1es',
            '/(ox)$/i' => '$1es',
            '/s$/i' => 's',
            '/$/' => 's',
        ];

        foreach ($plural as $rule => $replacement) {
            if (preg_match($rule, $singularWord)) {
                return preg_replace($rule, $replacement, $singularWord);
            }
        }

        return $singularWord;
    }

    /**
     * perform a json_encode on an array/string which may not be in utf-8/ascii since (from php docs for json_encode):
     *   "All string data must be UTF-8 encoded."
     * note that this function uses ut8_encode which specifically "Encodes an ISO-8859-1 string to UTF-8"
     *
     * this method should be used as a drop in replacement for json_encode where there the possibility of "ISO-8859-1"
     * strings, an example would be values from the database containing accents.
     *
     * @param mixed $encode
     * @param int $options
     *
     * @return string
     * @throws \Exception
     */
    public static function jsonEncodeSafe($encode, $options = 0): string
    {
        if (is_array($encode)) {
            array_walk_recursive($encode, function (&$value) {
                if (is_string($value) && strings::detectEncoding($value) == self::ENCODING_ISO_8859_1) {
                    $value = utf8_encode($value);
                }
            });
        } else {
            if (is_string($encode)) {
                if (self::detectEncoding($encode) == self::ENCODING_ISO_8859_1) {
                    $encode = utf8_encode($encode);
                }
            } else {
                if (!is_scalar($encode)) {
                    throw new \Exception(__METHOD__ . " currently only works on arrays and scalars but type was " . gettype($encode));
                }
            }
        }
        return json_encode($encode, $options);
    }

    /**
     * the default mb_detect_encoding doesn't look for "ISO-8859-1" ... this isn't general enough to be a drop in
     * replacement but it does at least detect "ISO-8859-1"
     *
     * @param string $string
     *
     * @return string
     */
    public static function detectEncoding(string $string): string
    {
        return mb_detect_encoding($string,
            [
                self::ENCODING_ASCII,
                self::ENCODING_UTF_8,
                self::ENCODING_ISO_8859_1],
            true);
    }

    /**
     * @param int $digit
     * @param bool $ordinalVersion
     *
     * @return string
     * @throws \Exception
     */
    public static function getDigitAsWord(int $digit, bool $ordinalVersion = false): string
    {
        if (!is_numeric($digit) || strlen($digit) != 1) {
            throw new \Exception("method only takes a single digit as input");
        }
        switch ($digit) {
            case 0:
                if ($ordinalVersion) {
                    throw new \Exception("You can't have zeroth!!");
                }
                return "zero";// unsuitable for an ordered version
            case 1:
                return $ordinalVersion ? "first" : "one";
            case 2:
                return $ordinalVersion ? "second" : "two";
            case 3:
                return $ordinalVersion ? "third" : "three";
            case 4:
                return $ordinalVersion ? "fourth" : "four";
            case 5:
                return $ordinalVersion ? "fifth" : "five";
            case 6:
                return $ordinalVersion ? "sixth" : "six";
            case 7:
                return $ordinalVersion ? "seventh" : "seven";
            case 8:
                return $ordinalVersion ? "eighth" : "eight";
            case 9:
                return $ordinalVersion ? "ninth" : "nine";
            default:
                throw new \Exception("'" . $digit . "' is not a single digit");
        }
    }

    /**
     * @param int|string $integer
     * @param bool $useSup whether to surround the text bit with <sup> tag
     * @return string
     * @throws \Exception
     */
    public static function ordinaliseNumericInteger($integer, bool $useSup = false): string
    {
        if (((int)$integer == $integer) && $integer > 0) {
            $open_sup = $useSup ? "<sup>" : "";
            $close_sup = $useSup ? "</sup>" : "";
            $last_digit = substr("" . $integer, -1);
            if ($integer >= 10) {
                $last_two_digits = substr("" . $integer, -2);
                if ($last_two_digits == "10" || $last_two_digits == "11" || $last_two_digits == "12" || $last_two_digits == "13") {
                    return $integer . $open_sup . "th" . $close_sup;
                }
            }
            switch ($last_digit) {
                case "1":
                    return $integer . $open_sup . "st" . $close_sup;
                case "2":
                    return $integer . $open_sup . "nd" . $close_sup;
                case "3":
                    return $integer . $open_sup . "rd" . $close_sup;
                default:
                    return $integer . $open_sup . "th" . $close_sup;
            }
        } else {
            throw new \Exception("Passed value was not an integer");
        }
    }

    /**
     * @param int $index
     * @param bool $uppercase
     * @return string
     * @throws \Exception
     */
    public static function getNthLetterOfAlphabet(int $index, bool $uppercase = true): string
    {
        if ($index < 1) {
            throw new \Exception("valid range is > 1");
        }
        $char = '';
        if ($index > 26) {
            $char = self::getNthLetterOfAlphabet(floor($index / 26), $uppercase);
        }

        $char .= chr(($uppercase ? 64 : 96) + $index % 26);

        return $char;
    }

    /**
     * @param string $text
     * @return string
     * attempts to clean user entered text consistently by:
     *   1. converting <ol>s to <ul>s
     *   2. stripping all remaining tags except : <p><ul><li><strong><b><em>
     */
    public static function restrictHtmlFilter(string $text): string
    {
        return strip_tags(str_replace(["<ol>", "</ol>"], ["<ul>", "</ul>"], $text), "<p><ul><li><strong><b><em>");
    }

    /**
     * @param string $fromString
     * @param string $findString
     * @param int $count
     *
     * @return string
     */
    public static function getBefore(string $fromString, string $findString, int $count = 1): string
    {
        if (strlen($fromString) == 0) {
            return "";
        }
        $pos = 0;
        while ($count > 0 && (($pos = strpos($fromString, $findString, $pos + 1)) != false)) {
            $count--;
        }
        if ($count > 0) {
            return $fromString;
        }
        if ($pos === false) {
            return $fromString;
        }
        return substr($fromString, 0, $pos);
    }

    /**
     * adds string2 to string1 if string1 does not end with string2 (with a rudimentary check for regular plurals)
     *
     * @param string $string1
     * @param string $string2
     * @param string $separator
     * @return string
     */
    public static function addWithoutFinalWordRepeat(string $string1, string $string2, string $separator = ' '): string
    {
        if (self::endsWith($string1, $string2) || self::endsWith(rtrim($string1, "Ss"), $string2)) {
            return $string1;
        } else {
            return $string1 . $separator . $string2;
        }
    }

    /**
     * @param string $text
     * @param bool $includeTooltip
     * @param int $maximumLetters
     * @param int $minimumLetters
     *
     * @return string
     */
    public static function acronymise(string $text, bool $includeTooltip = true, int $maximumLetters = 10, int $minimumLetters = 2): string
    {
        $text = trim($text);
        if (strpos($text, " ") === false) {
            return $text;
        }
        preg_match_all('/\b[A-Z]\w+\b/', $text, $matches);
        $results = $matches[0];
        if (is_numeric($maximumLetters) && count($results) >= $maximumLetters) {
            return $text;
        }
        if (is_numeric($minimumLetters) && count($results) <= $minimumLetters) {
            return $text;
        }
        array_walk($results, function (&$str) {
            $str = $str[0] . ".";
        });
        return $includeTooltip ? "<span title='" . $text . "'>" . implode($results) . "</span>" : implode($results);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function prependIndefiniteArticle(string $string): string
    {
        $trimmed = trim($string);
        return (stristr('aeiou', $trimmed[0]) ? 'an ' : 'a ') . $trimmed;
    }

    /**
     * generate a period of time as a human readable string
     * @param int $ts
     * @return string
     */
    public static function readableTimePeriod(int $ts): string
    {
        $output = [];

        $uptime = abs($ts);
        $days = $uptime / 60 / 60 / 24;
        $day_fraction = $days - floor($days);
        $days = floor($days);
        if ($days > 0) {
            if ($days > 1) {
                $output[] = $days . ' days';
            } else {
                $output[] = $days . ' day';
            }
        }

        if ($day_fraction > 0) {
            $hours = 24 * $day_fraction;
            $hour_fraction = $hours - floor($hours);

            $hours = floor($hours);
            if ($hours > 1) {
                $output[] = $hours . ' hours';
            } else {
                $output[] = $hours . ' hour';
            }

            if ($hour_fraction > 0) {
                $minutes = floor(60 * $hour_fraction);

                if ($minutes > 1) {
                    $output[] = $minutes . ' minutes';
                } else {
                    $output[] = $minutes . ' minute';
                }
            }
        }

        return implode(' ', $output);
    }

    /**
     * @param float $amount
     * @param string $symbol
     *
     * @return string
     */
    public static function currencyFormat(float $amount, string $symbol = '&pound;'): string
    {
        return $symbol . money_format("%.2n", $amount);
    }

    /**
     * if you're using this then you're doing it wrong
     * http://stackoverflow.com/questions/574805/how-to-escape-strings-in-sql-server-using-php
     * @param string $data
     * @return string
     */
    public static function mssqlEscape(string $data)
    {
        if (is_numeric($data))
            return $data;
        $unpacked = unpack('H*hex', $data);
        return '0x' . $unpacked['hex'];
    }

    /**
     * @see https://github.com/eyecatchup/php-yt_downloader/blob/master/youtube-dl.class.php#L400
     *  Check if input string is a valid YouTube URL
     *  and try to extract the YouTube Video ID from it.
     * @param  string $youtube_url The string that shall be checked.
     * @return  string|bool Returns YouTube Video ID, or (boolean) false.
     */
    public static function extractYoutubeIdFromUrl(string $youtube_url)
    {
        $pattern = '#^(?:https?://|//)?(?:www\.|m\.)?(?:youtu\.be/|youtube\.com/(?:embed/|v/|watch\?v=|watch\?.+&v=))([\w-]{11})(?![\w-])#';
        preg_match($pattern, $youtube_url, $matches);
        return (isset($matches[1])) ? $matches[1] : false;
    }
}


