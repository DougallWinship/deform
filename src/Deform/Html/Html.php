<?php

declare(strict_types=1);

namespace Deform\Html;

use Deform\Util\Strings;

/**
 * generate html in a consistent fashion using chaining.
 *
 * Structural Tags:
 * @method static HtmlTag a(array $attributes=[])
 * @method static HtmlTag article(array $attributes=[])
 * @method static HtmlTag aside(array $attributes=[])
 * @method static HtmlTag body(array $attributes=[])
 * @method static HtmlTag br(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag details(array $attributes=[])
 * @method static HtmlTag div(array $attributes=[])
 * @method static HtmlTag h1(array $attributes=[])
 * @method static HtmlTag h2(array $attributes=[])
 * @method static HtmlTag h3(array $attributes=[])
 * @method static HtmlTag h4(array $attributes=[])
 * @method static HtmlTag h5(array $attributes=[])
 * @method static HtmlTag h6(array $attributes=[])
 * @method static HtmlTag head(array $attributes=[])
 * @method static HtmlTag header(array $attributes=[])
 * @method static HtmlTag hgroup(array $attributes=[])
 * @method static HtmlTag hr(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag html(array $attributes=[])
 * @method static HtmlTag footer(array $attributes=[])
 * @method static HtmlTag nav(array $attributes=[])
 * @method static HtmlTag p(array $attributes=[])
 * @method static HtmlTag section(array $attributes=[])
 * @method static HtmlTag span(array $attributes=[])
 * @method static HtmlTag summary(array $attributes=[])
 *
 * Metadata Tags:
 * @method static HtmlTag base(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag link(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag meta(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag style(array $attributes=[])
 * @method static HtmlTag title(array $attributes=[])
 *
 * Form Tags:
 * @method static HtmlTag button(array $attributes=[])
 * @method static HtmlTag datalist(array $attributes=[])
 * @method static HtmlTag fieldset(array $attributes=[])
 * @method static HtmlTag form(array $attributes=[])
 * @method static HtmlTag input(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag keygen(array $attributes=[])
 * @method static HtmlTag label(array $attributes=[])
 * @method static HtmlTag legend(array $attributes=[])
 * @method static HtmlTag meter(array $attributes=[])
 * @method static HtmlTag optgroup(array $attributes=[])
 * @method static HtmlTag option(array $attributes=[])
 * @method static HtmlTag select(array $attributes=[])
 * @method static HtmlTag textarea(array $attributes=[])
 *
 * Formatting Tags:
 * @method static HtmlTag abbr(array $attributes=[])
 * @method static HtmlTag address(array $attributes=[])
 * @method static HtmlTag b(array $attributes=[])
 * @method static HtmlTag bdi(array $attributes=[])
 * @method static HtmlTag bdo(array $attributes=[])
 * @method static HtmlTag blockquote(array $attributes=[])
 * @method static HtmlTag cite(array $attributes=[])
 * @method static HtmlTag code(array $attributes=[])
 * @method static HtmlTag del(array $attributes=[])
 * @method static HtmlTag dfn(array $attributes=[])
 * @method static HtmlTag em(array $attributes=[])
 * @method static HtmlTag i(array $attributes=[])
 * @method static HtmlTag ins(array $attributes=[])
 * @method static HtmlTag kbd(array $attributes=[])
 * @method static HtmlTag mark(array $attributes=[])
 * @method static HtmlTag output(array $attributes=[])
 * @method static HtmlTag pre(array $attributes=[])
 * @method static HtmlTag progress(array $attributes=[])
 * @method static HtmlTag q(array $attributes=[])
 * @method static HtmlTag rp(array $attributes=[])
 * @method static HtmlTag rt(array $attributes=[])
 * @method static HtmlTag ruby(array $attributes=[])
 * @method static HtmlTag samp(array $attributes=[])
 * @method static HtmlTag strong(array $attributes=[])
 * @method static HtmlTag sub(array $attributes=[])
 * @method static HtmlTag sup(array $attributes=[])
 * @method static HtmlTag tt(array $attributes=[])
 * @method static HtmlTag var(array $attributes=[])
 * @method static HtmlTag wbr(array $attributes=[])
 *
 * List Tags:
 * @method static HtmlTag dd(array $attributes=[])
 * @method static HtmlTag dl(array $attributes=[])
 * @method static HtmlTag dt(array $attributes=[])
 * @method static HtmlTag li(array $attributes=[])
 * @method static HtmlTag ol(array $attributes=[])
 * @method static HtmlTag menu(array $attributes=[])
 * @method static HtmlTag ul(array $attributes=[])
 *
 * Table Tags:
 * @method static HtmlTag captions(array $attributes=[])
 * @method static HtmlTag col(array $attributes=[])
 * @method static HtmlTag colgroup(array $attributes=[])
 * @method static HtmlTag table(array $attributes=[])
 * @method static HtmlTag tbody(array $attributes=[])
 * @method static HtmlTag td(array $attributes=[])
 * @method static HtmlTag tfoot(array $attributes=[])
 * @method static HtmlTag thead(array $attributes=[])
 * @method static HtmlTag th(array $attributes=[])
 * @method static HtmlTag tr(array $attributes=[])
 *
 * Scripting Tags:
 * @method static HtmlTag noscript(array $attributes=[])
 * @method static HtmlTag script(array $attributes=[])
 *
 * Embedded Content Tags:
 * @method static HtmlTag area(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag audio(array $attributes=[])
 * @method static HtmlTag canvas(array $attributes=[])
 * @method static HtmlTag embed(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag figcaption(array $attributes=[])
 * @method static HtmlTag figure(array $attributes=[])
 * @method static HtmlTag iframe(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag img(array $attributes=[]) EMPTY TAG
 * @method static HtmlTag map(array $attributes=[])
 * @method static HtmlTag object(array $attributes=[])
 * @method static HtmlTag param(array $attributes=[])
 * @method static HtmlTag source(array $attributes=[])
 * @method static HtmlTag time(array $attributes=[])
 * @method static HtmlTag track(array $attributes=[])
 * @method static HtmlTag video(array $attributes=[])
 *
 * Notes:
 * - the purpose is to allow code generated strings of HTML which can still be
 *   altered just prior to rendering (so a controller can make it and a view
 *   customise it)
 * - as this is a string generation tool and NOT a DOM representation tool any
 *   HTML specifics such as class constants are a mere convenience
 * - if you want to parse/generate implicitly valid html use PHP's DOM library
 *   or a suitable 3rd party equivalent
 * - it's explicitly intentional to avoid any sort of DOM selection at this stage
 *   ... if you need more granularity generate it upstream!
 *
 */
class Html
{
    /** @var \ReflectionClass|null */
    private static ?\ReflectionClass $reflectionSelf = null;

    /** @var array */
    private static array $selfClosingTags = [];

    /** @var array */
    private static array $standardTags = [];

    /**
     * @param $tag string
     * @param $arguments mixed
     * @return IHtml
     * @throws \Exception
     */
    public static function __callStatic(string $tag, $arguments): IHtml
    {
        self::identifyTags();

        $isSelfClosing = in_array($tag, self::$selfClosingTags);
        $isStandard = in_array($tag, self::$standardTags);
        if (!$isSelfClosing && !$isStandard) {
            throw new \Exception(
                "Undocumented tag '" . $tag . "' please add a static method definition to the class annotations"
            );
        }

        if ((isset($arguments[0]) && !is_array($arguments[0])) || isset($arguments[1])) {
            throw new \Exception(
                "Html::" . $tag . "(....) expects either no arguments or a single array of attributes"
            );
        }
        $attributes = $arguments[0] ?? [];
        $tag = strtolower($tag);
        return new HtmlTag($tag, $attributes);
    }

    /**
     * @throws \Exception
     */
    private static function identifyTags()
    {
        if (self::$reflectionSelf === null) {
            self::$reflectionSelf = new \ReflectionClass(self::class);
            $comments = explode(PHP_EOL, self::$reflectionSelf->getDocComment());
            array_walk($comments, function ($comment) {
                $signature = Strings::extractStaticMethodSignature($comment);
                if (is_array($signature)) {
                    if (
                        isset($signature['comment_parts'][0]) &&
                        strtolower($signature['comment_parts'][0]) === 'empty'
                    ) {
                        self::$selfClosingTags[] = $signature['methodName'];
                    } else {
                        self::$standardTags[] = $signature['methodName'];
                    }
                }
            });
        }
    }

    /**
     * @param string $tag
     * @return bool
     * @throws \Exception
     */
    public static function isSelfClosedTag(string $tag): bool
    {
        self::identifyTags();
        return in_array($tag, self::$selfClosingTags);
    }

    /**
     * @param string $tag
     * @return bool
     * @throws \Exception
     */
    public static function isStandardTag(string $tag): bool
    {
        self::identifyTags();
        return in_array($tag, self::$standardTags);
    }

    /**
     * @param string $tag
     * @return bool
     * @throws \Exception
     */
    public static function isRegisteredTag(string $tag): bool
    {
        return self::isSelfClosedTag($tag) || self::isStandardTag($tag);
    }
}
