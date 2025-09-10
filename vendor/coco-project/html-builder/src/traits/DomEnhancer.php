<?php

    declare(strict_types = 1);

    namespace Coco\htmlBuilder\traits;

    use Coco\htmlBuilder\dom\DomBlock;
    use Coco\htmlBuilder\dom\DomSection;
    use Coco\htmlBuilder\dom\others\CSSCode;
    use Coco\htmlBuilder\dom\others\JSCode;
    use Coco\htmlBuilder\dom\tags\Link;
    use Coco\htmlBuilder\dom\tags\Meta;
    use Coco\htmlBuilder\dom\tags\Script;
    use Coco\htmlBuilder\dom\tags\Style;

trait DomEnhancer
{
    protected static array $valueMap = [];

    /**
     *head 中的自定义 js 代码对象
     *
     * @var JSCode|null $scriptSection
     */
    protected ?JSCode $scriptSection = null;

    /**
     *head 中的自定义css代码对象
     *
     * @var CSSCode|null $styleSection
     */
    protected ?CSSCode $styleSection = null;

    /**
     * head 中的js 链接
     *
     * @param string $link
     * @param bool   $isUnique
     *
     * @return $this
     */
    public function jsHead(string $link, bool $isUnique = true): static
    {
        $uniqueLabel = md5($link);
        if (!$isUnique || !isset(self::$valueMap[$uniqueLabel])) {
            self::$valueMap[$uniqueLabel] = 1;
            $this->appendRootSection('JS_HEAD', Script::ins()->link($link));
        }

        return $this;
    }

    /**
     * body 中的 js 链接
     *
     * @param string $link
     * @param bool   $isUnique
     *
     * @return $this
     */
    public function jsLib(string $link, bool $isUnique = true): static
    {
        $uniqueLabel = md5($link);
        if (!$isUnique || !isset(self::$valueMap[$uniqueLabel])) {
            self::$valueMap[$uniqueLabel] = 1;
            $this->appendRootSection('JS_LIB', Script::ins()->link($link));
        }

        return $this;
    }

    /**
     * body 中的 js 调用代码，适用于代码不需要替换变量的场景，可设定每次调用追加与否
     *
     *
     * @param string $codeWithoutScriptTag
     * @param bool   $isUnique
     *
     * @return $this
     */
    public function jsCustomRawCode(string $codeWithoutScriptTag, bool $isUnique = true): static
    {
        $uniqueLabel = md5($codeWithoutScriptTag);
        if (!$isUnique || !isset(self::$valueMap[$uniqueLabel])) {
            self::$valueMap[$uniqueLabel] = 1;
            $this->appendRootSection('JS_CUSTOM', Script::ins()->rawCode($codeWithoutScriptTag));
        }

        return $this;
    }

    /**
     * body 中的 js 调用代码，适用于复杂代码需要替换的场景，每次调用会追加一次
     *
     * @param DomBlock $section
     *
     * @return $this
     */
    public function jsCustomDomSection(DomBlock $section): static
    {
        $this->appendRootSection('JS_CUSTOM', $section);

        return $this;
    }

    /**
     * head 中的 css 调用代码，适用于复杂代码需要替换的场景，每次调用会追加一次
     *
     * @param DomBlock $section
     *
     * @return $this
     */
    public function cssCustomDomSection(DomBlock $section): static
    {
        $this->appendRootSection('CSS_CUSTOM', $section);

        return $this;
    }

    /**
     * 添加 meta 标签
     *
     * @return DomSection
     */
    public function metaKv(array $kvAttr): static
    {
        $this->appendRootSection('HEAD', Meta::ins()->addKvAttr($kvAttr));

        return $this;
    }


    /**
     * 添加 meta 标签
     *
     * <meta charset="utf-8" />
     *
     * @return DomSection
     */
    public function metaRaw(string $raw): static
    {
        $this->appendRootSection('HEAD', $raw);

        return $this;
    }


    /**
     * head 中的 css 链接
     *
     * @param string $link
     * @param bool   $isUnique
     *
     * @return $this
     */
    public function cssLib(string $link, bool $isUnique = true): static
    {
        $uniqueLabel = md5($link);
        if (!$isUnique || !isset(self::$valueMap[$uniqueLabel])) {
            self::$valueMap[$uniqueLabel] = 1;
            $this->appendRootSection('CSS_LIB', Link::ins($link));
        }

        return $this;
    }

    /**
     * head 中的自定义css代码
     *
     * @param string $codeWithoutStyleTag
     * @param bool   $isUnique
     *
     * @return $this
     */
    public function cssCustomRawCode(string $codeWithoutStyleTag, bool $isUnique = true): static
    {
        $uniqueLabel = md5($codeWithoutStyleTag);
        if (!$isUnique || !isset(self::$valueMap[$uniqueLabel])) {
            self::$valueMap[$uniqueLabel] = 1;
            $this->appendRootSection('CSS_CUSTOM', Style::ins($codeWithoutStyleTag));
        }

        return $this;
    }


    /**
     * 获取底部自定义的 js 模板对象
     *
     * @return JSCode|null
     */
    public function getScriptSection(): ?JSCode
    {
        return $this->scriptSection;
    }


    /**
     * 获取底部自定义的 js 模板对象
     *
     * @return CSSCode|null
     */
    public function getStyleSection(): ?CSSCode
    {
        return $this->styleSection;
    }

    /**
     * 构造器中自动初始化当前页面的js模板对象
     * 其中的逻辑应该是定义一个匿名类，继承 JSCode，然后把类赋值给 $this->scriptSection
     *
     * @return void
     */
    protected function makeScriptSection(): void
    {
    }

    /**
     * 构造器中自动初始化当前页面的css模板对象
     * 其中的逻辑应该是定义一个匿名类，继承 CSSCode，然后把类赋值给 $this->styleSection
     *
     * @return void
     */
    protected function makeStyleSection(): void
    {
    }

    /**
     * 构造器中自动初始化
     * 适用于调用 cssLib，jsLib
     *
     * @return void
     */
    protected function init(): void
    {
    }
}
