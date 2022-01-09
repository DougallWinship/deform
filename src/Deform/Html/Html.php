<?php
namespace Deform\Html;

/**
 * generate html in a consistent fashion using chaining. 
 *
 * Structural Tags:
 * @method static HtmlTag a(array $a=[])
 * @method static HtmlTag article(array $a=[])
 * @method static HtmlTag aside(array $a=[])
 * @method static HtmlTag body(array $a=[])
 * @method static HtmlTag br(array $a=[]) EMPTY TAG
 * @method static HtmlTag details(array $a=[])
 * @method static HtmlTag div(array $a=[])
 * @method static HtmlTag h1(array $a=[])
 * @method static HtmlTag h2(array $a=[])
 * @method static HtmlTag h3(array $a=[])
 * @method static HtmlTag h4(array $a=[])
 * @method static HtmlTag h5(array $a=[])
 * @method static HtmlTag h6(array $a=[])
 * @method static HtmlTag head(array $a=[])
 * @method static HtmlTag header(array $a=[])
 * @method static HtmlTag hgroup(array $a=[])
 * @method static HtmlTag hr(array $a=[]) EMPTY TAG
 * @method static HtmlTag html(array $a=[])
 * @method static HtmlTag footer(array $a=[])
 * @method static HtmlTag nav(array $a=[])
 * @method static HtmlTag p(array $a=[])
 * @method static HtmlTag section(array $a=[])
 * @method static HtmlTag span(array $a=[])
 * @method static HtmlTag summary(array $a=[])
 *
 * Metadata Tags:
 * @method static HtmlTag base(array $a=[]) EMPTY TAG
 * @method static HtmlTag link(array $a=[]) EMPTY TAG
 * @method static HtmlTag meta(array $a=[]) EMPTY TAG
 * @method static HtmlTag style(array $a=[])
 * @method static HtmlTag title(array $a=[])
 *
 * Form Tags:
 * @method static HtmlTag button(array $a=[])
 * @method static HtmlTag datalist(array $a=[])
 * @method static HtmlTag fieldset(array $a=[])
 * @method static HtmlTag form(array $a=[])
 * @method static HtmlTag input(array $a=[]) EMPTY TAG
 * @method static HtmlTag keygen(array $a=[])
 * @method static HtmlTag label(array $a=[])
 * @method static HtmlTag legend(array $a=[])
 * @method static HtmlTag meter(array $a=[])
 * @method static HtmlTag optgroup(array $a=[])
 * @method static HtmlTag option(array $a=[])
 * @method static HtmlTag select(array $a=[])
 * @method static HtmlTag textarea(array $a=[])
 *
 * Formatting Tags:
 * @method static HtmlTag abbr(array $a=[])
 * @method static HtmlTag address(array $a=[])
 * @method static HtmlTag b(array $a=[])
 * @method static HtmlTag bdi(array $a=[])
 * @method static HtmlTag bdo(array $a=[])
 * @method static HtmlTag blockquote(array $a=[])
 * @method static HtmlTag cite(array $a=[])
 * @method static HtmlTag code(array $a=[])
 * @method static HtmlTag del(array $a=[])
 * @method static HtmlTag dfn(array $a=[])
 * @method static HtmlTag em(array $a=[])
 * @method static HtmlTag i(array $a=[])
 * @method static HtmlTag ins(array $a=[])
 * @method static HtmlTag kbd(array $a=[])
 * @method static HtmlTag mark(array $a=[])
 * @method static HtmlTag output(array $a=[])
 * @method static HtmlTag pre(array $a=[])
 * @method static HtmlTag progress(array $a=[])
 * @method static HtmlTag q(array $a=[])
 * @method static HtmlTag rp(array $a=[])
 * @method static HtmlTag rt(array $a=[])
 * @method static HtmlTag ruby(array $a=[])
 * @method static HtmlTag samp(array $a=[])
 * @method static HtmlTag strong(array $a=[])
 * @method static HtmlTag sub(array $a=[])
 * @method static HtmlTag sup(array $a=[])
 * @method static HtmlTag tt(array $a=[])
 * @method static HtmlTag var(array $a=[])
 * @method static HtmlTag wbr(array $a=[])
 *
 * List Tags:
 * @method static HtmlTag dd(array $a=[])
 * @method static HtmlTag dl(array $a=[])
 * @method static HtmlTag dt(array $a=[])
 * @method static HtmlTag li(array $a=[])
 * @method static HtmlTag ol(array $a=[])
 * @method static HtmlTag menu(array $a=[])
 * @method static HtmlTag ul(array $a=[])
 *
 * Table Tags:
 * @method static HtmlTag captions(array $a=[])
 * @method static HtmlTag col(array $a=[])
 * @method static HtmlTag colgroup(array $a=[])
 * @method static HtmlTag table(array $a=[])
 * @method static HtmlTag tbody(array $a=[])
 * @method static HtmlTag td(array $a=[])
 * @method static HtmlTag tfoot(array $a=[])
 * @method static HtmlTag thead(array $a=[])
 * @method static HtmlTag th(array $a=[])
 * @method static HtmlTag tr(array $a=[])
 *
 * Scripting Tags:
 * @method static HtmlTag noscript(array $a=[])
 * @method static HtmlTag script(array $a=[])
 *
 * Embedded Content Tags:
 * @method static HtmlTag area(array $a=[]) EMPTY TAG
 * @method static HtmlTag audio(array $a=[])
 * @method static HtmlTag canvas(array $a=[])
 * @method static HtmlTag embed(array $a=[]) EMPTY TAG
 * @method static HtmlTag figcaption(array $a=[])
 * @method static HtmlTag figure(array $a=[])
 * @method static HtmlTag iframe(array $a=[]) EMPTY TAG
 * @method static HtmlTag img(array $a=[]) EMPTY TAG
 * @method static HtmlTag map(array $a=[])
 * @method static HtmlTag object(array $a=[])
 * @method static HtmlTag param(array $a=[])
 * @method static HtmlTag source(array $a=[])
 * @method static HtmlTag time(array $a=[])
 * @method static HtmlTag track(array $a=[])
 * @method static HtmlTag video(array $a=[])
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
    /** @var \ReflectionClass */
    private static $reflectionSelf;

    /** @var array */
    private static array $selfClosingTags=[];

    /** @var array */
    private static array $standardTags=[];

    /**
     * @param $tag string
     * @param $arguments mixed
     * @return IHtml
     * @throws \Exception
     */
    public static function __callStatic(string $tag, $arguments) : IHtml
    {
        self::identifyTags();

        $isSelfClosing = in_array($tag,self::$selfClosingTags);
        $isStandard = in_array($tag,self::$standardTags);
        if (!$isSelfClosing && !$isStandard) {
            throw new \Exception("Undocumented tag '".$tag."' please add a static method definition to the class annotations");
        }

        if ((isset($arguments[0]) && !is_array($arguments[0])) || isset($arguments[1])) {
            throw new \Exception("Html::".$tag."(....) expects either no arguments or a single array of attributes");
        }
        $attributes = $arguments[0] ?? [];
        $tag = strtolower($tag);
        return new HtmlTag($tag, $attributes);
    }

    private static function identifyTags()
    {
        if (self::$reflectionSelf===null) {
            self::$reflectionSelf = new \ReflectionClass(self::class);
            $comments = explode(PHP_EOL, self::$reflectionSelf->getDocComment());
            array_walk($comments, [self::class,'registerStaticMethodSignatures']);
        }
    }

    /**
     * @param string $comment
     * @throws \Exception
     */
    private static function registerStaticMethodSignatures(string $comment)
    {
        $trimmed = ltrim(trim(preg_replace('/\s+/', ' ', $comment)),'* ');
        $parts = explode(" ",$trimmed);
        if (count($parts)>=4 && $parts[0]==='@method' && $parts[1]==='static') {
            $result = preg_match('/(\S+)\s+(\S+)\s+(\S+)\s+(.*?)\((.*?)\)(\s*)(\S*)/', $trimmed, $matches);
            if (!$result) throw new \Exception("Failed to parse annotation : ".$trimmed);
            list($discardAll,$discardMethod,$discardStatic,$className,$methodName,$params,$discardWhitespace,$isEmpty) = $matches;
            if ($className!=='HtmlTag') {
                throw new \Exception("Unexpected class '".$className."' in annotations : ".$trimmed);
            }
            if (strtolower(trim($isEmpty))=='empty') {
                self::$selfClosingTags[] = $methodName;
            }
            else {
                self::$standardTags[] = $methodName;
            }
        }
    }

    public static function isSelfClosedTag($tag)
    {
        self::identifyTags();
        return in_array($tag, self::$selfClosingTags);
    }

    public static function isStandardTag($tag)
    {
        self::identifyTags();
        return in_array($tag, self::$standardTags);
    }

    public static function isRegisteredTag($tag)
    {
        return self::isSelfClosedTag($tag) || self::isStandardTag($tag);
    }
}